<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $model = new ProductModel();
        return $this->respond(['data' => $model->findAll()]);
    }

    public function create()
    {
        $model = new ProductModel();
        $data = $this->request->getJSON(true);

        // Validasi bisa ditambahkan di sini

        if ($model->insert($data)) {
            return $this->respondCreated(['message' => 'Produk berhasil ditambahkan']);
        }
        return $this->fail('Gagal menambahkan produk');
    }

    public function update($id = null)
    {
        $model = new ProductModel();
        $data = $this->request->getJSON(true);
        
        if ($model->update($id, $data)) {
            return $this->respond(['message' => 'Produk berhasil diupdate']);
        }
        return $this->failNotFound('Produk tidak ditemukan');
    }

    public function delete($id = null)
    {
        $model = new ProductModel();
        if ($model->delete($id)) {
            return $this->respondDeleted(['message' => 'Produk berhasil dihapus']);
        }
        return $this->failNotFound('Produk tidak ditemukan');
    }
}