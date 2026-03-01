<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==========================================
// 🌐 WEB ROUTES (Akses langsung di Browser)
// ==========================================

// Rute untuk menampilkan halaman login
$routes->get('login', function() {
    return view('login_view');
});

// Rute untuk menampilkan halaman dashboard di browser
$routes->get('dashboard', 'DashboardController::index');

// Rute untuk menampilkan halaman manajemen (Views)
$routes->get('products', 'Products::index');
$routes->get('employees', 'Employees::index');
$routes->get('reports', 'Reports::index');

// ==========================================
// 🔌 API ROUTES (Akses melalui Fetch/Postman)
// ==========================================
$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {

    // 🔓 PUBLIC ROUTES - Accessible without authentication
    $routes->post('login', 'AuthController::login');
    $routes->post('refresh', 'AuthController::refresh');

    // 🔐 PROTECTED ROUTES - Requires JWT Authentication
    $routes->group('', ['filter' => 'jwt'], function($routes) {

        // 👤 User Profile
        $routes->get('me', 'UserController::me');

        // 📦 Product Management
        $routes->get('products', 'ProductController::index');
        $routes->get('products/(:num)', 'ProductController::show/$1');

        // 🔒 Admin Only - Product Mutations
        $routes->post('products', 'ProductController::create', ['filter' => 'role:admin']);
        $routes->put('products/(:num)', 'ProductController::update/$1', ['filter' => 'role:admin']);
        $routes->delete('products/(:num)', 'ProductController::delete/$1', ['filter' => 'role:admin']);
        
        // 📊 Dashboard API (Admin Only)
        $routes->get('dashboard', 'DashboardController::index', ['filter' => 'role:admin']);

        // 💰 Sales Management
        $routes->get('sales', 'SaleController::index', ['filter' => 'role:admin']);
        $routes->post('sales', 'SaleController::create'); 

        // 👥 User/Employee Management (CRUD Karyawan)
        $routes->resource('users', ['controller' => 'UserController', 'filter' => 'role:admin']);
    });
});