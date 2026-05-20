<?php

namespace App\Models;

use CodeIgniter\Model;

class UnlockedChapterModel extends Model
{
    protected $table      = 'unlocked_chapters';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id', 'chapter_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // No updated_at field

    /**
     * Check if a user has unlocked a chapter
     */
    public function hasUnlocked(string $userId, int $chapterId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('chapter_id', $chapterId)
                    ->first() !== null;
    }

    /**
     * Unlock a chapter for a user
     */
    public function unlock(string $userId, int $chapterId): bool
    {
        if ($this->hasUnlocked($userId, $chapterId)) {
            return true;
        }

        return (bool) $this->insert((object) [
            'user_id'    => $userId,
            'chapter_id' => $chapterId
        ]);
    }
}
