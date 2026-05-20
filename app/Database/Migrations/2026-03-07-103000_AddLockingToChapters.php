<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLockingToChapters extends Migration
{
    public function up()
    {
        // Add fields to chapters table
        if (!$this->db->fieldExists('is_locked', 'chapters')) {
            $this->forge->addColumn('chapters', [
                'is_locked' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                    'after'      => 'status'
                ],
                'price' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                    'after'      => 'is_locked'
                ],
            ]);
        }

        // Create unlocked_chapters table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'chapter_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('chapter_id', 'chapters', 'id', 'CASCADE', 'CASCADE');
        if (!$this->db->tableExists('unlocked_chapters')) {
            $this->forge->createTable('unlocked_chapters');
        }
    }

    public function down()
    {
        $this->forge->dropTable('unlocked_chapters');
        $this->forge->dropColumn('chapters', ['is_locked', 'price']);
    }
}
