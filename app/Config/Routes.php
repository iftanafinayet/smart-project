<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --- 1. LOGIN & REDIRECT ---
$routes->get('/', function() { return redirect()->to('/login'); });
$routes->get('login', function() { return view('login_view'); });

// --- 2. WEB ROUTES (Fixing View Paths) ---
$routes->group('', function($routes) {
    
    // Admin Dashboard
    // Pastikan DashboardController.php (Web) memanggil view('dashboard/index')
    $routes->get('dashboard', 'DashboardController::index'); 

    // Modul Utama: Mengarah ke folder/index sesuai struktur modular
    $routes->get('products', function() { return view('products/index'); });
    $routes->get('employees', function() { return view('employees/index'); });
    $routes->get('reports', function() { return view('reports/index'); });
    
    // Halaman Kasir (Jika file masih di root Views)
    $routes->get('sales', function() { return view('kasir_view'); }); 

    // Modul Gudang (Warehouse): Sinkronisasi Nama File
    $routes->group('warehouse', function($routes) {
        // Mengarahkan ke file dashboard.php di folder warehouse
        $routes->get('dashboard', function() { return view('warehouse/dashboard'); });
        
        // FIX: Jika file Anda bernama inventory.php, rute ini harus memanggil inventory
        $routes->get('inventory', function() { return view('warehouse/inventory'); }); 
        
        // Mengarahkan ke file stock_logs.php
        $routes->get('stock-logs', function() { return view('warehouse/stock_logs'); }); 
    });
});

// --- 3. API ROUTES (Tetap Menggunakan Namespace API) ---
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {
    
    $routes->post('login', 'AuthController::login');

    $routes->group('', ['filter' => ['jwt']], function($routes) {
        
        // Akses Data Produk & Penjualan
        $routes->get('products', 'ProductController::index', ['filter' => 'role:admin,kasir,gudang']);
        $routes->get('sales', 'SaleController::index'); 
        $routes->post('sales', 'SaleController::create'); 
        $routes->get('stock-logs', 'StockLogController::index');

        // Manajemen Admin
        $routes->group('', ['filter' => ['role:admin']], function($routes) {
            $routes->get('dashboard', 'DashboardController::index'); 
            $routes->resource('users', ['controller' => 'UserController']);
            $routes->resource('products', ['controller' => 'ProductController']);
        });
    });
});