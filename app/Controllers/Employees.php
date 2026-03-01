<?php

namespace App\Controllers;

class Employees extends BaseController
{
    public function index()
    {
        return view('employees_view');
    }
}