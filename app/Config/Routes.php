<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('api', ['namespace' => 'App\Controllers\API'], function($routes) {

    $routes->post('login', 'AuthController::login');

    $routes->group('', ['filter' => 'jwt'], function($routes) {

        // Products
        $routes->get('products', 'ProductController::index');
        $routes->post('products', 'ProductController::create');
        $routes->get('products/(:num)', 'ProductController::show/$1');
        $routes->put('products/(:num)', 'ProductController::update/$1');
        $routes->delete('products/(:num)', 'ProductController::delete/$1');

        // Sales
        $routes->get('sales', 'SalesController::index');
        $routes->post('sales', 'SalesController::create');
    });
});