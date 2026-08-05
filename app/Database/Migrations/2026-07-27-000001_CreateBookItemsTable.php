<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'book_id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
            ],
            'item_code' => [
                'type'           => 'VARCHAR',
                'constraint'     => 64,
            ],
            'condition' => [
                'type'           => 'ENUM',
                'constraint'     => ['baik', 'rusak', 'hilang'],
                'default'        => 'baik',
            ],
            'status' => [
                'type'           => 'ENUM',
                'constraint'     => ['tersedia', 'dipinjam', 'diperbaiki'],
                'default'        => 'tersedia',
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            'deleted_at TIMESTAMP NULL',
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('item_code');
        $this->forge->addForeignKey('book_id', 'books', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('book_items', true);

        // Add book_item_id column to loans table if not existing
        $fields = [
            'book_item_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'book_id',
            ],
        ];
        $this->forge->addColumn('loans', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('loans', 'book_item_id');
        $this->forge->dropTable('book_items');
    }
}
