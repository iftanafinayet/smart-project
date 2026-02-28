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
        // Mengambil header Authorization. 
        // Catatan: Jika menggunakan Apache, pastikan .htaccess mengizinkan HTTP_AUTHORIZATION
        $authHeader = $request->getServer('HTTP_AUTHORIZATION');

        // 1. Cek apakah header Authorization ada
        if (!$authHeader) {
            return Services::response()
                ->setStatusCode(401)
                ->setJSON(['status' => 401, 'message' => 'Token not found']);
        }

        try {
            // 2. Ambil token dari format "Bearer {token}"
            $token = str_replace('Bearer ', '', $authHeader);
            
            // Ambil secret key dari file .env
            $key = getenv('JWT_SECRET');
            
            // 3. Decode token menggunakan secret key
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            // 4. Tempelkan data user hasil decode ke object request.
            // Di CI4, kita bisa menambah properti dinamis ke objek $request
            // agar bisa diakses di Controller atau filter berikutnya.
            $request->user = $decoded;

        } catch (Exception $e) {
            // Jika token tidak valid, expired, atau salah signature
            return Services::response()
                ->setStatusCode(401)
                ->setJSON([
                    'status' => 401, 
                    'message' => 'Invalid or Expired Token',
                    'error' => $e->getMessage() // Opsional: tampilkan error detail untuk debugging
                ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak diperlukan aksi setelah request diproses
    }
}