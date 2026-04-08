<?php

namespace App\Http\Controllers\FileUpload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Secure File Upload Implementation
 *
 * Demonstrates proper file upload security:
 * 1. Whitelist extension validation
 * 2. Magic bytes (file content) validation
 * 3. Random filename generation
 * 4. Storage outside public directory
 * 5. Secure file serving with authorization
 */
class SecureUploadController extends Controller
{
    /**
     * Allowed file extensions (whitelist)
     */
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Allowed MIME types based on file content
     */
    private array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Maximum file size in bytes (5MB)
     */
    private int $maxFileSize = 5 * 1024 * 1024;

    /**
     * Show the secure upload demo page
     */
    public function index(): View
    {
        $uploadedFiles = $this->getUploadedFiles();

        return view('file-upload-lab.secure.index', [
            'uploadedFiles' => $uploadedFiles,
            'allowedExtensions' => $this->allowedExtensions,
            'maxFileSize' => $this->formatBytes($this->maxFileSize),
        ]);
    }

    /**
     * Handle secure file upload
     */
    public function upload(Request $request): \Illuminate\Http\RedirectResponse
    {
        // 1. Basic Laravel validation
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.($this->maxFileSize / 1024), // KB for Laravel
            ],
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'File size exceeds the maximum limit of '.$this->formatBytes($this->maxFileSize),
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();

