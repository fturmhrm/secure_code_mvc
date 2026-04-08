<?php

namespace App\Http\Controllers\FileUpload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * EDUCATIONAL PURPOSE ONLY!
 *
 * Controller ini SENGAJA dibuat vulnerable untuk demonstrasi.
 * JANGAN gunakan pattern ini di production!
 */
class VulnerableUploadController extends Controller
{
    /**
     * Show the vulnerable lab index
     */
    public function index(): View
    {
        return view('file-upload-lab.lab.index');
    }

    /**
     * Level 1: No validation at all (RCE possible)
     * Vulnerability: Accepts any file, stores in public directory
     */
    public function level1(Request $request): View
    {
        $message = null;
        $uploadedFile = null;
        $fileUrl = null;

        if ($request->isMethod('post') && $request->hasFile('file')) {
            $file = $request->file('file');

            // VULNERABLE: No validation, store in public!
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/vulnerable'), $filename);

            $uploadedFile = $filename;
            $fileUrl = url('uploads/vulnerable/'.$filename);
            $message = 'File uploaded successfully!';

            Log::channel('daily')->info('Level 1: Vulnerable upload', [
                'filename' => $filename,
                'user_ip' => $request->ip(),
            ]);
        }

        return view('file-upload-lab.lab.level1', compact('message', 'uploadedFile', 'fileUrl'));
    }

    /**
     * Level 2: Client-side validation only
     * Vulnerability: JavaScript validation can be bypassed
     */
    public function level2(Request $request): View
    {
        $message = null;
        $uploadedFile = null;
        $fileUrl = null;

        if ($request->isMethod('post') && $request->hasFile('file')) {
            $file = $request->file('file');

            // VULNERABLE: Still no server-side validation!
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/vulnerable'), $filename);

            $uploadedFile = $filename;
            $fileUrl = url('uploads/vulnerable/'.$filename);
            $message = 'File uploaded successfully!';

            Log::channel('daily')->info('Level 2: Client-side bypass upload', [
                'filename' => $filename,
                'user_ip' => $request->ip(),
            ]);
        }

        return view('file-upload-lab.lab.level2', compact('message', 'uploadedFile', 'fileUrl'));
    }

    /**
     * Level 3: Blacklist validation (bypassable)
     * Vulnerability: Blacklist can be bypassed with alternative extensions
     */
    public function level3(Request $request): View
    {
        $message = null;
        $uploadedFile = null;
        $fileUrl = null;
        $error = null;

        // VULNERABLE: Incomplete blacklist, case-sensitive
        $blacklist = ['php', 'exe', 'sh', 'bat'];

        if ($request->isMethod('post') && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            // VULNERABLE: Only checks exact match, case-sensitive
            if (in_array($extension, $blacklist)) {
                $error = 'File type not allowed!';
            } else {
                $file->move(public_path('uploads/vulnerable'), $filename);
                $uploadedFile = $filename;
                $fileUrl = url('uploads/vulnerable/'.$filename);
                $message = 'File uploaded successfully!';
            }

            Log::channel('daily')->info('Level 3: Blacklist bypass attempt', [
                'filename' => $filename,
                'extension' => $extension,
                'blocked' => $error !== null,
                'user_ip' => $request->ip(),
            ]);
        }

        return view('file-upload-lab.lab.level3', compact('message', 'uploadedFile', 'fileUrl', 'error', 'blacklist'));
    }

    /**
     * Level 4: MIME type validation only
     * Vulnerability: Content-Type header can be spoofed
     */
    public function level4(Request $request): View
    {
        $message = null;
        $uploadedFile = null;
        $fileUrl = null;
        $error = null;

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if ($request->isMethod('post') && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            // VULNERABLE: Trusts Content-Type header from request!
            // getClientMimeType() reads the Content-Type header sent by client
            // NOT the actual file content - this is spoofable!
            $mimeType = $file->getClientMimeType();

            if (! in_array($mimeType, $allowedMimeTypes)) {
                $error = "Invalid MIME type: {$mimeType}. Only images allowed!";
            } else {
                $file->move(public_path('uploads/vulnerable'), $filename);
                $uploadedFile = $filename;
                $fileUrl = url('uploads/vulnerable/'.$filename);
                $message = 'File uploaded successfully!';
            }

            Log::channel('daily')->info('Level 4: MIME type bypass attempt', [
                'filename' => $filename,
                'mime_type' => $mimeType,
                'blocked' => $error !== null,
                'user_ip' => $request->ip(),
            ]);
        }

        return view('file-upload-lab.lab.level4', compact('message', 'uploadedFile', 'fileUrl', 'error', 'allowedMimeTypes'));
    }

    /**
     * Level 5: Magic bytes check (harder but still bypassable)
     * Vulnerability: Polyglot files can have valid image header + PHP code
     */
    public function level5(Request $request): View
    {
        $message = null;
        $uploadedFile = null;
        $fileUrl = null;
        $error = null;
        $detectedMime = null;

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];

        if ($request->isMethod('post') && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            // Better: Check magic bytes from file content
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->file($file->getPathname());

            if (! in_array($detectedMime, $allowedMimeTypes)) {
                $error = "Invalid file content! Detected: {$detectedMime}";
            } else {
                // STILL VULNERABLE: Preserves original filename with extension
                // Polyglot files can pass magic bytes check but still execute as PHP
                $file->move(public_path('uploads/vulnerable'), $filename);
                $uploadedFile = $filename;
                $fileUrl = url('uploads/vulnerable/'.$filename);
                $message = 'File uploaded successfully!';
            }

            Log::channel('daily')->info('Level 5: Magic bytes bypass attempt', [
                'filename' => $filename,
                'detected_mime' => $detectedMime,
                'blocked' => $error !== null,
                'user_ip' => $request->ip(),
            ]);
        }

        return view('file-upload-lab.lab.level5', compact('message', 'uploadedFile', 'fileUrl', 'error', 'allowedMimeTypes', 'detectedMime'));
    }

    /**
     * Clear all uploaded vulnerable files
     */
    public function clearUploads(): \Illuminate\Http\RedirectResponse
    {
        $uploadDir = public_path('uploads/vulnerable');

        if (is_dir($uploadDir)) {
            $files = glob($uploadDir.'/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        Log::channel('daily')->warning('Vulnerable uploads cleared', [
            'user_ip' => request()->ip(),
        ]);

        return back()->with('success', 'All uploaded files have been cleared!');
    }

    /**
     * List all files in vulnerable uploads directory
     */
    public function listFiles(): View
    {
        $uploadDir = public_path('uploads/vulnerable');
        $files = [];

        if (is_dir($uploadDir)) {
            $fileList = glob($uploadDir.'/*');
            foreach ($fileList as $file) {
                if (is_file($file)) {
                    $files[] = [
                        'name' => basename($file),
                        'size' => filesize($file),
                        'modified' => filemtime($file),
                        'url' => url('uploads/vulnerable/'.basename($file)),
                    ];
                }
            }
        }

        return view('file-upload-lab.lab.files', compact('files'));
    }
}
