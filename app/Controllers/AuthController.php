<?php

namespace App\Controllers;

use App\Models\UserModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register()
    {
        $this->authService->register(
            $this->request->getPost('name'),
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        return $this->response->setJSON(['message' => 'Registered']);
    }

    public function login()
    {
        $result = $this->authService->login(
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        if (!$result) {
            return $this->response->setStatusCode(401)
                ->setJSON(['message' => 'Invalid credentials']);
        }

        return $this->response->setJSON($result);
    }

    public function refresh()
    {
        $token = $this->request->getPost('refresh_token');

        $access = $this->authService->refresh($token);

        if (!$access) {
            return $this->response->setStatusCode(401)
                ->setJSON(['message' => 'Invalid refresh token']);
        }

        return $this->response->setJSON([
            'access_token' => $access
        ]);
    }

    public function logout()
    {
        $this->authService->logout(
            $this->request->getPost('refresh_token')
        );

        return $this->response->setJSON(['message' => 'Logged out']);
    }
}