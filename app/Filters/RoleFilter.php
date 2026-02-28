<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class RoleFilter implements FilterInterface
{
    /**
     * Memeriksa apakah user memiliki role yang diizinkan.
     * Penggunaan di Routes: 'filter' => 'role:Admin,Manager'
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');

        if (!$authHeader) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['status' => 401, 'message' => 'Unauthorized - Token not found']);
        }

        try {
            // Ambil token dari format "Bearer {token}"
            $token = str_replace('Bearer ', '', $authHeader);
            $key   = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // Ambil role_name atau role_id dari payload JWT
            // Catatan: Pastikan di AuthService saat login, Anda memasukkan 'role' ke payload
            $userRole = $decoded->role ?? null;

            if (!$userRole || ($arguments && !in_array($userRole, $arguments))) {
                return Services::response()
                    ->setStatusCode(403)
                    ->setJSON([
                        'status' => 403, 
                        'message' => 'Forbidden - Insufficient Role Permissions',
                        'required' => $arguments,
                        'current' => $userRole
                    ]);
            }

        } catch (Exception $e) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['status' => 401, 'message' => 'Invalid Token: ' . $e->getMessage()]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}