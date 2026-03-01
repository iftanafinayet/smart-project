<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\SaleModel;

class SaleController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $start = $this->request->getVar('start');
        $end = $this->request->getVar('end');
        
        $model = new SaleModel();
        
        // Filter berdasarkan kolom 'sale_date' sesuai gambar
        if ($start && $end) {
            $sales = $model->where('sale_date >=', $start)
                           ->where('sale_date <=', $end)
                           ->findAll();
        } else {
            $sales = $model->findAll();
        }

        // Mengembalikan respon dengan struktur { "data": [...] }
        return $this->respond(['data' => $sales]);
    }
}