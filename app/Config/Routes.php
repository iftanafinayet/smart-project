<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', function() { return redirect()->to('/login'); });
$routes->get('login', function() { return view('login_view'); });

// --- WEB ROUTES ---
$routes->group('', function($routes) {
    $routes->get('dashboard', 'DashboardController::index'); 
    $routes->get('products', function() { return view('products/index'); });
    $routes->get('employees', function() { return view('employees/index'); });
    $routes->get('reports', function() { return view('reports/index'); });
    $routes->get('sales', function() { return view('kasir_view'); }); 

    $routes->group('warehouse', function($routes) {
        $routes->get('dashboard', function() { return view('warehouse/dashboard'); });
        $routes->get('inventory', function() { return view('warehouse/inventory'); }); 
        $routes->get('stock-logs', function() { return view('warehouse/stock_logs'); }); 
    });
});

// --- API ROUTES (Pusat Perbaikan) ---
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {
    $routes->post('login', 'AuthController::login');

    $routes->group('', ['filter' => ['jwt']], function($routes) {
        
        // Resource Products harus bisa diakses semua role (GET), 
        // tapi PUT/POST dibatasi lewat Filter atau Controller.
        $routes->resource('products', [
            'controller' => 'ProductController',
            'filter'     => 'role:admin,gudang,kasir' 
        ]);

        $routes->get('sales', 'SaleController::index', ['filter' => 'role:admin,kasir']); 
        $routes->post('sales', 'SaleController::create', ['filter' => 'role:admin,kasir']); 
        $routes->get('stock-logs', 'StockLogController::index', ['filter' => 'role:admin,gudang']);

        $routes->group('', ['filter' => ['role:admin']], function($routes) {
            $routes->get('dashboard', 'DashboardController::index'); 
            $routes->resource('users', ['controller' => 'UserController']);
        });
    });
});