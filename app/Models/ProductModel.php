<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'category_id', 'sku', 'product_name', 'unit', 
        'purchase_price', 'selling_price', 'current_stock', 'min_stock'
    ];
}