<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateManualTierEnumInMembersTable extends Migration
{
    public function up()
    {
        $fields = [
            'manual_tier' => [
                'type'       => 'ENUM',
                'constraint' => ['none', 'living_library', 'silver', 'gold', 'platinum'],
                'default'    => 'none',
            ],
        ];

        $this->forge->modifyColumn('members', $fields);
    }

    public function down()
    {
        $fields = [
            'manual_tier' => [
                'type'       => 'ENUM',
                'constraint' => ['none', 'silver', 'gold', 'platinum'],
                'default'    => 'none',
            ],
        ];

        $this->forge->modifyColumn('members', $fields);
    }
}
