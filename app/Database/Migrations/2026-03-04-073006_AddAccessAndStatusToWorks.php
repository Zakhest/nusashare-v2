<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccessAndStatusToWorks extends Migration
{
    public function up()
    {
        $fields = [
            'access_type' => [
                'type'       => 'ENUM',
                'constraint' => ['full', 'chapter'],
                'default'    => 'full',
                'after'      => 'content_type'
            ],
            'work_status' => [
                'type'       => 'ENUM',
                'constraint' => ['ongoing', 'ended'],
                'default'    => 'ongoing',
                'after'      => 'access_type'
            ],
        ];
        $this->forge->addColumn('works', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('works', ['access_type', 'work_status']);
    }
}
