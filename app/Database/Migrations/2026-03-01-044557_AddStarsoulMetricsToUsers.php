<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStarsoulMetricsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'engagement' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'starsoul_status'
            ],
            'commitment' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'engagement'
            ],
            'behavior' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 15.50,
                'after'      => 'commitment'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['engagement', 'commitment', 'behavior']);
    }
}
