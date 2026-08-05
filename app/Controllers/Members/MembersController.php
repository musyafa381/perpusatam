<?php

namespace App\Controllers\Members;

use App\Libraries\QRGenerator;
use App\Models\BookModel;
use App\Models\BookStockModel;
use App\Models\FineModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

class MembersController extends ResourceController
{
    protected MemberModel $memberModel;
    protected BookModel $bookModel;
    protected BookStockModel $bookStockModel;
    protected LoanModel $loanModel;
    protected FineModel $fineModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel;
        $this->bookModel = new BookModel;
        $this->bookStockModel = new BookStockModel;
        $this->loanModel = new LoanModel;
        $this->fineModel = new FineModel;

        helper('upload');
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $itemPerPage = 20;
        $search = $this->request->getGet('search');
        $typeFilter = $this->request->getGet('type');
        $institutionFilter = $this->request->getGet('institution');

        $query = $this->memberModel;

        if (!empty($typeFilter)) {
            $query->where('member_type', $typeFilter);
        }

        if (!empty($institutionFilter)) {
            $query->where('institution', $institutionFilter);
        }

        if (!empty($search)) {
            $query->groupStart()
                ->like('first_name', $search, insensitiveSearch: true)
                ->orLike('last_name', $search, insensitiveSearch: true)
                ->orLike('email', $search, insensitiveSearch: true)
                ->orLike('institution', $search, insensitiveSearch: true)
                ->orLike('class_level', $search, insensitiveSearch: true)
                ->orLike('uid', $search, insensitiveSearch: true)
                ->groupEnd();
        }

        $members = $query->paginate($itemPerPage, 'members');
        $members = array_filter($members, function ($member) {
            return $member['deleted_at'] == null;
        });

        $data = [
            'members'           => $members,
            'pager'             => $this->memberModel->pager,
            'currentPage'       => $this->request->getVar('page_categories') ?? 1,
            'itemPerPage'       => $itemPerPage,
            'search'            => $search,
            'typeFilter'        => $typeFilter,
            'institutionFilter' => $institutionFilter,
        ];

