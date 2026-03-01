<?php

namespace App\Models;

use CodeIgniter\Model;

class PurhcaseOrderItemModel extends Model
{
    protected $table            = 'purchase_order_items';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['po_id', 'product_id', 'qty', 'cost_per_unit'];
    protected $useTimestamps    = false;
}