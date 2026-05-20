<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddChapterIdToComments extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('chapter_id', 'comments')) {
            $this->forge->addColumn('comments', [
                'chapter_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'work_id',
                ],
            ]);

            $this->forge->addForeignKey('chapter_id', 'chapters', 'id', 'CASCADE', 'CASCADE');
            $this->forge->processIndexes('comments');
        }
    }

    public function down()
    {
        $this->forge->dropForeignKey('comments', 'comments_chapter_id_foreign');
        $this->forge->dropColumn('comments', 'chapter_id');
    }
}