        // 2. Validate extension (whitelist, case-insensitive)
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, $this->allowedExtensions)) {
            Log::channel('daily')->warning('Secure upload: Invalid extension rejected', [
                'original_name' => $originalName,
                'extension' => $extension,
                'user_ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'file' => "Invalid file extension: .{$extension}. Allowed: ".implode(', ', $this->allowedExtensions),
            ]);
        }

        // 3. Validate MIME type from file content (magic bytes)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($file->getPathname());

        if (! in_array($detectedMime, $this->allowedMimeTypes)) {
            Log::channel('daily')->warning('Secure upload: Invalid MIME type rejected', [
                'original_name' => $originalName,
                'detected_mime' => $detectedMime,
                'user_ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'file' => "Invalid file content. Detected type: {$detectedMime}",
            ]);
        }

        // 4. Additional image validation (optional but recommended)
        if (! $this->isValidImage($file->getPathname())) {
            Log::channel('daily')->warning('Secure upload: Image validation failed', [
                'original_name' => $originalName,
                'user_ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'file' => 'File appears to be corrupted or not a valid image.',
            ]);
        }

        // 5. Generate random filename (prevents path traversal and overwrites)
        $randomName = Str::uuid().'.'.$extension;

        // 6. Store OUTSIDE public directory (in storage/app/private)
        $path = $file->storeAs('secure-uploads', $randomName, 'local');

        // 7. Store metadata (in real app, save to database)
        $this->storeMetadata($randomName, $originalName, $detectedMime, $file->getSize());

        // 8. Log successful upload
        Log::channel('daily')->info('Secure upload: File uploaded successfully', [
            'original_name' => $originalName,
            'stored_as' => $randomName,
            'mime_type' => $detectedMime,
            'size' => $file->getSize(),
            'user_ip' => $request->ip(),
        ]);

        return back()->with('success', "File uploaded securely! Stored as: {$randomName}");
    }

    /**
     * Serve file securely (with authorization check)
     */
    public function serve(string $filename): BinaryFileResponse|Response
    {
        // 1. Validate filename format (prevent path traversal)
        if (! preg_match('/^[a-f0-9\-]+\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            Log::channel('daily')->warning('Secure serve: Invalid filename format', [
                'filename' => $filename,
                'user_ip' => request()->ip(),
            ]);
            abort(400, 'Invalid filename format');
        }

        // 2. Check if file exists
        $path = storage_path('app/secure-uploads/'.$filename);
        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        // 3. In real app: Check authorization (user owns file, has permission, etc.)
        // $this->authorize('view', $file);

        // 4. Get MIME type for response
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($path);

        // 5. Log file access
        Log::channel('daily')->info('Secure serve: File accessed', [
            'filename' => $filename,
            'user_ip' => request()->ip(),
        ]);

        // 6. Return file with security headers
        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'X-Content-Type-Options' => 'nosniff', // Prevent MIME sniffing
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'",
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Download file securely
     */
    public function download(string $filename): BinaryFileResponse|Response
    {
        // Same validation as serve
        if (! preg_match('/^[a-f0-9\-]+\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            abort(400, 'Invalid filename format');
        }

        $path = storage_path('app/secure-uploads/'.$filename);
        if (! file_exists($path)) {
            abort(404, 'File not found');
        }

        // Get original filename from metadata
        $metadata = $this->getMetadata($filename);
        $downloadName = $metadata['original_name'] ?? $filename;

        Log::channel('daily')->info('Secure download: File downloaded', [
            'filename' => $filename,
            'user_ip' => request()->ip(),
        ]);

        return response()->download($path, $downloadName, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Delete a file
     */
    public function delete(string $filename): \Illuminate\Http\RedirectResponse
    {
        if (! preg_match('/^[a-f0-9\-]+\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            abort(400, 'Invalid filename format');
        }

        $path = storage_path('app/secure-uploads/'.$filename);

        if (file_exists($path)) {
            unlink($path);
            $this->removeMetadata($filename);

            Log::channel('daily')->info('Secure upload: File deleted', [
                'filename' => $filename,
                'user_ip' => request()->ip(),
            ]);

            return back()->with('success', 'File deleted successfully!');
        }

        return back()->withErrors(['file' => 'File not found']);
    }

    /**
     * Clear all secure uploads
     */
    public function clearAll(): \Illuminate\Http\RedirectResponse
    {
        $uploadDir = storage_path('app/secure-uploads');

        if (is_dir($uploadDir)) {
            $files = glob($uploadDir.'/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    unlink($file);
                }
            }
        }

        // Clear metadata
        $metadataFile = storage_path('app/secure-uploads-metadata.json');
        if (file_exists($metadataFile)) {
            unlink($metadataFile);
        }

        Log::channel('daily')->warning('Secure uploads: All files cleared', [
            'user_ip' => request()->ip(),
        ]);

        return back()->with('success', 'All files have been cleared!');
    }

    /**
     * Validate image using GD library
     */
    private function isValidImage(string $path): bool
    {
        try {
            $imageInfo = @getimagesize($path);

            return $imageInfo !== false && $imageInfo[0] > 0 && $imageInfo[1] > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Store file metadata (simplified - in real app use database)
     */
    private function storeMetadata(string $storedName, string $originalName, string $mimeType, int $size): void
    {
        $metadataFile = storage_path('app/secure-uploads-metadata.json');
        $metadata = [];

        if (file_exists($metadataFile)) {
            $metadata = json_decode(file_get_contents($metadataFile), true) ?? [];
        }

        $metadata[$storedName] = [
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'size' => $size,
            'uploaded_at' => now()->toDateTimeString(),
        ];

        file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    }

    /**
     * Get file metadata
     */
    private function getMetadata(string $storedName): array
    {
        $metadataFile = storage_path('app/secure-uploads-metadata.json');

        if (file_exists($metadataFile)) {
            $metadata = json_decode(file_get_contents($metadataFile), true) ?? [];

            return $metadata[$storedName] ?? [];
        }

        return [];
    }

    /**
     * Remove metadata for a file
     */
    private function removeMetadata(string $storedName): void
    {
        $metadataFile = storage_path('app/secure-uploads-metadata.json');

        if (file_exists($metadataFile)) {
            $metadata = json_decode(file_get_contents($metadataFile), true) ?? [];
            unset($metadata[$storedName]);
            file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Get list of uploaded files with metadata
     */
    private function getUploadedFiles(): array
    {
        $uploadDir = storage_path('app/secure-uploads');
        $files = [];

        if (is_dir($uploadDir)) {
            $fileList = glob($uploadDir.'/*');
            foreach ($fileList as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    $filename = basename($file);
                    $metadata = $this->getMetadata($filename);

                    $files[] = [
                        'stored_name' => $filename,
                        'original_name' => $metadata['original_name'] ?? $filename,
                        'mime_type' => $metadata['mime_type'] ?? 'unknown',
                        'size' => $metadata['size'] ?? filesize($file),
                        'uploaded_at' => $metadata['uploaded_at'] ?? date('Y-m-d H:i:s', filemtime($file)),
                    ];
                }
            }
        }

        return $files;
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
