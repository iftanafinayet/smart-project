<?php

namespace App\Services;

use App\Models\UserModel;
use Firebase\JWT\JWT;

class AuthService
{
    public function login($username, $password)
    {
        $model = new UserModel();
        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $key = getenv('JWT_SECRET');
            
            // Konversi ID ke string untuk dibaca oleh RoleFilter
            $roleName = match ((int)$user['role_id']) {
                1 => 'admin',
                2 => 'kasir',
                3 => 'gudang',
                default => 'guest',
            };

            $payload = [
                'iat'     => time(),
                'exp'     => time() + (60 * 60 * 24),
                'uid'     => $user['id'],
                'user'    => $user['username'],
                'role_id' => $user['role_id'],
                'role'    => $roleName, // Dibaca oleh RoleFilter agar tidak "Akses Ditolak"
            ];

            return [
                'token'   => JWT::encode($payload, $key, 'HS256'),
                'role_id' => $user['role_id']
            ];
        }
        return null;
    }
}