        return view('members/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        $loans = $this->loanModel->where([
            'member_id' => $member['id'],
            'return_date' => null
        ])->findAll();

        $fines = $this->loanModel
            ->select('loans.id, fines.amount_paid, fines.fine_amount, fines.paid_at')
            ->join('fines', 'loans.id=fines.loan_id', 'LEFT')
            ->where('member_id', $member['id'])->findAll();

        $totakBooksLent = empty($loans) ? 0 : array_reduce(
            array_map(function ($loan) {
                return $loan['quantity'];
            }, $loans),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        $return = array_filter($loans, function ($loan) {
            return $loan['return_date'] != null;
        });

        $lateLoans = array_filter($loans, function ($loan) {
            return $loan['return_date'] == null && Time::now()->isAfter(Time::parse($loan['due_date']));
        });

        $totalFines = array_reduce(
            array_map(function ($fine) {
                return $fine['fine_amount'];
            }, $fines),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        $paidFines = array_reduce(
            array_map(function ($fine) {
                return $fine['amount_paid'];
            }, $fines),
            function ($carry, $item) {
                return ($carry + $item);
            }
        );

        $unpaidFines = $totalFines - $paidFines;

        // Create qr code if not exist
        if (!file_exists(MEMBERS_QR_CODE_PATH . $member['qr_code']) || empty($member['qr_code'])) {
            $qrGenerator = new QRGenerator();
            $qrCodeLabel = $member['first_name'] . ($member['last_name'] ? ' ' . $member['last_name'] : '');
            $qrCode = $qrGenerator->generateQRCode(
                $member['uid'],
                labelText: $qrCodeLabel,
                dir: MEMBERS_QR_CODE_PATH,
                filename: $qrCodeLabel
            );

            $this->memberModel->update($member['id'], ['qr_code' => $qrCode]);
            $member = $this->memberModel->where('uid', $uid)->first();
        }

        $bookItemModel = new \App\Models\BookItemModel();
        $donatedItems = $bookItemModel
            ->select('book_items.*, books.title, books.slug, books.author, books.year, books.book_cover, racks.name as rack_name')
            ->join('books', 'book_items.book_id = books.id', 'LEFT')
            ->join('racks', 'book_items.rack_id = racks.id', 'LEFT')
            ->where('book_items.donated_by_member_id', $member['id'])
            ->findAll();

        $tierDetails = MemberModel::getTierDetails($member);

        $data = [
            'member'            => $member,
            'tierDetails'       => $tierDetails,
            'donatedItems'      => $donatedItems,
            'totalBooksLent'    => $totakBooksLent,
            'loanCount'         => count($loans),
            'returnCount'       => count($return),
            'lateCount'         => count($lateLoans),
            'unpaidFines'       => $unpaidFines,
            'paidFines'         => $paidFines,
        ];

        return view('members/show', $data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        return view('members/create', [
            'validation' => \Config\Services::validation()
        ]);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $memberType = $this->request->getVar('member_type') ?? 'siswa';

        $rules = [
            'uid'                 => 'required|is_unique[members.uid]|max_length[100]',
            'member_type'         => 'required|in_list[siswa,petugas]',
            'first_name'          => 'required|string|max_length[100]',
            'last_name'           => 'permit_empty|string|max_length[100]',
            'gender'              => 'required|string',
            'manual_tier'         => 'permit_empty|in_list[none,living_library,silver,gold,platinum]',
            'donated_books_count' => 'permit_empty|numeric',
        ];


        if ($memberType === 'siswa') {
            $rules['institution']   = 'required|in_list[MTs,MA,SMK,PAUD,PDF,Ma\'had Aly]';
            $rules['class_level']   = 'required|string|max_length[64]';
            $rules['email']         = 'permit_empty|valid_email|max_length[255]';
            $rules['phone']         = 'permit_empty|string|max_length[20]';
            $rules['address']       = 'permit_empty|string|max_length[511]';
            $rules['date_of_birth'] = 'permit_empty|valid_date';
        } else {
            $rules['email']         = 'required|valid_email|max_length[255]';
            $rules['phone']         = 'required|string|min_length[4]|max_length[20]';
            $rules['address']       = 'permit_empty|string|max_length[511]';
            $rules['date_of_birth'] = 'permit_empty|valid_date';
        }

        if (!$this->validate($rules)) {
            $data = [
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ];

            return view('members/create', $data);
        }

        $emailVal = $this->request->getVar('email') ?: ('student_' . time() . '_' . rand(100, 999) . '@perpus.local');
        $phoneVal = $this->request->getVar('phone') ?: '-';

        $inputUid = trim($this->request->getVar('uid') ?? '');
        $uid = !empty($inputUid) ? $inputUid : ('MBR' . date('Ymd') . rand(1000, 9999));

        $qrGenerator = new QRGenerator();
        $qrCodeLabel = $this->request->getVar('first_name')
            . ($this->request->getVar('last_name') ? ' ' . $this->request->getVar('last_name') : '');
        $qrCode = $qrGenerator->generateQRCode(
            data: $uid,
            labelText: $qrCodeLabel,
            dir: MEMBERS_QR_CODE_PATH,
            filename: $qrCodeLabel
        );

        if (!$this->memberModel->save([
            'uid'                 => $uid,
            'member_type'         => $memberType,
            'first_name'          => $this->request->getVar('first_name'),
            'last_name'           => $this->request->getVar('last_name'),
            'email'               => $this->request->getVar('email'),
            'phone'               => $this->request->getVar('phone'),
            'address'             => $this->request->getVar('address'),
            'date_of_birth'       => $this->request->getVar('date_of_birth'),
            'gender'              => $this->request->getVar('gender'),
            'institution'         => $memberType === 'siswa' ? $this->request->getVar('institution') : null,
            'class_level'         => $memberType === 'siswa' ? $this->request->getVar('class_level') : null,
            'manual_tier'         => $this->request->getPost('manual_tier') ?? $this->request->getVar('manual_tier') ?? 'none',
            'donated_books_count' => (int)($this->request->getVar('donated_books_count') ?? 0),
            'qr_code'             => $qrCode
        ])) {


            $data = [
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ];

            session()->setFlashdata(['msg' => 'Insert failed']);
            return view('members/create', $data);
        }

        session()->setFlashdata(['msg' => 'Penambahan anggota baru berhasil']);
        return redirect()->to('admin/members/' . $uid);
    }


    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        $data = [
            'member'     => $member,
            'validation' => \Config\Services::validation(),
        ];

        return view('members/edit', $data);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        $memberType = $this->request->getVar('member_type') ?? ($member['member_type'] ?? 'siswa');

        $rules = [
            'uid'                 => "required|is_unique[members.uid,id,{$member['id']}]|max_length[100]",
            'member_type'         => 'required|in_list[siswa,petugas]',
            'first_name'          => 'required|string|max_length[100]',
            'last_name'           => 'permit_empty|string|max_length[100]',
            'gender'              => 'required|string',
            'donated_books_count' => 'permit_empty|numeric',
        ];

        if ($memberType === 'siswa') {
            $rules['institution']   = 'required|in_list[MTs,MA,SMK,PAUD,PDF,Ma\'had Aly]';
            $rules['class_level']   = 'required|string|max_length[64]';
            $rules['email']         = 'permit_empty|valid_email|max_length[255]';
            $rules['phone']         = 'permit_empty|string|max_length[20]';
            $rules['address']       = 'permit_empty|string|max_length[511]';
            $rules['date_of_birth'] = 'permit_empty|valid_date';
        } else {
            $rules['email']         = 'required|valid_email|max_length[255]';
            $rules['phone']         = 'required|string|min_length[4]|max_length[20]';
            $rules['address']       = 'permit_empty|string|max_length[511]';
            $rules['date_of_birth'] = 'permit_empty|valid_date';
        }

        if (!$this->validate($rules)) {
            $data = [
                'member'     => $member,
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ];

            return view('members/edit', $data);
        }

        $firstName = $this->request->getVar('first_name');
        $email = $this->request->getVar('email');
        $phone = $this->request->getVar('phone');
        $gender = $this->request->getVar('gender');

        $inputUid = trim($this->request->getVar('uid') ?? '');
        $newUid = !empty($inputUid) ? $inputUid : $member['uid'];

        $saveData = [
            'id'                  => $member['id'],
            'uid'                 => $newUid,
            'member_type'         => $memberType,
            'first_name'          => $this->request->getVar('first_name'),
            'last_name'           => $this->request->getVar('last_name'),
            'email'               => $this->request->getVar('email'),
            'phone'               => $this->request->getVar('phone'),
            'address'             => $this->request->getVar('address'),
            'date_of_birth'       => $this->request->getVar('date_of_birth'),
            'gender'              => $this->request->getVar('gender'),
            'institution'         => $memberType === 'siswa' ? $this->request->getVar('institution') : null,
            'class_level'         => $memberType === 'siswa' ? $this->request->getVar('class_level') : null,
            'manual_tier'         => $this->request->getPost('manual_tier') ?? $this->request->getVar('manual_tier') ?? 'none',
            'donated_books_count' => (int)($this->request->getVar('donated_books_count') ?? 0),
        ];

        if (!$this->memberModel->update($member['id'], $saveData)) {



            $data = [
                'member'     => $member,
                'validation' => \Config\Services::validation(),
                'oldInput'   => $this->request->getVar(),
            ];

            session()->setFlashdata(['msg' => 'Update member failed']);
            return view('members/edit', $data);
        }

        session()->setFlashdata(['msg' => 'Perubahan data anggota berhasil disimpan']);
        return redirect()->to('admin/members');
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($uid = null)
    {
        $member = $this->memberModel->where('uid', $uid)->first();

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        if (!$this->memberModel->delete($member['id'])) {
            session()->setFlashdata(['msg' => 'Failed to delete member', 'error' => true]);
            return redirect()->back();
        }

        deleteMembersQRCode($member['qr_code']);

        session()->setFlashdata(['msg' => 'Member deleted successfully']);
        return redirect()->to('admin/members');
    }

    /**
     * Display members who hold a membership card (Silver, Gold, Platinum - donated >= 3 books OR assigned manually)
     */
    public function cards()
    {
        $tierFilter = $this->request->getGet('tier'); // silver, gold, platinum
        $search = $this->request->getGet('search');

        $query = $this->memberModel;

        if (empty($tierFilter)) {
            $query->groupStart()
                ->where('donated_books_count >=', 3)
                ->orWhereIn('manual_tier', ['living_library', 'silver', 'gold', 'platinum'])
            ->groupEnd();
        }



        if (!empty($search)) {
            $query->groupStart()
                ->like('first_name', $search, insensitiveSearch: true)
                ->orLike('last_name', $search, insensitiveSearch: true)
                ->orLike('email', $search, insensitiveSearch: true)
                ->orLike('uid', $search, insensitiveSearch: true)
                ->groupEnd();
        }

        $allMembersList = $query->findAll();
        $members = [];

        foreach ($allMembersList as $m) {
            $m['donated_books_count'] = max((int)($m['donated_books_count'] ?? 0), MemberModel::getDonatedBooksCount($m['id']));
            $m['tier'] = MemberModel::getTierDetails($m);
            if (!empty($tierFilter)) {
                if ($m['tier']['code'] !== $tierFilter) {
                    continue;
                }
            }
            $members[] = $m;
        }

        $data = [
            'members'    => $members,
            'allMembers' => $this->memberModel->findAll(),
            'tierFilter' => $tierFilter,
            'search'     => $search,
        ];

        return view('members/cards', $data);
    }

    /**
     * Manually assign or override member tier
     */
    public function assignManualTier()
    {
        $memberId = $this->request->getPost('member_id');
        $manualTier = $this->request->getPost('manual_tier');

        $member = $this->memberModel->find($memberId);
        if (empty($member)) {
            session()->setFlashdata(['msg' => 'Anggota tidak ditemukan', 'error' => true]);
            return redirect()->back();
        }

        $validTiers = ['none', 'living_library', 'silver', 'gold', 'platinum'];
        $manualTier = in_array($manualTier, $validTiers) ? $manualTier : 'none';

        $this->memberModel->update($memberId, [
            'manual_tier' => $manualTier
        ]);


        session()->setFlashdata(['msg' => 'Penetapan tingkatan member manual berhasil diperbarui.']);
        return redirect()->to("admin/members/cards/{$memberId}");
    }

    /**
     * Display member card detail page with card design, privileges, and print/delivery status dropdowns
     */
    public function showCard($id = null)
    {
        $member = $this->memberModel->find($id);

        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        $member['donated_books_count'] = max((int)($member['donated_books_count'] ?? 0), MemberModel::getDonatedBooksCount($member['id']));
        $tierDetails = MemberModel::getTierDetails($member);

        $bookItemModel = new \App\Models\BookItemModel();
        $donatedItems = $bookItemModel
            ->select('book_items.*, books.title, books.year, racks.name as rack_name')
            ->join('books', 'book_items.book_id = books.id', 'LEFT')
            ->join('racks', 'book_items.rack_id = racks.id', 'LEFT')
            ->where('book_items.donated_by_member_id', $member['id'])
            ->findAll();

        $data = [
            'member'       => $member,
            'tier'         => $tierDetails,
            'donatedItems' => $donatedItems,
        ];

        return view('members/card_detail', $data);
    }

    /**
     * Update card print status and delivery status
     */
    public function updateCardStatus($id = null)
    {
        $member = $this->memberModel->find($id);
        if (empty($member)) {
            throw new PageNotFoundException('Member not found');
        }

        $printStatus = $this->request->getPost('card_print_status');
        $deliveryStatus = $this->request->getPost('card_delivery_status');
        $manualTier = $this->request->getPost('manual_tier');

        $updateData = [
            'card_print_status'    => in_array($printStatus, ['belum_dicetak', 'sudah_dicetak']) ? $printStatus : 'belum_dicetak',
            'card_delivery_status' => in_array($deliveryStatus, ['menunggu', 'sudah_diberikan']) ? $deliveryStatus : 'menunggu',
        ];

        if ($manualTier !== null && in_array($manualTier, ['none', 'living_library', 'silver', 'gold', 'platinum'])) {
            $updateData['manual_tier'] = $manualTier;
        }


        $this->memberModel->update($id, $updateData);

        session()->setFlashdata(['msg' => 'Status dan penetapan kartu member berhasil diperbarui.']);
        return redirect()->to("admin/members/cards/{$id}");
    }
}
