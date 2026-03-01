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

    /**
     * Endpoint API untuk menyuplai data ke Dashboard
     */
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
            
            // Identifikasi Role untuk hak akses data
            $roleId = $userData->role_id ?? null;
            $role = strtolower($userData->role ?? ($roleId == 1 ? 'admin' : 'staff'));

            // 1. Data Umum: Produk Stok Rendah (< 10)
            // Sinkron dengan renderDashboard() di Frontend
            $lowStockProducts = $productModel->where('current_stock <', 10)->findAll();

            $data = [
                'role' => $role,
                'low_stock_products' => $lowStockProducts
            ];

            // 2. Logika Khusus Admin: Statistik Finansial dan SDM
            if ($role === 'admin' || $roleId == 1) {
                // Penjualan Hari Ini
                $salesToday = $saleModel->where('DATE(sale_date)', date('Y-m-d'))
                                        ->selectSum('total_net')
                                        ->get()->getRow()->total_net;
                $data['sales_today'] = (float)($salesToday ?? 0);

                // Total SDM Organisasi
                $data['total_employees'] = $userModel->countAllResults();

                // Data Grafik Penjualan 7 Hari Terakhir
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

            // 3. Logika Khusus Gudang/Admin: Inventaris
            if (in_array($role, ['gudang', 'admin']) || in_array($roleId, [1, 3])) {
                $data['pending_po'] = $poModel->where('status', 'Pending')->countAllResults();
                $totalInv = $productModel->selectSum('current_stock')->get()->getRow()->current_stock;
                $data['total_inventory'] = (int)($totalInv ?? 0);
            }

            // Mengembalikan respon JSON ke Frontend
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