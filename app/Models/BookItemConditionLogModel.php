<?php

namespace App\Models;

use CodeIgniter\Model;

class BookItemConditionLogModel extends Model
{
    protected $table            = 'book_item_condition_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'book_item_id',
        'loan_id',
        'member_id',
        'condition_state',
        'condition_note',
        'recorded_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get condition log history for a specific book_item_id
     */
    public function getLogsByItem(int $bookItemId)
    {
        return $this->select('book_item_condition_logs.*, members.first_name, members.last_name, members.uid as member_uid, users.username as staff_user')
            ->join('members', 'book_item_condition_logs.member_id = members.id', 'LEFT')
            ->join('users', 'book_item_condition_logs.recorded_by = users.id', 'LEFT')
            ->where('book_item_id', $bookItemId)
            ->orderBy('book_item_condition_logs.created_at', 'DESC')
            ->findAll();
    }
}
