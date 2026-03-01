<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class SaleController extends BaseController
{
    use ResponseTrait;

    // Menangani GET /api/v1/sales?start=...&end=...
    public function index()
    {
        $db = \Config\Database::connect();
        $start = $this->request->getVar('start');
        $end = $this->request->getVar('end');
        
        $builder = $db->table('sales');

        // Sesuai skema: sale_date (DATE)
        if ($start && $end) {
            $builder->where('sale_date >=', $start)
                    ->where('sale_date <=', $end);
        }

        $sales = $builder->orderBy('sale_date', 'DESC')->get()->getResult();

        return $this->respond([
            'status' => 200,
            'data'   => $sales
        ]);
    }

    // Menangani POST /api/v1/sales (Transaksi Kasir)
    public function create()
    {
        $db = \Config\Database::connect();
        $data = $this->request->getJSON(true);

        $db->transStart();
        try {
            // Sesuai skema: invoice_number VARCHAR(50)
            $invoiceNo = 'INV-' . date('YmdHis');

            // 1. Insert ke tabel 'sales'
            $db->table('sales')->insert([
                'invoice_number' => $invoiceNo,
                'user_id'        => (int)($this->request->user->uid ?? 1),
                'sale_date'      => date('Y-m-d'),
                'total_gross'    => (float)$data['total_gross'],
                'discount_total' => (float)($data['discount_total'] ?? 0),
                'total_net'      => (float)$data['total_net'],
                'payment_status' => 'Lunas'
            ]);
            
            $saleId = $db->insertID();

            foreach ($data['items'] as $item) {
                // 2. Insert ke detail (sale_items)
                $db->table('sale_items')->insert([
                    'sale_id'    => $saleId,
                    'product_id' => (int)$item['product_id'],
                    'qty'        => (int)$item['qty'],
                    'price'      => (float)$item['price']
                ]);

                // 3. Update stok di tabel 'products'
                $db->table('products')
                   ->where('id', $item['product_id'])
                   ->set('current_stock', "current_stock - " . (int)$item['qty'], false)
                   ->update();

                // 4. Catat riwayat di 'stock_logs'
                $db->table('stock_logs')->insert([
                    'product_id'   => (int)$item['product_id'],
                    'type'         => 'Out',
                    'qty'          => (int)$item['qty'],
                    'reference_no' => $invoiceNo,
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Gagal menyimpan transaksi ke database.');
            }

            return $this->respondCreated(['status' => 201, 'invoice' => $invoiceNo]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }
}