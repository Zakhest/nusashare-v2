<?php

namespace App\Models;

use CodeIgniter\Model;

class ChapterModel extends Model
{
    protected $table      = 'chapters';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'work_id', 'title', 'body', 'order_num', 'status', 'is_locked', 'price'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all chapters for a work, ordered by order_num
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
