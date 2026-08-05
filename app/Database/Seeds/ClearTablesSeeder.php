<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClearTablesSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->db->table('fines')->truncate();
        $this->db->table('loans')->truncate();
        $this->db->table('book_items')->truncate();
        $this->db->table('book_stock')->truncate();
        $this->db->table('book_reservations')->truncate();
        $this->db->table('books')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }
}
