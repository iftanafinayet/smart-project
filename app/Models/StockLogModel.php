<?php

namespace App\Models;

use CodeIgniter\Model;

class StockLogModel extends Model
{
    protected $table            = 'stock_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['product_id', 'type', 'qty', 'reference_no', 'created_at'];

    // Query untuk join dengan tabel produk agar mendapatkan nama barang
    public function getLogsWithProduct()
    {
        return $this->select('stock_logs.*, products.product_name, products.sku')
                    ->join('products', 'products.id = stock_logs.product_id')
                    ->orderBy('stock_logs.created_at', 'DESC')
                    ->findAll();
    }
}