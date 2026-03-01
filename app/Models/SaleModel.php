<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // Sesuaikan allowedFields dengan struktur tabel baru
protected $allowedFields    = ['invoice_number', 'customer_id', 'user_id', 'sale_date', 'total_gross', 'discount_total', 'total_net', 'payment_status'];
    

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}