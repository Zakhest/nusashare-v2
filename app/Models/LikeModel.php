<?php

namespace App\Models;

use CodeIgniter\Model;

class LikeModel extends Model
{
    protected $table            = 'likes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'work_id', 'created_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Check if a user has liked a work
     */
    public function hasLiked($userId, $workId)
    {
        return $this->where('user_id', $userId)
                    ->where('work_id', $workId)
                    ->countAllResults() > 0;
    }

    /**
     * Get like count for a work
     */
    public function getCount($workId)
    {
        return $this->where('work_id', $workId)->countAllResults();
    }

    /**
     * Toggle like for a user on a work
     */
    public function toggleLike($userId, $workId)
    {
        $existing = $this->where('user_id', $userId)
                         ->where('work_id', $workId)
                         ->first();

        if ($existing) {
            $this->delete($existing['id']);
            return ['status' => 'unliked', 'count' => $this->getCount($workId)];
        } else {
            $this->insert([
                'user_id' => $userId,
                'work_id' => $workId
            ]);
            return ['status' => 'liked', 'count' => $this->getCount($workId)];
        }
    }
}
