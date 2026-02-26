<?php

namespace App\Services;

use App\Models\UserModel;
use App\Models\RefreshTokenModel;

class AuthService
{
    private $userModel;
    private $refreshModel;
    private $tokenService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->refreshModel = new RefreshTokenModel();
        $this->tokenService = new TokenService();
    }

    public function register($name, $email, $password)
    {
        return $this->userModel->save([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role' => 'user'
        ]);
    }

    public function login($email, $password)
    {
        $user = $this->userModel->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        $access = $this->tokenService->generateAccessToken($user);
        $refresh = $this->tokenService->generateRefreshToken($user);

        $this->refreshModel->save([
            'user_id' => $user['id'],
            'token' => $refresh,
            'expires_at' => date('Y-m-d H:i:s', time() + 604800),
            'revoked' => false
        ]);

        return [
            'access_token' => $access,
            'refresh_token' => $refresh
        ];
    }

    public function refresh($refreshToken)
    {
        $stored = $this->refreshModel->findValidToken($refreshToken);

        if (!$stored) {
            return false;
        }

        $decoded = $this->tokenService->verifyRefresh($refreshToken);
        $user = $this->userModel->find($decoded->data->id);

        return $this->tokenService->generateAccessToken($user);
    }

    public function logout($refreshToken)
    {
        $token = $this->refreshModel
                      ->where('token', $refreshToken)
                      ->first();

        if ($token) {
            $this->refreshModel->update($token['id'], [
                'revoked' => true
            ]);
        }
    }
}