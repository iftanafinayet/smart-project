<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // 1. Cek apakah user sudah login di session web
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login'); // Arahkan ke halaman login
        }

        // 2. Tampilkan view dashboard
        return view('dashboard/index');
    }
}