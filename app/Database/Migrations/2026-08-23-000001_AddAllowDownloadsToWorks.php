<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAllowDownloadsToWorks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('works', [
            'allow_downloads' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'purchase_price',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('works', ['allow_downloads']);
    }
}
