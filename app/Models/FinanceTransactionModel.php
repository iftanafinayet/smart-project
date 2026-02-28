<?php

namespace App\Models;

use CodeIgniter\Model;

class FinanceTransactionModel extends Model
{
    protected $table            = 'finance_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields    = ['type', 'amount', 'description', 'transaction_date', 'reference_no'];
    protected $useTimestamps    = false;
}