<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Ambil token dari header Authorization
        $header = $request->getHeaderLine('Authorization');
        $token = null;

        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Token not found'])
                ->setStatusCode(401);
        }

        // 2. Decode Token secara langsung
        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $payload = (array) $decoded;
        } catch (\Exception $e) {
            return Services::response()
                ->setJSON(['status' => 401, 'message' => 'Invalid token'])
                ->setStatusCode(401);
        }

        // 3. Ambil role dari payload
        $userRole = $payload['role'] ?? null;
        $requiredRole = $arguments[0] ?? null;

        // 4. 🔥 Pengecekan Case-Insensitive (Mengabaikan Huruf Besar/Kecil)
        if (!$userRole || strtolower($userRole) !== strtolower($requiredRole)) {
            return Services::response()
                ->setJSON([
                    'status'  => 403,
                    'message' => 'Forbidden - Insufficient Role Permissions',
                    'required' => [$requiredRole],
                    'current'  => $userRole 
                ])
                ->setStatusCode(403);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}