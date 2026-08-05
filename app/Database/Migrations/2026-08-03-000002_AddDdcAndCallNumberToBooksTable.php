<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDdcAndCallNumberToBooksTable extends Migration
{
    public function up()
    {
        $fields = [
            'ddc' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'synopsis',
            ],
            'call_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'ddc',
            ],
        ];

        $this->forge->addColumn('books', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('books', ['ddc', 'call_number']);
    }
}
