<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('api/v1', ['namespace' => 'App\Controllers\API'], function($routes) {

    // 🔓 PUBLIC
    $routes->post('login', 'AuthController::login');
    $routes->post('refresh', 'AuthController::refresh');

    // 🔐 PROTECTED (JWT Required)
    $routes->group('', ['filter' => 'jwt'], function($routes) {

        // 👤 Profile
        $routes->get('me', 'UserController::me');

        // 📦 Products (Accessible by all authenticated users)
        $routes->get('products', 'ProductController::index');
        $routes->get('products/(:num)', 'ProductController::show/$1');

        // 🔒 Admin Only - Product Mutations
        // Gunakan array untuk multiple filters jika diperlukan, 
        // tapi karena sudah di dalam group 'jwt', kita cukup tambahkan 'role'
        $routes->post('products', 'ProductController::create', ['filter' => 'role:admin']);
        $routes->put('products/(:num)', 'ProductController::update/$1', ['filter' => 'role:admin']);
        $routes->delete('products/(:num)', 'ProductController::delete/$1', ['filter' => 'role:admin']);

        // 💰 Sales
        // Pastikan nama Controller sesuai (Sales vs Sale)
        $routes->get('sales', 'SaleController::index', ['filter' => 'role:admin']);
        $routes->post('sales', 'SaleController::create'); 
    });
});