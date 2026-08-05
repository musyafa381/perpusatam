<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        // Table `settings` already exists with fields: id, class, key, value, type, context, created_at, updated_at
        // If not exists, create it
        if (!$this->db->tableExists('settings')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'class' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => 'Library',
                ],
                'key' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'value' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'string',
                ],
                'context' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('settings', true);
        }

        $defaultSettings = [
            'female_open_days'         => '6,0,1',    // Sabtu (6), Minggu (0), Senin (1)
            'male_open_days'           => '2,3,4',    // Selasa (2), Rabu (3), Kamis (4)
            'general_closed_days'      => '5',        // Jumat (5)
            'apply_gender_schedule_to' => 'santri,siswa',
            'max_books_per_member'     => '2',
            'default_loan_duration'    => '7',
            'max_loan_extensions'      => '1',
            'fine_per_day'             => '1000',
            'max_fine_amount'          => '20000',
            'grace_period_days'        => '0',
            'damaged_book_fine'        => '5000',
            'special_holidays'         => '',
            'library_name'             => 'Perpustakaan Assalafiyyah',
            'library_address'          => 'Jl. Pesantren Assalafiyyah',
            'library_contact'          => '08123456789',
            'struk_footer_note'        => 'Terima kasih telah membaca. Jagalah buku dengan baik!',
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($defaultSettings as $k => $v) {
            $existing = $this->db->table('settings')
                ->where('class', 'Library')
                ->where('key', $k)
                ->get()
                ->getRowArray();

            if (!$existing) {
                $this->db->table('settings')->insert([
                    'class'      => 'Library',
                    'key'        => $k,
                    'value'      => $v,
                    'type'       => 'string',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down()
    {
        $this->db->table('settings')->where('class', 'Library')->delete();
    }
}
