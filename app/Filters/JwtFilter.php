<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtFilter implements FilterInterface
{
    private $key = "SUPER_SECRET_JWT_KEY";

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');

        if (!$header) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['message' => 'Token Required']);
        }

        $token = explode(' ', $header)[1];

        try {
            $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
        } catch (\Exception $e) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['message' => 'Invalid Token']);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}