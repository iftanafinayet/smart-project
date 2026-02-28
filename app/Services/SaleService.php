<?php

namespace App\Services;

use App\Models\SaleModel;
use App\Models\SaleItemModel;
use App\Models\ProductModel;
use App\Models\FinanceTransactionModel; // Tambahkan ini
use Config\Database;

class SaleService
{
    public function getAllSales()
    {
        $model = new SaleModel();
        return $model->select('sales.*, customers.customer_name')
                     ->join('customers', 'customers.id = sales.customer_id', 'left')
                     ->findAll();
    }

    public function processSale($data)
{
    $db = Database::connect();
    
    // Aktifkan debug agar menampilkan error saat terjadi kegagalan query
    // $db->transException(true); // Opsi 1: Lempar Exception

    $db->transStart();

    $saleModel = new SaleModel();
    $itemModel = new SaleItemModel();
    $productModel = new ProductModel();
    $financeModel = new FinanceTransactionModel();

    // 1. Simpan Header Penjualan
    $saleId = $saleModel->insert($data, true);
    
    // --- TAMBAHKAN INI UNTUK DEBUG ---
    if (!$saleId) {
        log_message('error', 'SaleModel Error: ' . json_encode($saleModel->errors()));
        $db->transRollback();
        return false;
    }
    // ---------------------------------

    // 2. Simpan Items & 3. Potong Stok
    if (isset($data['items'])) {
        foreach ($data['items'] as $item) {
            $item['sale_id'] = $saleId;
            $itemModel->insert($item);
            
            // --- TAMBAHKAN INI UNTUK DEBUG ---
            if ($itemModel->errors()) {
                log_message('error', 'SaleItemModel Error: ' . json_encode($itemModel->errors()));
                $db->transRollback();
                return false;
            }
            // ---------------------------------

            // Potong Stok
            $productModel->where('id', $item['product_id'])
                         ->set('current_stock', "current_stock - {$item['qty']}", false)
                         ->update();
                         
            // --- TAMBAHKAN INI UNTUK DEBUG ---
            if ($productModel->errors()) {
                log_message('error', 'ProductModel Error: ' . json_encode($productModel->errors()));
                $db->transRollback();
                return false;
            }
            // ---------------------------------
        }
    }

    // 4. INTEGRASI FINANCE
    if (isset($data['payment_status']) && $data['payment_status'] == 'Paid') {
        $financeModel->insert([
            'type'             => 'Income',
            'amount'           => $data['total_net'], 
            'description'      => 'Pendapatan Penjualan - Invoice: ' . $data['invoice_number'],
            'transaction_date' => date('Y-m-d H:i:s'),
            'reference_no'     => $data['invoice_number']
        ]);
        
        // --- TAMBAHKAN INI UNTUK DEBUG ---
        if ($financeModel->errors()) {
            log_message('error', 'FinanceModel Error: ' . json_encode($financeModel->errors()));
            $db->transRollback();
            return false;
        }
        // ---------------------------------
    }

    $db->transComplete();
    
    // --- LOG ERROR DATABASE JIKA TRANS FAILED ---
    if ($db->transStatus() === FALSE) {
        log_message('error', 'DB Transaction Failed: ' . $db->error()['message']);
        return false;
    }

    return true;
}
}