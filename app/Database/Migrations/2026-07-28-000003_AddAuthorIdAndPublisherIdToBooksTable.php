<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthorIdAndPublisherIdToBooksTable extends Migration
{
    public function up()
    {
        $fields = [
            'author_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'title'
            ],
            'publisher_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'author_id'
            ],
        ];

        $this->forge->addColumn('books', $fields);

        // Populate authors and publishers from existing books data
        $db = \Config\Database::connect();
        $books = $db->table('books')->select('id, author, publisher')->get()->getResultArray();

        foreach ($books as $b) {
            $authorId = null;
            $publisherId = null;

            if (!empty(trim($b['author'] ?? ''))) {
                $authorName = trim($b['author']);
                $existingAuthor = $db->table('authors')->where('name', $authorName)->get()->getRowArray();
                if ($existingAuthor) {
                    $authorId = $existingAuthor['id'];
                } else {
                    $db->table('authors')->insert([
                        'name' => $authorName,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $authorId = $db->insertID();
                }
            }

            if (!empty(trim($b['publisher'] ?? ''))) {
                $publisherName = trim($b['publisher']);
                $existingPublisher = $db->table('publishers')->where('name', $publisherName)->get()->getRowArray();
                if ($existingPublisher) {
                    $publisherId = $existingPublisher['id'];
                } else {
                    $db->table('publishers')->insert([
                        'name' => $publisherName,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $publisherId = $db->insertID();
                }
            }

            if ($authorId || $publisherId) {
                $updateData = [];
                if ($authorId) $updateData['author_id'] = $authorId;
                if ($publisherId) $updateData['publisher_id'] = $publisherId;
                $db->table('books')->where('id', $b['id'])->update($updateData);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('books', ['author_id', 'publisher_id']);
    }
}
