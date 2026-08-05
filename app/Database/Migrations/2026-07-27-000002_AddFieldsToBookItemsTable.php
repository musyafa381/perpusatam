<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToBookItemsTable extends Migration
{
    public function up()
    {
        $fields = [
            'acquisition' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'default'    => 'beli',
                'after'      => 'status',
            ],
            'price' => [
                'type'       => 'INT',
                'default'    => 0,
                'after'      => 'acquisition',
            ],
            'rack_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'price',
            ],
        ];

        $this->forge->addColumn('book_items', $fields);

        // Add foreign key constraint for rack_id in book_items
        $this->db->query("ALTER TABLE book_items ADD CONSTRAINT fk_book_items_rack_id FOREIGN KEY (rack_id) REFERENCES racks(id) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE book_items DROP FOREIGN KEY fk_book_items_rack_id");
        $this->forge->dropColumn('book_items', ['acquisition', 'price', 'rack_id']);
    }
}
