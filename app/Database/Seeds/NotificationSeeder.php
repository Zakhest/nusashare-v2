<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $userIds = $this->db->table('users')->select('id')->limit(5)->get()->getResultArray();
        $data = [];
        
        foreach ($userIds as $user) {
            $data[] = [
                'user_id'    => $user['id'],
                'type'       => 'system',
                'title'      => 'Selamat Datang!',
                'message'    => 'Notifikasi sistem baru telah aktif.',
                'link'       => '/explore',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $data[] = [
                'user_id'    => $user['id'],
                'type'       => 'follow',
                'title'      => 'Pengikut Baru',
                'message'    => 'Seseorang mulai mengikuti Anda hari ini.',
                'link'       => '/dashboard/follows',
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ];
            $data[] = [
                'user_id'    => $user['id'],
                'type'       => 'like',
                'title'      => 'Karya Disukai',
                'message'    => 'Karya Anda mendapat 10 suka baru.',
                'link'       => '/dashboard',
                'is_read'    => 1, // Read message
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ];
        }
        
        if (!empty($data)) {
            $this->db->table('notifications')->insertBatch($data);
        }
    }
}
