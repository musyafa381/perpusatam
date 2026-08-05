<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddManualTierToMembersTable extends Migration
{
    public function up()
    {
        $fields = [
            'manual_tier' => [
                'type'       => 'ENUM',
                'constraint' => ['none', 'living_library', 'silver', 'gold', 'platinum'],
                'default'    => 'none',
                'after'      => 'card_delivery_status',
            ],

        ];

        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['manual_tier']);
    }
}
