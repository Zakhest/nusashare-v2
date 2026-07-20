<?php

namespace App\Services;

use App\Models\NotificationModel;

/**
 * NotificationService
 *
 * Layanan terpusat untuk membuat notifikasi.
 * Gunakan method notify() dari controller mana pun agar logika tidak tersebar.
 *
 * Contoh penggunaan:
 *   $notifService = new \App\Services\NotificationService();
 *   $notifService->notify($creatorId, 'follow', 'Pengikut Baru', '...', '/profile/...');
 */
class NotificationService
{
    protected NotificationModel $model;

    public function __construct()
    {
        $this->model = new NotificationModel();
    }

    /**
     * Buat satu notifikasi untuk user tertentu.
     *
     * @param string      $userId  ID user penerima (VARCHAR 9, sesuai users.id)
     * @param string      $type    Tipe notifikasi: 'follow'|'like'|'comment'|'purchase'|'unlock'|'topup'|'system'
     * @param string      $title   Judul singkat (max 255 karakter)
     * @param string      $message Pesan deskriptif
     * @param string|null $link    URL relatif yang dituju saat notifikasi diklik (opsional)
     */
    public function notify(string $userId, string $type, string $title, string $message, ?string $link = null): void
    {
        // Jangan buat notifikasi jika userId kosong atau tidak valid
        if (empty($userId)) {
            return;
        }

        $this->model->insert([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
            'is_read' => 0,
        ]);
    }

    /**
     * Shortcut: notifikasi pengikut baru untuk kreator.
     *
     * @param string $creatorId  ID kreator yang diikuti
     * @param string $followerUsername Username user yang mengikuti
     */
    public function notifyFollow(string $creatorId, string $followerUsername): void
    {
        $this->notify(
            $creatorId,
            'follow',
            'Pengikut Baru',
            "@{$followerUsername} mulai mengikuti Anda.",
            '/me/follows'
        );
    }

    /**
     * Shortcut: notifikasi karya disukai.
     *
     * @param string $creatorId  ID kreator pemilik karya
     * @param string $likerUsername Username user yang menyukai
     * @param string $workTitle Judul karya
     * @param int    $workId    ID karya
     */
    public function notifyLike(string $creatorId, string $likerUsername, string $workTitle, int $workId): void
    {
        $this->notify(
            $creatorId,
            'like',
            'Karya Disukai',
            "@{$likerUsername} menyukai karya Anda: {$workTitle}",
            "/works/{$workId}"
        );
    }

    /**
     * Shortcut: notifikasi komentar baru.
     *
     * @param string $creatorId       ID kreator pemilik karya
     * @param string $commenterUsername Username komentator
     * @param string $workTitle       Judul karya
     * @param int    $workId          ID karya
     */
    public function notifyComment(string $creatorId, string $commenterUsername, string $workTitle, int $workId): void
    {
        $this->notify(
            $creatorId,
            'comment',
            'Komentar Baru',
            "@{$commenterUsername} berkomentar di karya Anda: {$workTitle}",
            "/works/{$workId}"
        );
    }

    /**
     * Shortcut: notifikasi karya dibeli/didownload.
     *
     * @param string $creatorId    ID kreator pemilik karya
     * @param string $buyerUsername Username pembeli
     * @param string $workTitle   Judul karya
     * @param int    $workId      ID karya
     */
    public function notifyPurchase(string $creatorId, string $buyerUsername, string $workTitle, int $workId): void
    {
        $this->notify(
            $creatorId,
            'purchase',
            'Karya Dibeli',
            "@{$buyerUsername} telah membeli/mengunduh karya Anda: {$workTitle}",
            "/works/{$workId}"
        );
    }

    /**
     * Shortcut: notifikasi karya berbayar (dengan timer) dibuka.
     *
     * @param string $creatorId    ID kreator pemilik karya
     * @param string $buyerUsername Username pembeli
     * @param string $workTitle   Judul karya
     * @param int    $workId      ID karya
     */
    public function notifyUnlock(string $creatorId, string $buyerUsername, string $workTitle, int $workId): void
    {
        $this->notify(
            $creatorId,
            'unlock',
            'Karya Dibuka',
            "@{$buyerUsername} membuka akses karya Anda: {$workTitle}",
            "/works/{$workId}"
        );
    }

    /**
     * Shortcut: notifikasi bab terkunci dibuka.
     *
     * @param string $creatorId    ID kreator
     * @param string $buyerUsername Username pembeli
     * @param string $chapterTitle Judul bab
     * @param string $workTitle   Judul karya
     * @param int    $workId      ID karya
     */
    public function notifyUnlockChapter(string $creatorId, string $buyerUsername, string $chapterTitle, string $workTitle, int $workId): void
    {
        $this->notify(
            $creatorId,
            'unlock',
            'Bab Dibuka',
            "@{$buyerUsername} membuka bab \"{$chapterTitle}\" di karya Anda: {$workTitle}",
            "/works/{$workId}"
        );
    }

    /**
     * Shortcut: notifikasi topup berhasil untuk user sendiri.
     *
     * @param string $userId      ID user yang melakukan topup
     * @param int    $amount      Jumlah CC yang ditambahkan
     * @param string $packageLabel Label paket topup
     */
    public function notifyTopup(string $userId, int $amount, string $packageLabel): void
    {
        $this->notify(
            $userId,
            'topup',
            'Top Up Berhasil',
            'Top Up Paket ' . $packageLabel . ' sebesar ' . number_format($amount) . ' CC berhasil ditambahkan ke saldo Anda.',
            '/topup'
        );
    }
}
