<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pastikan data user dari JwtFilter tersedia
        if (!isset($request->user)) {
            return Services::response()->setStatusCode(401)->setJSON(['message' => 'Unauthorized']);
        }

        $userRole = strtolower($request->user->role ?? '');

        // Validasi argumen rute (misal role:admin)
        if (!empty($arguments)) {
            $allowedRoles = array_map('strtolower', $arguments);
            if (!in_array($userRole, $allowedRoles)) {
                return Services::response()->setStatusCode(403)->setJSON([
                    'status' => 403,
                    'message' => "Akses Ditolak. Role ($userRole) tidak diizinkan di sini."
                ]);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}