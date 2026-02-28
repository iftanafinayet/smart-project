<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use App\Models\PurchaseOrderModel;

class PurchaseController extends BaseController
{
    // Controller sederhana untuk mengelola PO
    public function index() 
    {
        $model = new PurchaseOrderModel();
        return $this->respond($model->findAll());
    }
}