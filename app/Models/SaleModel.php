<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    
    // Sesuaikan dengan kolom di Migration
    protected $allowedFields    = [
        'invoice_number', 
        'customer_id', 
        'user_id', 
        'sale_date', 
        'total_gross', 
        'discount_total', 
        'total_net', 
        'payment_status'
    ];

    // Dates
    protected $useTimestamps = false; // Di migrasi Anda tidak ada created_at untuk tabel ini

    /**
     * Mendapatkan detail penjualan beserta item-itemnya
     */
    public function getSaleDetail($id)
    {
        $sale = $this->select('sales.*, customers.customer_name, users.full_name as cashier_name')
                     ->join('customers', 'customers.id = sales.customer_id', 'left')
                     ->join('users', 'users.id = sales.user_id', 'left')
                     ->where('sales.id', $id)
                     ->first();

        if ($sale) {
            $sale['items'] = $this->db->table('sale_items')
                ->select('sale_items.*, products.product_name, products.sku')
                ->join('products', 'products.id = sale_items.product_id')
                ->where('sale_id', $id)
                ->get()
                ->getResultArray();
        }

        return $sale;
    }

    /**
     * Rule Validasi Sederhana
     */
    protected $validationRules = [
        'invoice_number' => 'required|is_unique[sales.invoice_number,id,{id}]',
        'total_net'      => 'required|decimal',
        'payment_status' => 'required|in_list[Unpaid,Paid,Partial]',
    ];
}