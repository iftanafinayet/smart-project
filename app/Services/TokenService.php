<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{
    private $accessKey = "ACCESS_SECRET_KEY";
    private $refreshKey = "REFRESH_SECRET_KEY";

    public function generateAccessToken($user)
    {
        $payload = [
            'iss' => 'ci4-api',
            'iat' => time(),
            'exp' => time() + 900,
            'data' => [
                'id' => $user['id'],
                'role' => $user['role']
            ]
        ];

        return JWT::encode($payload, $this->accessKey, 'HS256');
    }

    public function generateRefreshToken($user)
    {
        $payload = [
            'iat' => time(),
            'exp' => time() + 604800,
            'data' => [
                'id' => $user['id']
            ]
        ];

        return JWT::encode($payload, $this->refreshKey, 'HS256');
    }

    public function verifyAccess($token)
    {
        return JWT::decode($token, new Key($this->accessKey, 'HS256'));
    }

    public function verifyRefresh($token)
    {
        return JWT::decode($token, new Key($this->refreshKey, 'HS256'));
    }
}