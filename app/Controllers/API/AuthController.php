<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Services\AuthService;

class AuthController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $token = $this->service->login($username, $password);
        
        if (!$token) {
            return $this->failUnauthorized('Invalid credentials');
        }
        
        return $this->respondSuccess(['token' => $token], 'Login successful');
    }
}