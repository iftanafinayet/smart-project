<?php

namespace App\Repository;

use App\Models\ProductModel;

class ProductRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    public function findAllWithCategory()
    {
        return $this->model->select('products.*, categories.category_name')
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->findAll();
    }

    public function updateStock($id, $qty)
    {
        return $this->model->where('id', $id)->set('current_stock', "current_stock + $qty", false)->update();
    }
}