<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Services\SaleService;

class SaleController extends BaseController
{
    protected $service;

    public function __construct() { $this->service = new SaleService(); }

    public function index() { return $this->respond($this->service->getAllSales()); }

    public function create()
    {
        $data = $this->request->getJSON(true);
        $result = $this->service->processSale($data);
        if (!$result) return $this->fail('Transaction failed', 400);
        
        return $this->respondCreated(['message' => 'Sale successful']);
    }
}