<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PurchaseOrderModel;
use App\Models\PurchaseOrderItemModel; 
use App\Models\ProductModel;
use App\Models\StockLogModel; // Tambahkan Model Log Stok

class PurchaseOrderController extends BaseController
{
    use ResponseTrait;

    public function show($id = null)
    {
        $itemModel = new PurchaseOrderItemModel();
        
        $items = $itemModel->select('purchase_order_items.*, products.name as product_name')
                           ->join('products', 'products.id = purchase_order_items.product_id')
                           ->where('po_id', $id)
                           ->findAll();

        if (!$items) {
            return $this->failNotFound('Detail item tidak ditemukan');
        }

        return $this->respond(['data' => $items]);
    }

    public function index()
    {
        $model = new PurchaseOrderModel();
        $data = $model->select('purchase_orders.*, suppliers.supplier_name')
                      ->join('suppliers', 'suppliers.id = purchase_orders.supplier_id')
                      ->findAll();
        return $this->respond(['data' => $data]);
    }

    public function receiveOrder($id = null)
    {
        $db = \Config\Database::connect();
        $poModel = new PurchaseOrderModel();
        $itemModel = new PurchaseOrderItemModel();
        $productModel = new ProductModel();
        $stockLogModel = new StockLogModel(); // Inisialisasi Model Log
        
        $po = $poModel->find($id);
        if (!$po) return $this->failNotFound('Data PO tidak ditemukan');

        if ($po['status'] === 'Received') {
            return $this->failResourceExists('PO ini sudah diterima sebelumnya.');
        }

        $db->transStart();

        // 1. Update status PO menjadi Received
        $poModel->update($id, ['status' => 'Received']);

        $items = $itemModel->where('po_id', $id)->findAll();

        foreach ($items as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                // 2. Update stok produk
                $newStock = $product['stock'] + $item['qty'];
                $productModel->update($item['product_id'], ['stock' => $newStock]);

                // 3. Catat ke Stock Logs sesuai skema tabel terbaru
                $stockLogModel->insert([
                    'product_id'   => $item['product_id'],
                    'type'         => 'In',
                    'qty'          => $item['qty'],
                    'reference_no' => $po['po_number'], // Menggunakan nomor PO sebagai referensi
                    'created_at'   => date('Y-m-d H:i:s')
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->fail('Terjadi kesalahan saat memperbarui stok atau mencatat log.');
        }

        return $this->respond(['message' => 'Barang diterima, stok diperbarui, dan log telah dicatat.']);
    }
}