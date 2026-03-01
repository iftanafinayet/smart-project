<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtFilter implements FilterInterface
{
    /**
     * Mengecek validitas token JWT sebelum request masuk ke Controller
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Ambil header Authorization dari server
        // Menggunakan getServer untuk kompatibilitas konfigurasi Apache/Nginx yang ketat
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');

        // Jika getServer kosong, coba ambil melalui getHeaderLine sebagai cadangan
        if (!$authHeader) {
            $authHeader = $request->getHeaderLine('Authorization');
        }

        if (!$authHeader) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 401, 
                    'message' => 'Token tidak ditemukan. Silakan login terlebih dahulu.'
                ]);
        }

        try {
            // 2. Ekstrak token dari format "Bearer {token}"
            // Menggunakan regex agar lebih aman terhadap spasi ganda
            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $token = $matches[1];
            } else {
                $token = str_replace('Bearer ', '', $authHeader);
            }
            
            // Ambil secret key dari file .env
            $key = getenv('JWT_SECRET');
            
            // 3. Decode token menggunakan secret key
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // 4. Tempelkan data user hasil decode ke object request.
            // Data ini (seperti role_id) akan digunakan oleh RoleFilter selanjutnya.
            $request->user = $decoded;

        } catch (Exception $e) {
            // Jika token tidak valid, expired, atau salah signature
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 401, 
                    'message' => 'Sesi tidak valid atau telah berakhir.',
                    'debug' => $e->getMessage() 
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan aksi setelah request diproses
    }
}