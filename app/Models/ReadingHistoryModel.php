<?php

namespace App\Models;

use CodeIgniter\Model;

class ReadingHistoryModel extends Model
{
    protected $table      = 'reading_history';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id', 'work_id', 'chapter_id', 'updated_at'
    ];

    protected $useTimestamps = false; // We use updated_at manually or via DB

    /**
     * Update or create reading history for a user and work
     */
    public function updateProgress(string $userId, int $workId, int $chapterId)
    {
        $existing = $this->where([
            'user_id' => $userId,
            'work_id' => $workId
        ])->first();

        $data = [
            'user_id'    => $userId,
            'work_id'    => $workId,
            'chapter_id' => $chapterId,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            return $this->update($existing['id'], (object) $data);
        } else {
            return $this->insert((object) $data);
        }
    }

    /**
     * Get last read chapter for a user and work
     */
    public function getLastRead(string $userId, int $workId)
    {
        return $this->select('reading_history.*, chapters.title as chapter_title, chapters.order_num')
                    ->join('chapters', 'chapters.id = reading_history.chapter_id')
                    ->where([
                        'user_id' => $userId,
                        'reading_history.work_id' => $workId
                    ])
                    ->first();
    }
}
