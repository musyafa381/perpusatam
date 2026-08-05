<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDonatedByToBookItemsTable extends Migration
{
    public function up()
    {
        $fields = [
            'donated_by_member_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'rack_id',
            ],
        ];

        $this->forge->addColumn('book_items', $fields);

        $this->db->query("ALTER TABLE book_items ADD CONSTRAINT fk_book_items_donated_by FOREIGN KEY (donated_by_member_id) REFERENCES members(id) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE book_items DROP FOREIGN KEY fk_book_items_donated_by");
        $this->forge->dropColumn('book_items', ['donated_by_member_id']);
    }
}
