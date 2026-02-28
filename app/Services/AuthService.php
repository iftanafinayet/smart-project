<?php

namespace App\Services;

use App\Models\UserModel;
use Firebase\JWT\JWT;
use Exception;

class AuthService
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Logika Login
     */
    public function login($username, $password)
    {
        // 1. Cari user berdasarkan username dan join ke roles untuk dapet nama role-nya
        $user = $this->userModel->select('users.*, roles.role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('username', $username)
            ->first();

        // 2. Cek apakah user ada dan password cocok
        if ($user && password_verify($password, $user['password'])) {
            return $this->generateToken($user);
        }

        return false;
    }

    /**
     * Generate JWT Token
     */
    private function generateToken($user)
    {
        $key = getenv('JWT_SECRET') ?: 'default_secret_key'; // Ambil dari .env
        $iat = time();
        $exp = $iat + (60 * 60 * 24); // Token berlaku 24 jam

        $payload = [
            'iss'  => 'smart-project-pos', // Issuer
            'aud'  => 'smart-project-pos', // Audience
            'iat'  => $iat,                // Issued At
            'nbf'  => $iat,                // Not Before
            'exp'  => $exp,                // Expiration Time
            'uid'  => $user['id'],         // User ID
            'user' => $user['username'],   // Username
            'role' => $user['role_name'],   // Role Name (admin/kasir)
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    /**
     * Logic Refresh Token (Optional)
     */
    public function validateToken($token)
    {
        try {
            $key = getenv('JWT_SECRET');
            return JWT::decode($token, new \Firebase\JWT\Key($key, 'HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
}