<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        // Tampilkan view saja
        return view('dashboard/index');
    }
}