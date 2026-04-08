<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model SqliLabProduct
 *
 * Digunakan untuk demo secure search dengan Eloquent ORM.
 * Eloquent secara otomatis menggunakan prepared statements
 * sehingga aman dari SQL Injection.
 */
class SqliLabProduct extends Model
{
    /**
     * Nama tabel
     */
    protected $table = 'sqli_lab_products';

    /**
     * Kolom yang bisa di-fill secara mass assignment
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'stock',
    ];

    /**
     * Casting tipe data
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Scope untuk pencarian
     *
     * SECURE: Menggunakan parameter binding otomatis
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', '%'.$term.'%')
            ->orWhere('description', 'LIKE', '%'.$term.'%');
    }

    /**
     * Scope untuk filter harga
     *
     * SECURE: Eloquent scope dengan parameter
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope untuk stok tersedia
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Format harga sebagai Rupiah
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}
