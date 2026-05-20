<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'amount', 'type', 'category', 'reference_id', 'description', 'created_at'];

    protected $useTimestamps = false; // We use created_at manually or via DB default
    protected $createdField  = 'created_at';

    /**
     * Record a transaction
     */
    public function record($userId, $amount, $type, $category, $referenceId = null, $description = null)
    {
        return $this->insert((object)[
            'user_id'      => $userId,
            'amount'       => $amount,
            'type'         => $type,
            'category'     => $category,
            'reference_id' => $referenceId,
            'description'  => $description,
            'created_at'   => date('Y-m-d H:i:s')
        ]);
    }
}
