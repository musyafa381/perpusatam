<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConditionNoteAndLogsTable extends Migration
{
    public function up()
    {
        // 1. Add condition_note column to book_items table if not exists
        if (!$this->db->fieldExists('condition_note', 'book_items')) {
            $fields = [
                'condition_note' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                    'after'      => 'condition',
                ],
            ];
            $this->forge->addColumn('book_items', $fields);
        }

        // 2. Create book_item_condition_logs table if not exists
        if (!$this->db->tableExists('book_item_condition_logs')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'book_item_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                ],
                'loan_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'member_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'condition_state' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'default'    => 'baik',
                ],
                'condition_note' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'recorded_by' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
                'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL',
            ]);

            $this->forge->addPrimaryKey('id');
            $this->forge->addForeignKey('book_item_id', 'book_items', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('book_item_condition_logs', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('book_item_condition_logs')) {
            $this->forge->dropTable('book_item_condition_logs');
        }

        if ($this->db->fieldExists('condition_note', 'book_items')) {
            $this->forge->dropColumn('book_items', ['condition_note']);
        }
    }
}
