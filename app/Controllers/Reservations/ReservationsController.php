<?php

namespace App\Controllers\Reservations;

use App\Models\BookModel;
use App\Models\BookReservationModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;

class ReservationsController extends ResourceController
{
    protected BookReservationModel $reservationModel;
    protected MemberModel $memberModel;
    protected BookModel $bookModel;

    public function __construct()
    {
        $this->reservationModel = new BookReservationModel();
        $this->memberModel      = new MemberModel();
        $this->bookModel        = new BookModel();
    }

    /**
     * Display all reservations
     */
    public function index()
    {
        $statusFilter = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $reservations = $this->reservationModel->getAllReservations($statusFilter, $search);

        $bookItemModel = new \App\Models\BookItemModel();

        foreach ($reservations as &$r) {
            $r['tier'] = MemberModel::getTierDetails([
                'donated_books_count' => $r['donated_books_count'] ?? 0,
                'manual_tier'         => $r['manual_tier'] ?? 'none',
            ]);

            $r['available_stock'] = $bookItemModel->where([
                'book_id' => $r['book_id'],
                'status'  => 'tersedia',
                'deleted_at' => null
            ])->countAllResults();
        }

        $data = [
            'reservations' => $reservations,
            'statusFilter' => $statusFilter,
            'search'       => $search,
            'allMembers'   => $this->memberModel->findAll(),
            'allBooks'     => $this->bookModel->findAll(),
        ];

        return view('reservations/index', $data);
    }

    /**
     * Store a new reservation
     */
    public function store()
    {
        $memberId = $this->request->getPost('member_id');
        $bookId   = $this->request->getPost('book_id');

        $member = $this->memberModel->find($memberId);
        $book   = $this->bookModel->find($bookId);

        if (empty($member) || empty($book)) {
            session()->setFlashdata(['msg' => 'Anggota atau Buku tidak ditemukan.', 'error' => true]);
            return redirect()->back();
        }

        $tier = MemberModel::getTierDetails($member);

        // Check if member is eligible to book (Gold or Platinum)
        if (!$tier['can_book']) {
            session()->setFlashdata([
                'msg'   => "Fungsi Booking/Reservasi hanya diperbolehkan untuk Gold Member dan Platinum Member. Anggota ini berstatus {$tier['name']}.",
                'error' => true
            ]);
            return redirect()->back();
        }

        // Check if member already has a pending reservation for this book
        $existing = $this->reservationModel->where([
            'member_id' => $memberId,
            'book_id'   => $bookId,
            'status'    => 'pending'
        ])->first();

        if (!empty($existing)) {
            session()->setFlashdata(['msg' => 'Anggota ini sudah memiliki antrean booking aktif untuk buku ini.', 'error' => true]);
            return redirect()->back();
        }

        $this->reservationModel->save([
            'member_id' => $memberId,
            'book_id'   => $bookId,
            'status'    => 'pending',
        ]);

        session()->setFlashdata(['msg' => "Berhasil melakukan booking buku '{$book['title']}' untuk {$member['first_name']} {$member['last_name']} ({$tier['name']})."]);
        return redirect()->to("admin/books/{$book['slug']}");
    }

    /**
     * Cancel a reservation
     */
    public function cancel($id = null)
    {
        $reservation = $this->reservationModel->find($id);
        if (empty($reservation)) {
            throw new PageNotFoundException('Reservasi tidak ditemukan');
        }

        $this->reservationModel->update($id, ['status' => 'cancelled']);
        session()->setFlashdata(['msg' => 'Booking buku berhasil dibatalkan.']);
        return redirect()->back();
    }

    public function fulfill($id = null)
    {
        $reservation = $this->reservationModel->find($id);
        if (empty($reservation)) {
            throw new PageNotFoundException('Reservasi tidak ditemukan');
        }

        $member = $this->memberModel->find($reservation['member_id']);
        $book   = $this->bookModel
            ->select('books.*, book_stock.quantity, categories.name as category, racks.name as rack, racks.floor')
            ->join('book_stock', 'books.id = book_stock.book_id', 'LEFT')
            ->join('categories', 'books.category_id = categories.id', 'LEFT')
            ->join('racks', 'books.rack_id = racks.id', 'LEFT')
            ->where('books.id', $reservation['book_id'])
            ->first();

        if (empty($member) || empty($book)) {
            session()->setFlashdata(['msg' => 'Anggota atau Buku untuk booking ini tidak ditemukan.', 'error' => true]);
            return redirect()->back();
        }

        // Mark reservation as fulfilled
        $this->reservationModel->update($id, ['status' => 'fulfilled']);

        // Prepare data for Step 3 Loan Creation View (loans/create.php)
        $loansController = new \App\Controllers\Loans\LoansController();
        $bookItemModel   = new \App\Models\BookItemModel();

        $book['stock'] = $loansController->getRemainingBookStocks($book);
        $book['available_items'] = $bookItemModel->where([
            'book_id'   => $book['id'],
            'status'    => 'tersedia',
            'condition' => 'baik'
        ])->findAll();

        $data = [
            'books'      => [$book],
            'member'     => $member,
            'validation' => \Config\Services::validation(),
            'oldInput'   => null,
        ];

        session()->setFlashdata(['msg' => "Booking untuk {$member['first_name']} {$member['last_name']} berhasil diselesaikan. Silakan tentukan durasi dan simpan transaksi peminjaman."]);
        return view('loans/create', $data);
    }

    /**
     * Permanently delete a reservation record
     */
    public function delete($id = null)
    {
        $reservation = $this->reservationModel->find($id);
        if (empty($reservation)) {
            throw new PageNotFoundException('Reservasi tidak ditemukan');
        }

        $this->reservationModel->delete($id);
        session()->setFlashdata(['msg' => 'Data booking berhasil dihapus permanen.']);
        return redirect()->back();
    }
}
