<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;
use App\Models\PurchaseOrderModel;

class WarehouseDashboard extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $productModel = new ProductModel();
        $poModel = new PurchaseOrderModel();

        // 1. Ambil stok kritis (misal stok < 10)
        $criticalStock = $productModel->where('stock <', 10)->findAll();

        // 2. Hitung PO Pending (Perlu dicek)
        $pendingPO = $poModel->where('status', 'Pending')->countAllResults();

        // 3. PO Diterima Hari Ini
        $today = date('Y-m-d');
        $receivedToday = $poModel->where('status', 'Received')
                                 ->where('po_date', $today)
                                 ->countAllResults();

        return $this->respond([
            'critical_items' => $criticalStock,
            'stats' => [
                'pending_po' => $pendingPO,
                'received_today' => $receivedToday
            ]
        ]);
    }
}