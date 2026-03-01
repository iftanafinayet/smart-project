<?php

namespace App\Services;

use Config\Database;

class DashboardService
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getDashboardData()
    {
        $today = date('Y-m-d');
        $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));

        // 1. Total Penjualan Hari Ini
        $totalSalesToday = $this->db->table('sales')
            ->selectSum('total_net')
            ->where('sale_date', $today)
            ->where('payment_status', 'Paid')
            ->get()
            ->getRow()
            ->total_net ?? 0;

        // 2. Histori Penjualan 7 Hari Terakhir (UNTUK GRAFIK)
        // Kita gunakan query grouping by date
        $salesHistory = $this->db->table('sales')
            ->select('sale_date as date, SUM(total_net) as total')
            ->where('sale_date >=', $sevenDaysAgo)
            ->where('payment_status', 'Paid')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'ASC')
            ->get()
            ->getResultArray();

        // 3. Sisa Stok Terendah
        $lowStockProducts = $this->db->table('products')
            ->select('product_name, current_stock, min_stock')
            ->where('current_stock <= min_stock')
            ->orderBy('current_stock', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // 4. Total Karyawan
        $totalEmployees = $this->db->table('employees')->countAll();

        return [
            'sales_today'        => (float) $totalSalesToday,
            'sales_chart_data'   => $salesHistory, // Ini data untuk Chart.js/ApexCharts
            'low_stock_products' => $lowStockProducts,
            'total_employees'    => (int) $totalEmployees,
            'date'               => $today
        ];
    }
}