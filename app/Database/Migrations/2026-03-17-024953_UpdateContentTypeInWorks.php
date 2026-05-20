<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateContentTypeInWorks extends Migration
{
    public function up()
    {
        // Modify content_type column to accept the new types
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

    public function down()
    {
        $this->forge->modifyColumn('works', [
            'content_type' => [
                'name'       => 'content_type',
                'type'       => 'ENUM',
                'constraint' => ['text', 'image', 'pdf'],
                'default'    => 'text',
                'null'       => false,
            ],
        ]);
    }
}
