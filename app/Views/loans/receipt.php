<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Struk Peminjaman Buku - <?= esc($loan['uid']); ?></title>
<style>
  .receipt-card {
    max-width: 480px;
    margin: 0 auto;
    background: #ffffff;
    border: 2px dashed #c59b27;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  }

  .receipt-divider {
    border-top: 2px dashed #cbd5e1;
    margin: 1rem 0;
  }

  @media print {
    body * {
      visibility: hidden !important;
    }
    .printable-receipt-area, .printable-receipt-area * {
      visibility: visible !important;
    }
    .printable-receipt-area {
      position: fixed !important;
      left: 50% !important;
      top: 20px !important;
      transform: translateX(-50%) !important;
      width: 100% !important;
      max-width: 450px !important;
      box-shadow: none !important;
      border: 1px solid #000 !important;
    }
    .no-print {
      display: none !important;
    }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use CodeIgniter\I18n\Time;

$loanDate = Time::parse($loan['loan_date'], locale: 'id');
$dueDate = Time::parse($loan['due_date'], locale: 'id');
?>

<div class="d-flex align-items-center justify-content-between mb-4 no-print">
  <div>
    <a href="<?= base_url('admin/loans'); ?>" class="btn btn-outline-secondary rounded-pill me-2">
      <i class="ti ti-arrow-left me-1"></i> Daftar Peminjaman
    </a>
    <a href="<?= base_url('admin/loans/new/members/search'); ?>" class="btn btn-pill-gold">
      <i class="ti ti-plus me-1"></i> Tambah Peminjaman Baru
    </a>
  </div>
  <div>
    <button type="button" class="btn btn-pill-gold fw-bold shadow-sm px-4" onclick="window.print();">
      <i class="ti ti-printer me-1"></i> Cetak Struk Transaksi
    </button>
  </div>
</div>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2 no-print">
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Printable Receipt Container -->
<div class="printable-receipt-area mb-5">
  <div class="card receipt-card p-4">
    <!-- Header Logo & Identity -->
    <div class="text-center mb-3">
      <div class="d-inline-flex align-items-center justify-content-center p-1 mb-2">
        <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan Assalafiyyah" class="shadow-sm" style="height: 52px; width: 52px; border-radius: 12px; object-fit: cover; border: 1px solid #e8decb;">
      </div>
      <h5 class="fw-bold text-dark mb-0 tracking-wide">PERPUSTAKAAN PUSAT</h5>
      <small class="text-muted fw-semibold">SEKOLAH ASSALAFIYYAH</small>
      <div class="mt-2">
        <span class="badge badge-subtle-primary px-3 py-1 fs-2 fw-bold text-uppercase">Struk Bukti Peminjaman Buku</span>
      </div>
    </div>


    <!-- 1D Barcode Widget -->
    <div class="text-center p-3 bg-light rounded-3 border mb-3">
      <div class="d-flex justify-content-center align-items-center mb-1 overflow-hidden" style="max-height: 55px;">
        <?= generateBarcodeSVG($loan['uid'], 50); ?>
      </div>
      <strong class="font-monospace text-dark fs-4 d-block tracking-wider">NO. TRX: <?= esc($loan['uid']); ?></strong>
      <small class="text-muted fs-1"><i class="ti ti-scan me-1"></i>Kode Barcode Transaksi 8-Digit</small>
    </div>

    <!-- Member Identity Info -->
    <div class="bg-light p-3 rounded-3 border fs-2 mb-3">
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted">Nama Peminjam:</span>
        <strong class="text-dark"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></strong>
      </div>
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted">UID / ID Card:</span>
        <span class="font-monospace fw-bold text-dark"><?= esc($loan['member_uid']); ?></span>
      </div>
      <div class="d-flex justify-content-between mb-1">
        <span class="text-muted">Waktu Pinjam:</span>
        <span class="text-dark fw-semibold"><?= $loanDate->toLocalizedString('d/MM/y HH:mm'); ?> WIB</span>
      </div>
      <div class="d-flex justify-content-between">
        <span class="text-muted">Batas Tenggat:</span>
        <strong class="text-primary"><?= $dueDate->toLocalizedString('d MMMM Y'); ?></strong>
      </div>
    </div>

    <div class="receipt-divider"></div>

    <!-- Borrowed Books List -->
    <div class="mb-3">
      <h6 class="fw-bold text-dark mb-2 fs-2"><i class="ti ti-books text-primary me-1"></i> Daftar Buku Dipinjam (<?= count($allSessionLoans); ?> Eksemplar):</h6>
      <div class="table-responsive">
        <table class="table table-sm table-borderless mb-0 fs-2">
          <thead>
            <tr class="border-bottom text-muted">
              <th>#</th>
              <th>Judul Buku</th>
              <th class="text-center">Eksemplar</th>
            </tr>
          </thead>
          <tbody>
            <?php $idx = 1; ?>
            <?php foreach ($allSessionLoans as $sBook) : ?>
              <tr class="border-bottom border-light">
                <td class="text-muted py-2"><?= $idx++; ?>.</td>
                <td class="py-2">
                  <strong class="text-dark d-block"><?= esc($sBook['book_title'] ?? $sBook['title']); ?></strong>
                  <small class="text-muted"><i class="ti ti-user me-1"></i><?= esc($sBook['book_author'] ?? $sBook['author']); ?></small>
                </td>
                <td class="text-center align-middle py-2">
                  <span class="badge badge-subtle-primary font-monospace fs-1 px-2 py-1"><?= esc($sBook['item_code'] ?: '-'); ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="receipt-divider"></div>

    <!-- Footer Note -->
    <div class="text-center text-muted fs-1 mt-2">
      <p class="mb-1 fw-semibold"><i class="ti ti-info-circle me-1 text-primary"></i> Simpan & bawa struk ini atau ID Card saat melakukan pengembalian buku.</p>
      <small class="d-block text-muted">Terima Kasih • Perpustakaan Assalafiyyah</small>
    </div>
  </div>
</div>

<?php if ((request()->getGet('print') ?? null) === 'true' || isset($_GET['print'])) : ?>
  <script>
    window.addEventListener('DOMContentLoaded', (event) => {
      setTimeout(() => {
        window.print();
      }, 500);
    });
  </script>
<?php endif; ?>
<?= $this->endSection() ?>
