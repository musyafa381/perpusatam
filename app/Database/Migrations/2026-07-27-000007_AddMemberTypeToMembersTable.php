<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMemberTypeToMembersTable extends Migration
{
    public function up()
    {
        $fields = [
            'member_type' => [
                'type'       => 'ENUM',
                'constraint' => ['siswa', 'petugas'],
                'default'    => 'siswa',
                'after'      => 'uid',
            ],
            'institution' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'gender',
            ],
            'class_level' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'institution',
            ],
        ];

        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['member_type', 'institution', 'class_level']);
    }
}
