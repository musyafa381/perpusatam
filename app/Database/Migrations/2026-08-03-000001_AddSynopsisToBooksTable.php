<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSynopsisToBooksTable extends Migration
{
    public function up()
    {
        $fields = [
            'synopsis' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'category_id',
            ],
        ];

        $this->forge->addColumn('books', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('books', 'synopsis');
    }
}
