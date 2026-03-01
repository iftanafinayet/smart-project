<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    // Sesuaikan dengan gambar struktur tabel:
    protected $allowedFields    = [
        'category_id', 
        'sku', 
        'product_name', 
        'unit', 
        'purchase_price', 
        'selling_price', 
        'current_stock', 
        'min_stock'
    ];
    protected $useTimestamps    = false; // Berdasarkan gambar, tidak ada kolom created_at/updated_at
}