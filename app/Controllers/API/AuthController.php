<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Services\AuthService;
use CodeIgniter\API\ResponseTrait;

class AuthController extends BaseController
{
    use ResponseTrait;
    protected $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function login()
    {
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        // Memanggil service untuk memvalidasi user & membuat JWT
        $authData = $this->service->login($username, $password);
        
        if (!$authData) {
            return $this->failUnauthorized('Username atau password salah.');
        }
        
        // Response menyertakan token yang didalamnya harus sudah ada klaim 'role' string
        return $this->respond([
            'status' => 200,
            'message' => 'Login Berhasil',
            'data' => [
                'token'   => $authData['token'],
                'role_id' => $authData['role_id'],
                'username'=> $username
            ]
        ]);
    }
}