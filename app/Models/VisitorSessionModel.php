<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorSessionModel extends Model
{
    protected $table            = 'visitor_sessions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_name',
        'session_date',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mendapatkan sesi aktif hari ini
     */
    public function getActiveSession()
    {
        return $this->where('session_date', date('Y-m-d'))
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * Menutup semua sesi aktif hari ini
     */
    public function deactivateAllTodaySessions()
    {
        return $this->where('session_date', date('Y-m-d'))
            ->set(['is_active' => 0])
            ->update();
    }

    /**
     * Mendapatkan daftar sesi hari ini
     */
    public function getTodaySessions()
    {
        return $this->where('session_date', date('Y-m-d'))
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
