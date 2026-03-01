<?php

namespace App\Controllers;

class Products extends BaseController
{
    public function index()
    {
        // Tampilkan view products
        return view('products_view');
    }
}