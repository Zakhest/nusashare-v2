<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixNotificationsUserIdType extends Migration
{
    public function up()
    {
        // Data lama dengan user_id integer tidak valid (mismatch dengan users.id CHAR/VARCHAR(9))
        // Hapus semua record invalid sebelum ALTER agar tidak ada error konversi
        $this->db->query("TRUNCATE TABLE notifications");

        // Ubah user_id dari INT UNSIGNED menjadi VARCHAR(9) agar cocok dengan users.id
        $this->db->query("ALTER TABLE notifications MODIFY COLUMN user_id VARCHAR(9) NOT NULL");

        // Pastikan index user_id masih ada setelah alter
        // (DROP dulu jika ada, lalu re-create)
        $this->db->query("ALTER TABLE notifications DROP INDEX IF EXISTS user_id");
        $this->db->query("ALTER TABLE notifications ADD INDEX (user_id)");
    }

    public function down()
    {
        // Kembalikan ke INT UNSIGNED (data akan hilang lagi)
        $this->db->query("TRUNCATE TABLE notifications");
        $this->db->query("ALTER TABLE notifications MODIFY COLUMN user_id INT(11) UNSIGNED NOT NULL");
    }
}
