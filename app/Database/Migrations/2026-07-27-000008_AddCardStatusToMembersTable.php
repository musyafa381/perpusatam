<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCardStatusToMembersTable extends Migration
{
    public function up()
    {
        $fields = [
            'card_print_status' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_dicetak', 'sudah_dicetak'],
                'default'    => 'belum_dicetak',
                'after'      => 'class_level',
            ],
            'card_delivery_status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'sudah_diberikan'],
                'default'    => 'menunggu',
                'after'      => 'card_print_status',
            ],
        ];

        $this->forge->addColumn('members', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('members', ['card_print_status', 'card_delivery_status']);
    }
}
