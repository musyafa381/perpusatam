<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePublishersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 127
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'deleted_at TIMESTAMP NULL',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('publishers', true);
    }

    public function down()
    {
        $this->forge->dropTable('publishers', true);
    }
}
