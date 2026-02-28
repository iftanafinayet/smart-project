<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use CodeIgniter\API\ResponseTrait; // TAMBAHKAN INI

abstract class BaseController extends Controller
{
    use ResponseTrait; // GUNAKAN INI

    protected $request;
    protected $helpers = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    protected function respondSuccess($data = [], $message = 'Success', $code = 200)
    {
        return $this->respond([
            'status'  => $code,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}