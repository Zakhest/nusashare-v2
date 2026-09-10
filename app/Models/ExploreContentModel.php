<?php

namespace App\Models;

use CodeIgniter\Model;

class ExploreContentModel extends Model
{
    // Mengacu pada tabel 'works' sesuai skema SQL
    protected $table      = 'works';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // Model ini bersifat read-only untuk fitur Explore secara default, 
    // tapi ditambahkan allowedFields agar bisa digunakan untuk Tambah/Edit Karya.
    protected $allowedFields = [
        'creator_id', 
        'title', 
        'genre',
        'description', 
        'cover_url', 
        'content_type', 
        'status', 
        'is_paid',
        'price',
        'purchase_price',
        'allow_downloads',
        'watermark_text',
        'timer_duration',
        'is_locked', 
        'view_count',
        'access_type',
        'work_status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Base query untuk mengambil data karya publik
     * Menggabungkan tabel works dengan tabel users untuk mendapatkan nama pencipta
     */
    protected function basePublicQuery()
    {
        return $this->builder()
            ->select([
                'works.id',
                'works.creator_id',
                'works.title',
                'works.genre',
                'works.description',
                'works.content_type',
                'works.status',
                'works.is_locked',
                'works.is_paid',
                'works.price',
                'works.purchase_price',
                'works.allow_downloads',
                'works.watermark_text',
                'works.timer_duration',
                'works.cover_url', // Assuming this field exists
                'works.view_count', // Assuming this field exists
                'works.work_status',
                'works.access_type',
                'works.created_at',
                'works.updated_at',
                'users.username as creator_name' // Mengambil username dari tabel users
            ])
            ->join('users', 'users.id = works.creator_id')
            // Menampilkan konten yang statusnya bukan 'draft'
            ->whereIn('works.status', ['published', 'curated', 'museum']);
    }

    /**
     * Mengambil daftar konten untuk halaman utama (Explore)
     */
    public function getExploreList(int $limit = 12, int $offset = 0)
    {
        return $this->basePublicQuery()
            ->orderBy('works.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Mengambil detail konten berdasarkan ID
     */
    public function findById(int $id)
    {
        return $this->basePublicQuery()
            ->where('works.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * Mengambil daftar karya berdasarkan creator_id tertentu
     */
    public function getByCreator(string $creatorId, int $limit = 12)
    {
        return $this->basePublicQuery()
            ->where('works.creator_id', $creatorId)
            ->orderBy('works.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
