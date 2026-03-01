<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\SaleModel;
use App\Models\UserModel;
use App\Models\ProductModel;
use App\Models\PurchaseOrderModel;

class DashboardController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        try {
            $saleModel = new SaleModel();
            $userModel = new UserModel();
            $productModel = new ProductModel();
            $poModel = new PurchaseOrderModel();

            // Ambil data user dari JwtFilter
            $request = $this->request;
            $userData = property_exists($request, 'user') ? $request->user : null;
            
            // Fallback role_id jika role name tidak tersedia
            $roleId = $userData->role_id ?? null;
            $role = strtolower($userData->role ?? ($roleId == 1 ? 'admin' : 'staff'));

            // 1. Ambil Produk Stok Rendah (< 10)
            // Kunci diubah menjadi 'low_stock_products' agar sesuai dengan JS di Frontend
            $lowStockProducts = $productModel->where('current_stock <', 10)->findAll();

            $data = [
                'role' => $role,
                'low_stock_products' => $lowStockProducts // Sesuai baris 153 di dashboard_view.php
            ];

            // 2. Logika Khusus Admin
            if ($role === 'admin' || $roleId == 1) {
                // Penjualan Hari Ini
                $salesToday = $saleModel->where('DATE(sale_date)', date('Y-m-d'))
                                        ->selectSum('total_net')
                                        ->get()->getRow()->total_net;
                $data['sales_today'] = (float)($salesToday ?? 0);

                $data['total_employees'] = $userModel->countAllResults();

                // Data Grafik 7 Hari Terakhir
                $chartLabels = [];
                $chartValues = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $chartLabels[] = date('d M', strtotime($date));
                    $dailySum = $saleModel->where('DATE(sale_date)', $date)
                                          ->selectSum('total_net')
                                          ->get()->getRow()->total_net;
                    $chartValues[] = (float)($dailySum ?? 0);
                }
                $data['sales_chart_data'] = [
                    'labels' => $chartLabels,
                    'values' => $chartValues
                ];
            }

            // 3. Logika Khusus Gudang/Admin
            if (in_array($role, ['gudang', 'admin']) || in_array($roleId, [1, 3])) {
                $data['pending_po'] = $poModel->where('status', 'Pending')->countAllResults();
                $totalInv = $productModel->selectSum('current_stock')->get()->getRow()->current_stock;
                $data['total_inventory'] = (int)($totalInv ?? 0);
            }

            return $this->respond([
                'status' => 200,
                'message' => 'Success',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan pada server: ' . $e->getMessage());
        }
    }
}