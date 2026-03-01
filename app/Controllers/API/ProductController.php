<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    use ResponseTrait;

    /**
     * Menampilkan daftar produk dengan fitur filter stok
     */
    public function index()
    {
        $model = new ProductModel();
        
        // Mengambil parameter filter dari request (misal: ?filter=low_stock)
        $filter = $this->request->getVar('filter');

        // Jika filter low_stock aktif, tampilkan produk dengan stok < 10
        if ($filter === 'low_stock') {
            $data = $model->where('stock <', 10)->findAll();
        } else {
            $data = $model->findAll();
        }

        return $this->respond(['data' => $data]);
    }

    public function create()
    {
        $model = new ProductModel();
        $data = $this->request->getJSON(true);

        // Disarankan menambahkan validasi sesuai kebutuhan MERN/PHP Stack Anda
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