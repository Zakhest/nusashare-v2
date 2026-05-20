<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'comments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'work_id', 'chapter_id', 'content'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get comments for a work (main work comments, excluding chapter specific ones)
     */
    public function getByWork($workId)
    {
        return $this->select('comments.*, users.username, users.role')
                    ->join('users', 'users.id = comments.user_id')
                    ->where('comments.work_id', $workId)
                    ->where('comments.chapter_id', null)
                    ->orderBy('comments.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get comments for a specific chapter
     */
    public function getByChapter($chapterId)
    {
        return $this->select('comments.*, users.username, users.role')
                    ->join('users', 'users.id = comments.user_id')
                    ->where('comments.chapter_id', $chapterId)
                    ->orderBy('comments.created_at', 'ASC') // ASC for conversation feel
                    ->findAll();
    }
}
