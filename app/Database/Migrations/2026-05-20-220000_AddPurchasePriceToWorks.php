<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPurchasePriceToWorks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('works', [
            'purchase_price' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'price',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('works', ['purchase_price']);
    }
}
