<?php
use CodeIgniter\Router\RouteCollection;
/** @var RouteCollection $routes */

// --- WEB ROUTES (Sudah Jalan - Tidak Diubah) ---
$routes->get('/', function() { return redirect()->to('/login'); });
$routes->get('login', function() { return view('login_view'); });

$routes->group('', function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('sales', function() { return view('kasir_view'); }); 
    $routes->get('products', function() { return view('products_view'); });
    $routes->get('employees', function() { return view('employees_view'); });
    $routes->get('reports', function() { return view('reports_view'); });

    $routes->group('warehouse', function($routes) {
        $routes->get('dashboard', function() { return view('warehouse_dashboard'); });
        $routes->get('inventory', function() { return view('warehouse_products_view'); });
        $routes->get('stock-logs', function() { return view('warehouse_stock_logs_view'); }); 
    });
});

// --- API ROUTES (Perbaikan Fokus di Sini) ---
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {
    $routes->post('login', 'AuthController::login');

    $routes->group('', ['filter' => ['jwt']], function($routes) {
        
        // Tetap pertahankan yang sudah jalan
        $routes->get('products', 'ProductController::index', ['filter' => 'role:admin,kasir,gudang']);

        // FIX 404 & DATABASE ERROR: Pastikan rute sales didefinisikan dengan benar
        // Rute GET untuk Laporan dan POST untuk Transaksi Kasir
        $routes->get('sales', 'SaleController::index'); 
        $routes->post('sales', 'SaleController::create');

        $routes->get('stock-logs', 'StockLogController::index');

        $routes->group('', ['filter' => ['role:admin']], function($routes) {
            $routes->get('dashboard', 'DashboardController::index');
            $routes->resource('users', ['controller' => 'UserController']);
            // Gunakan resource untuk mempermudah CRUD produk
            $routes->resource('products', ['controller' => 'ProductController']);
        });
    });
});