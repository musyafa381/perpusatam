<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCopyTypeToBookItemsTable extends Migration
{
    public function up()
    {
        $fields = [
            'copy_type' => [
                'type'       => 'ENUM',
                'constraint' => ['fisik', 'non_fisik'],
                'default'    => 'fisik',
                'after'      => 'condition',
            ],
        ];

        $this->forge->addColumn('book_items', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('book_items', ['copy_type']);
    }
}
