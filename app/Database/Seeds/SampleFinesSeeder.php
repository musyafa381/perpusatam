<?php

namespace App\Database\Seeds;

use App\Models\BookItemModel;
use App\Models\BookModel;
use App\Models\FineModel;
use App\Models\LoanModel;
use App\Models\MemberModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class SampleFinesSeeder extends Seeder
{
    public function run()
    {
        $memberModel = new MemberModel();
        $bookModel   = new BookModel();
        $bookItemModel = new BookItemModel();
        $loanModel   = new LoanModel();
        $fineModel   = new FineModel();

        $member = $memberModel->first();
        $book   = $bookModel->first();
        if (!$member || !$book) {
            return;
        }

        $items = $bookItemModel->where('book_id', $book['id'])->findAll();
        $itemId = !empty($items[0]['id']) ? $items[0]['id'] : null;

        // 1. Contoh Denda Belum Lunas (Menunggak)
        $uidUnpaid = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $loanUnpaid = [
            'book_id'      => $book['id'],
            'book_item_id' => $itemId,
            'quantity'     => 1,
            'member_id'    => $member['id'],
            'uid'          => $uidUnpaid,
            'loan_date'    => Time::now()->subDays(10)->toDateTimeString(),
            'due_date'     => Time::now()->subDays(3)->toDateTimeString(),
            'return_date'  => Time::now()->subDays(1)->toDateTimeString(),
            'qr_code'      => null
        ];
        $loanModel->insert($loanUnpaid);
        $loanUnpaidId = $loanModel->getInsertID();

        $fineModel->save([
            'loan_id'     => $loanUnpaidId,
            'fine_amount' => 15000,
            'amount_paid' => 5000,
            'paid_at'     => null
        ]);

        // 2. Contoh Denda Sudah Lunas
        $uidPaid = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $loanPaid = [
            'book_id'      => $book['id'],
            'book_item_id' => $itemId,
            'quantity'     => 1,
            'member_id'    => $member['id'],
            'uid'          => $uidPaid,
            'loan_date'    => Time::now()->subDays(14)->toDateTimeString(),
            'due_date'     => Time::now()->subDays(7)->toDateTimeString(),
            'return_date'  => Time::now()->subDays(2)->toDateTimeString(),
            'qr_code'      => null
        ];
        $loanModel->insert($loanPaid);
        $loanPaidId = $loanModel->getInsertID();

        $fineModel->save([
            'loan_id'     => $loanPaidId,
            'fine_amount' => 25000,
            'amount_paid' => 25000,
            'paid_at'     => Time::now()->subDays(2)->toDateTimeString()
        ]);
    }
}
