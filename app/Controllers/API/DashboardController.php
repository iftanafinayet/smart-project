<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\SaleModel;
use App\Models\UserModel;
use App\Models\ProductModel;

class DashboardController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $saleModel = new SaleModel();
        $userModel = new UserModel();
        $productModel = new ProductModel();

        // 1. Hitung Penjualan Hari Ini (Menggunakan total_net)
        $salesToday = $saleModel->where('sale_date', date('Y-m-d')) //
                                ->selectSum('total_net') //
                                ->get()
                                ->getRow()
                                ->total_net ?? 0;

        // 2. Total Karyawan
        $totalEmployees = $userModel->countAllResults();

        // 3. Stok Rendah
        $lowStockProducts = $productModel->where('current_stock <', 10)
                                        ->findAll();

        // 4. Data Grafik
        $chartLabels = [];
        $chartValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartLabels[] = date('d M', strtotime($date));
            
            $dailySales = $saleModel->where('sale_date', $date) //
                                    ->selectSum('total_net') //
                                    ->get()
                                    ->getRow()
                                    ->total_net ?? 0;
            $chartValues[] = (int)$dailySales;
        }

        return $this->respond([
            'status' => 200,
            'message' => 'Data berhasil diambil',
            'data' => [
                'sales_today' => $salesToday,
                'total_employees' => $totalEmployees,
                'low_stock_products' => $lowStockProducts,
                'sales_chart_data' => [
                    'labels' => $chartLabels,
                    'values' => $chartValues
                ]
            ]
        ]);
    }
}