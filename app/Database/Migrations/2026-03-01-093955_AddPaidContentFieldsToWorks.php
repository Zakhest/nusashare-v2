<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaidContentFieldsToWorks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('works', [
            'is_paid' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'status'
            ],
            'price' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'is_paid'
            ],
            'watermark_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'price'
            ],
            'timer_duration' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'watermark_text'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('works', ['is_paid', 'price', 'watermark_text', 'timer_duration']);
    }
}
