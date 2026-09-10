<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'cart_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'work_id'];

    protected $useTimestamps = false;

    /**
     * Ambil semua item cart user lengkap dengan data karya.
     * Hanya karya berstatus published/curated/museum yang ditampilkan.
     */
    public function getCartWithWorks(string $userId): array
    {
        return $this->db->table('cart_items')
            ->select([
                'cart_items.id as cart_id',
                'cart_items.work_id',
                'cart_items.created_at as added_at',
                'works.title',
                'works.description',
                'works.content_type',
                'works.cover_url',
                'works.is_paid',
                'works.price',
                'works.purchase_price',
                'works.allow_downloads',
                'works.creator_id',
                'works.status as work_status_pub',
                'users.username as creator_name',
            ])
            ->join('works', 'works.id = cart_items.work_id')
            ->join('users', 'users.id = works.creator_id')
            ->where('cart_items.user_id', $userId)
            ->whereIn('works.status', ['published', 'curated', 'museum'])
            ->orderBy('cart_items.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Cek apakah work sudah ada di cart user.
     */
    public function inCart(string $userId, int $workId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('work_id', $workId)
                    ->countAllResults() > 0;
    }

    /**
     * Ambil semua work_id yang ada di cart user (untuk tampilan badge).
     */
    public function getWorkIds(string $userId): array
    {
        $rows = $this->select('work_id')
                     ->where('user_id', $userId)
                     ->findAll();
        return array_column($rows, 'work_id');
    }

    /**
     * Hitung total harga semua item berbayar di cart.
     */
    public function getTotalPrice(string $userId): int
    {
        $result = $this->db->table('cart_items')
            ->select('SUM(CASE WHEN works.purchase_price > 0 THEN works.purchase_price ELSE works.price END) as total')
            ->join('works', 'works.id = cart_items.work_id')
            ->where('cart_items.user_id', $userId)
            ->where('works.creator_id !=', $userId)
            ->where('works.is_paid', 1)
            ->get()
            ->getRowArray();

        return (int)($result['total'] ?? 0);
    }

    /**
     * Kosongkan seluruh cart user.
     */
    public function clearCart(string $userId): void
    {
        $this->where('user_id', $userId)->delete();
    }
}
