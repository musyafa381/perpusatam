<?php

namespace App\Controllers;

use App\Models\MemberModel;
use App\Models\VisitorLogModel;
use App\Models\VisitorSessionModel;
use CodeIgniter\Controller;

class VisitorController extends Controller
{
    protected MemberModel $memberModel;
    protected VisitorLogModel $visitorLogModel;
    protected VisitorSessionModel $visitorSessionModel;

    public function __construct()
    {
        $this->memberModel          = new MemberModel();
        $this->visitorLogModel      = new VisitorLogModel();
        $this->visitorSessionModel  = new VisitorSessionModel();
    }

    /**
     * Halaman Kiosk / Buku Tamu Publik
     */
    public function index()
    {
        $activeSession = $this->visitorSessionModel->getActiveSession();

        $data = [
            'activeSession' => $activeSession,
            'todayLogs'     => $this->visitorLogModel->getTodayLogs($activeSession['id'] ?? null),
            'todayCount'    => $this->visitorLogModel->getTodayCount(),
        ];

        return view('visitor/index', $data);
    }

    /**
     * AJAX Search Member untuk autocomplete di halaman publik
     */
    public function searchMember()
    {
        $q = trim($this->request->getGet('q') ?? '');
        if (empty($q)) {
            return $this->response->setJSON([]);
        }

        $members = $this->memberModel
            ->select('id, uid, first_name, last_name, institution, class_level, member_type')
            ->groupStart()
                ->like('first_name', $q, insensitiveSearch: true)
                ->orLike('last_name', $q, insensitiveSearch: true)
                ->orLike('uid', $q, insensitiveSearch: true)
                ->orLike('institution', $q, insensitiveSearch: true)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        $result = [];
        foreach ($members as $m) {
            $fullName = trim($m['first_name'] . ' ' . ($m['last_name'] ?? ''));
            $meta = [];
            if (!empty($m['institution'])) $meta[] = $m['institution'];
            if (!empty($m['class_level'])) $meta[] = $m['class_level'];
            $metaStr = !empty($meta) ? ' (' . implode(' - ', $meta) . ')' : '';

            $result[] = [
                'id'          => $m['id'],
                'uid'         => $m['uid'],
                'name'        => $fullName,
                'institution' => $m['institution'] ?? '',
                'class_level' => $m['class_level'] ?? '',
                'label'       => "{$fullName}{$metaStr} - [{$m['uid']}]",
            ];
        }

        return $this->response->setJSON($result);
    }

