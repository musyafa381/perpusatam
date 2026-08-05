<?php

namespace App\Models;

use CodeIgniter\Model;

class BookReservationModel extends Model
{
    protected $table            = 'book_reservations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'member_id',
        'book_id',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get active reservations for a specific book with member details
     */
    public function getActiveReservationsForBook($bookId)
    {
        return $this->select('book_reservations.*, members.first_name, members.last_name, members.email, members.uid as member_uid, members.donated_books_count, members.manual_tier')
            ->join('members', 'book_reservations.member_id = members.id', 'LEFT')
            ->where('book_reservations.book_id', $bookId)
            ->where('book_reservations.status', 'pending')
            ->orderBy('book_reservations.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get all reservations with member and book details
     */
    public function getAllReservations($statusFilter = null, $search = null)
    {
        $builder = $this->select('book_reservations.*, members.first_name, members.last_name, members.email, members.uid as member_uid, members.donated_books_count, members.manual_tier, books.title as book_title, books.year as book_year, books.author as book_author, books.publisher as book_publisher, books.isbn as book_isbn, books.slug as book_slug, books.book_cover, categories.name as book_category, racks.name as book_rack, racks.floor as book_floor')
            ->join('members', 'book_reservations.member_id = members.id', 'LEFT')
            ->join('books', 'book_reservations.book_id = books.id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT');

        if (!empty($statusFilter)) {
            $builder->where('book_reservations.status', $statusFilter);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('members.first_name', $search, insensitiveSearch: true)
                ->orLike('members.last_name', $search, insensitiveSearch: true)
                ->orLike('books.title', $search, insensitiveSearch: true)
                ->orLike('members.uid', $search, insensitiveSearch: true)
                ->groupEnd();
        }

        return $builder->orderBy('book_reservations.created_at', 'DESC')->findAll();
    }
}
