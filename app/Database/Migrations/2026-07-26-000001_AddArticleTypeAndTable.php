<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddArticleTypeAndTable extends Migration
{
    public function up()
    {
        // 1. Tambah 'artikel' ke ENUM content_type di tabel works
        $this->forge->modifyColumn('works', [
            'content_type' => [
                'name'       => 'content_type',
                'type'       => 'ENUM',
                'constraint' => ['text', 'image', 'pdf', 'novel', 'light_novel', 'comic', 'artikel'],
                'default'    => 'novel',
                'null'       => false,
            ],
        ]);

        // 2. Buat tabel articles
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'work_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'null'       => false,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'body' => [
                'type' => 'LONGTEXT',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('work_id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('work_id', 'works', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('articles', true);
    }

    public function down()
    {
        // Hapus tabel articles
        $this->forge->dropTable('articles', true);

        // Rollback ENUM (hapus 'artikel')
        $this->forge->modifyColumn('works', [
            'content_type' => [
                'name'       => 'content_type',
                'type'       => 'ENUM',
                'constraint' => ['text', 'image', 'pdf', 'novel', 'light_novel', 'comic'],
                'default'    => 'novel',
                'null'       => false,
            ],
        ]);
    }
}
