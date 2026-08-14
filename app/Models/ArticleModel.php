<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table      = 'articles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'work_id',
        'slug',
        'body',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil artikel beserta data works + creator via slug.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->table('articles')
            ->select([
                'articles.id',
                'articles.work_id',
                'articles.slug',
                'articles.body',
                'articles.created_at',
                'articles.updated_at',
                'works.title',
                'works.description',
                'works.genre',
                'works.status',
                'works.view_count',
                'works.creator_id',
                'works.is_paid',
                'users.username as creator_name',
            ])
            ->join('works', 'works.id = articles.work_id')
            ->join('users', 'users.id = works.creator_id')
            ->whereIn('works.status', ['published', 'curated', 'museum'])
            ->where('articles.slug', $slug)
            ->get()
            ->getRowArray();
    }

    /**
     * Ambil artikel berdasarkan work_id (untuk edit).
     */
    public function findByWorkId(int $workId): ?array
    {
        return $this->where('work_id', $workId)->first();
    }

    /**
     * Generate slug unik dari judul.
     * Tambah suffix numerik jika slug sudah ada.
     */
    public function generateSlug(string $title, ?int $excludeWorkId = null): string
    {
        $base = strtolower(trim($title));
        // Transliterasi sederhana huruf Indonesia
        $base = strtr($base, [
            'ā' => 'a', 'ī' => 'i', 'ū' => 'u',
        ]);
        // Ganti karakter non-alphanumeric dengan dash
        $base = preg_replace('/[^a-z0-9]+/', '-', $base);
        $base = trim($base, '-');
        $base = substr($base, 0, 80); // maks 80 karakter

        $slug    = $base;
        $counter = 2;

        while ($this->slugExists($slug, $excludeWorkId)) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Cek apakah slug sudah dipakai.
     */
    private function slugExists(string $slug, ?int $excludeWorkId = null): bool
    {
        $builder = $this->where('slug', $slug);
        if ($excludeWorkId !== null) {
            $builder = $builder->where('work_id !=', $excludeWorkId);
        }
        return $builder->countAllResults() > 0;
    }
}
