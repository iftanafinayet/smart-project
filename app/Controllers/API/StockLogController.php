<?php

namespace App\Controllers\API;

use CodeIgniter\RESTful\ResourceController;
use App\Models\StockLogModel;

class StockLogController extends ResourceController
{
    protected $format = 'json';

    public function index()
    {
        $model = new StockLogModel();
        
        // Mengambil data log beserta nama produknya
        $data = $model->getLogsWithProduct();

        if (!$data) {
            return $this->respond([
                'status' => 200,
                'message' => 'Belum ada riwayat stok',
                'data' => []
            ]);
        }

        return $this->respond([
            'status' => 200,
            'data'   => $data
        ]);
    }
}