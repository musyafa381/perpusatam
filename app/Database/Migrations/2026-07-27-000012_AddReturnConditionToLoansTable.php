<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReturnConditionToLoansTable extends Migration
{
    public function up()
    {
        $fields = [
            'return_condition' => [
                'type'       => 'ENUM',
                'constraint' => ['baik', 'rusak', 'hilang'],
                'default'    => 'baik',
                'after'      => 'return_date',
            ],
        ];

        $this->forge->addColumn('loans', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('loans', ['return_condition']);
    }
}
