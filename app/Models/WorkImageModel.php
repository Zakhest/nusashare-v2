<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkImageModel extends Model
{
    protected $table      = 'work_images';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'work_id', 'file_path', 'order_num'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Get all images for a work, ordered by order_num
     */
    public function getByWork(int $workId): array
    {
        return $this->where('work_id', $workId)
                    ->orderBy('order_num', 'ASC')
                    ->findAll();
    }

    /**
     * Get next order number for a work
     */
    public function getNextOrder(int $workId): int
    {
        $max = $this->selectMax('order_num')->where('work_id', $workId)->first();
        return ($max['order_num'] ?? 0) + 1;
    }
}
