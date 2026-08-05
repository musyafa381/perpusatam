<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDonationsToMembersTable extends Migration
{
    public function up()
    {
        $fields = [
            'donated_books_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'gender',
            ],
        ];

        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['donated_books_count']);
    }
}