    /**
     * Submit Checkin Buku Tamu (Scan Barcode / Select Name)
     */
    public function checkin()
    {
        // 1. Cek ketersediaan Sesi Aktif
        $activeSession = $this->visitorSessionModel->getActiveSession();

        if (!$activeSession) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Sesi kunjungan perpustakaan saat ini sedang <strong>DITUTUP</strong> oleh Petugas. Silakan hubungi Petugas Perpustakaan untuk membuka sesi baru!'
            ]);
        }

        $input = trim($this->request->getPost('search_input') ?? $this->request->getPost('uid') ?? '');

        if (empty($input)) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Silakan masukkan atau scan Barcode UID / nama siswa!'
            ]);
        }

        // 2. Cek berdasarkan UID unik terlebih dahulu
        $member = $this->memberModel->where('uid', $input)->first();

        // 3. Jika tidak ketemu UID, coba cari berdasarkan ID Anggota atau Nama Lengkap
        if (!$member && is_numeric($input)) {
            $member = $this->memberModel->find($input);
        }

        if (!$member) {
            $member = $this->memberModel
                ->groupStart()
                    ->like('first_name', $input, insensitiveSearch: true)
                    ->orLike('last_name', $input, insensitiveSearch: true)
                ->groupEnd()
                ->first();
        }

        if ($member) {
            $visitorName = trim($member['first_name'] . ' ' . ($member['last_name'] ?? ''));
            $inst = $member['institution'] ?? '-';
            $cls  = $member['class_level'] ?? '-';
            $memberId = $member['id'];
            $uid = $member['uid'];
        } else {
            // Pengunjung Tamu / Non-Anggota yang mengetik nama langsung
            $visitorName = $input;
            $inst = 'Tamu / Umum';
            $cls  = '-';
            $memberId = null;
            $uid = null;
        }

        // 4. Validasi Kehadiran Ganda Per Sesi (1 UID Hanya Bisa 1x di Sesi Aktif)
        $hasCheckedIn = $this->visitorLogModel->hasCheckedInInSession(
            sessionId: $activeSession['id'],
            memberId: $memberId,
            uid: $uid,
            name: $visitorName
        );

        if ($hasCheckedIn) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => "Pengunjung <strong>{$visitorName}</strong> sudah mencatatkan presensi pada <strong>{$activeSession['session_name']}</strong> hari ini!"
            ]);
        }

        // 5. Simpan Data Presensi
        $now = date('Y-m-d H:i:s');
        $this->visitorLogModel->insert([
            'session_id'   => $activeSession['id'],
            'member_id'    => $memberId,
            'visitor_name' => $visitorName,
            'institution'  => $inst,
            'class_level'  => $cls,
            'uid'          => $uid,
            'created_at'   => $now,
        ]);

        $todayCount = $this->visitorLogModel->getTodayCount();

        return $this->response->setJSON([
            'status'       => true,
            'message'      => "Selamat Datang, <strong>{$visitorName}</strong>!",
            'visitor_name' => $visitorName,
            'session_name' => $activeSession['session_name'],
            'institution'  => $inst,
            'class_level'  => $cls,
            'time'         => date('H:i'),
            'todayCount'   => $todayCount,
        ]);
    }

    /**
     * Halaman Rekapitulasi Kunjungan & Manajemen Sesi di Panel Admin
     */
    public function adminIndex()
    {
        $activeTab   = $this->request->getGet('tab') ?? 'visitors';
        $startDate   = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate     = $this->request->getGet('end_date') ?? date('Y-m-d');
        $institution = $this->request->getGet('institution');
        $itemPerPage = 15;

        $logs = $this->visitorLogModel->getLogsFiltered($startDate, $endDate, $institution, $itemPerPage);
        $activeSession = $this->visitorSessionModel->getActiveSession();
        $todaySessions = $this->visitorSessionModel->getTodaySessions();

        // Fetch Reservations for Tab 2
        $reservationModel = new \App\Models\BookReservationModel();
        $statusFilter     = $this->request->getGet('status');
        $searchRes        = $this->request->getGet('search');
        $reservations     = $reservationModel->getAllReservations($statusFilter, $searchRes);

        $bookItemModel = new \App\Models\BookItemModel();
        foreach ($reservations as &$r) {
            $r['tier'] = \App\Models\MemberModel::getTierDetails([
                'donated_books_count' => $r['donated_books_count'] ?? 0,
                'manual_tier'         => $r['manual_tier'] ?? 'none',
            ]);

            $r['available_stock'] = $bookItemModel->where([
                'book_id'    => $r['book_id'],
                'status'     => 'tersedia',
                'deleted_at' => null
            ])->countAllResults();
        }
        unset($r);

        $data = [
            'activeTab'     => $activeTab,
            'logs'          => $logs,
            'pager'         => $this->visitorLogModel->pager,
            'currentPage'   => $this->request->getVar('page_visitors') ?? 1,
            'itemPerPage'   => $itemPerPage,
            'startDate'     => $startDate,
            'endDate'       => $endDate,
            'institution'   => $institution,
            'todayCount'    => $this->visitorLogModel->getTodayCount(),
            'activeSession' => $activeSession,
            'todaySessions' => $todaySessions,
            'reservations'  => $reservations,
            'statusFilter'  => $statusFilter,
            'search'        => $searchRes,
        ];

        return view('visitor/admin_index', $data);
    }

    /**
     * Admin: Buka Sesi Kunjungan Baru
     */
    public function openSession()
    {
        $sessionName = trim($this->request->getPost('session_name') ?? '');

        if (empty($sessionName)) {
            return redirect()->back()->with('msg', 'Nama Sesi harus diisi!')->with('error', true);
        }

        // Deaktifkan semua sesi hari ini sebelum membuka sesi baru
        $this->visitorSessionModel->deactivateAllTodaySessions();

        // Buat sesi baru
        $this->visitorSessionModel->insert([
            'session_name' => $sessionName,
            'session_date' => date('Y-m-d'),
            'is_active'    => 1,
        ]);

        return redirect()->back()->with('msg', "Sesi Kunjungan '{$sessionName}' berhasil DIBUKA!");
    }

    /**
     * Admin: Tutup Sesi Kunjungan
     */
    public function closeSession($id)
    {
        $session = $this->visitorSessionModel->find($id);

        if ($session) {
            $this->visitorSessionModel->update($id, ['is_active' => 0]);
            return redirect()->back()->with('msg', "Sesi Kunjungan '{$session['session_name']}' berhasil DITUTUP.");
        }

        return redirect()->back()->with('msg', 'Sesi tidak ditemukan!')->with('error', true);
    }
}
