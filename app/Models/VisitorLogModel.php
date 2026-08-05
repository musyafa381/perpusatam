<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitorLogModel extends Model
{
    protected $table            = 'visitor_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'member_id',
        'session_id',
        'visitor_name',
        'institution',
        'class_level',
        'uid',
        'created_at'
    ];

    protected $useTimestamps = false;

    /**
     * Memeriksa apakah UID / Member / Nama sudah presensi di sesi aktif ini
     */
    public function hasCheckedInInSession(int $sessionId, ?int $memberId = null, ?string $uid = null, ?string $name = null): bool
    {
        $builder = $this->where('session_id', $sessionId);

        if (!empty($memberId)) {
            $builder->where('member_id', $memberId);
        } elseif (!empty($uid)) {
            $builder->where('uid', $uid);
        } elseif (!empty($name)) {
            $builder->where('visitor_name', $name);
        } else {
            return false;
        }

        return $builder->countAllResults() > 0;
    }

    public function getTodayLogs(?int $sessionId = null)
    {
        $today = date('Y-m-d');
        $builder = $this->select('visitor_logs.*, members.first_name, members.last_name, members.member_type, visitor_sessions.session_name')
            ->join('members', 'visitor_logs.member_id = members.id', 'left')
            ->join('visitor_sessions', 'visitor_logs.session_id = visitor_sessions.id', 'left')
            ->where('DATE(visitor_logs.created_at)', $today);

        if ($sessionId) {
            $builder->where('visitor_logs.session_id', $sessionId);
        }

        return $builder->orderBy('visitor_logs.created_at', 'DESC')->findAll();
    }

    public function getLogsFiltered(?string $startDate = null, ?string $endDate = null, ?string $institution = null, int $perPage = 20)
    {
        $this->select('visitor_logs.*, members.first_name, members.last_name, members.member_type, visitor_sessions.session_name')
            ->join('members', 'visitor_logs.member_id = members.id', 'left')
            ->join('visitor_sessions', 'visitor_logs.session_id = visitor_sessions.id', 'left');

        if (!empty($startDate)) {
            $this->where('DATE(visitor_logs.created_at) >=', $startDate);
        }
        if (!empty($endDate)) {
            $this->where('DATE(visitor_logs.created_at) <=', $endDate);
        }
        if (!empty($institution)) {
            $this->where('visitor_logs.institution', $institution);
        }

        return $this->orderBy('visitor_logs.created_at', 'DESC')->paginate($perPage, 'visitors');
    }

    public function getTodayCount(): int
    {
        return $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
    }
}
