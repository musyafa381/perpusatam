<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Detail Pengembalian Transaksi</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php

use CodeIgniter\I18n\Time;

$now = Time::now(locale: 'id');
$loanDate = Time::parse($loan['loan_date'], locale: 'id');
$dueDate = Time::parse($loan['due_date'], locale: 'id');
$returnDate = Time::parse($loan['return_date'], locale: 'id');

$totalBooksCount = count($transactionLoans ?? []);

// Calculate aggregated fine
$totalFineAmount = 0;
$totalAmountPaid = 0;
$hasUnpaidFine = false;

foreach ($transactionLoans as $tLoan) {
  $fAmount = (float)($tLoan['fine_amount'] ?? 0);
  $fPaid = (float)($tLoan['amount_paid'] ?? 0);
  $totalFineAmount += $fAmount;
  $totalAmountPaid += $fPaid;
  if ($fAmount > 0 && $fPaid < $fAmount) {
    $hasUnpaidFine = true;
  }
}
$isLate = $returnDate->isAfter($dueDate);

if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div class="badge bg-white text-primary fw-bold px-3 py-1 mb-2 rounded-pill fs-2 shadow-sm">
          <i class="ti ti-rotate-clockwise-2 me-1"></i> Rincian Pengembalian
        </div>
        <h3 class="text-white fw-bold mb-1">Detail Transaksi Pengembalian</h3>
        <p class="text-white-50 mb-0">Rincian lengkap pengembalian <?= $totalBooksCount; ?> buku oleh <?= esc("{$loan['first_name']} {$loan['last_name']}"); ?>.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('admin/returns'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-arrow-left me-1"></i> Kembali
        </a>
        <?php
        $hasDamagedOrLost = false;
        foreach ($transactionLoans as $tL) {
          $cnd = strtolower($tL['return_condition'] ?? $tL['item_condition'] ?? $tL['condition'] ?? 'baik');
          if ($cnd === 'rusak' || $cnd === 'hilang') {
            $hasDamagedOrLost = true;
            break;
          }
        }
        ?>
        <?php if ($hasDamagedOrLost) : ?>
          <a href="<?= base_url("admin/returns/responsibility-letter/{$loan['uid']}?print=true"); ?>" target="_blank" class="btn btn-warning text-dark fw-bold shadow-sm">
            <i class="ti ti-file-text me-1"></i> Surat Pertanggungjawaban (PDF)
          </a>
        <?php endif; ?>
        <a href="<?= base_url("admin/loans/receipt/{$loan['uid']}?print=true"); ?>" target="_blank" class="btn btn-pill-gold fw-bold shadow-sm">
          <i class="ti ti-printer me-1"></i> Cetak / Print Struk
        </a>
        <form action="<?= base_url("admin/returns/{$loan['uid']}"); ?>" method="post" class="m-0">
          <?= csrf_field(); ?>
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="btn btn-outline-light fw-bold shadow-sm" data-confirm="Apakah Anda yakin ingin membatalkan pengembalian ini?">
            <i class="ti ti-trash me-1"></i> Batalkan Pengembalian
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Left Side: Member & Books -->
  <div class="col-12 col-lg-8">
    <!-- Member Info Card -->
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
          <div class="member-avatar me-3" style="width: 54px; height: 54px; font-size: 1.3rem;">
            <?= strtoupper(substr($loan['first_name'] ?? 'A', 0, 1) . substr($loan['last_name'] ?? '', 0, 1)); ?>
          </div>
          <div>
            <h4 class="fw-bold text-dark mb-1"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></h4>
            <span class="badge badge-subtle-primary me-2"><i class="ti ti-barcode me-1"></i><?= esc($loan['member_uid']); ?></span>
            <span class="text-muted fs-2"><i class="ti ti-mail me-1"></i><?= esc($loan['email'] ?: '-'); ?></span>
          </div>
        </div>

        <div class="row g-3 mt-2">
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">NOMOR TELEPON</small>
              <span class="fw-bold text-dark"><i class="ti ti-phone me-1 text-primary"></i><?= esc($loan['phone'] ?: '-'); ?></span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">ALAMAT ANGGOTA</small>
              <span class="fw-bold text-dark"><i class="ti ti-map-pin me-1 text-primary"></i><?= esc($loan['address'] ?: '-'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Returned Books Table -->
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="fw-bold text-dark mb-0"><i class="ti ti-books text-primary me-2"></i> Daftar Buku yang Dikembalikan (<?= $totalBooksCount; ?> Buku)</h5>
        </div>

        <div class="table-responsive rounded-3 border">
          <table class="table table-hover align-middle table-custom mb-0">
            <thead>
              <tr>
                <th class="ps-3">#</th>
                <th>Judul & Pengarang</th>
                <th class="text-center">Kode Eksemplar</th>
                <th>Lokasi Rak</th>
                <th class="text-center">Kondisi Pengembalian</th>
                <th class="text-center pe-3">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $bNum = 1; ?>
              <?php foreach ($transactionLoans as $bItem) : ?>
                <?php $cond = strtolower($bItem['return_condition'] ?? 'baik'); ?>
                <tr>

                  <th scope="row" class="ps-3 text-muted"><?= $bNum++; ?></th>
                  <td>
                    <div class="fw-bold text-dark fs-3"><?= esc("{$bItem['title']} ({$bItem['year']})"); ?></div>
                    <small class="text-muted"><i class="ti ti-user me-1"></i><?= esc($bItem['author']); ?></small>
                  </td>
                  <td class="text-center">
                    <?php if (!empty($bItem['item_code'])) : ?>
                      <span class="badge badge-subtle-primary fs-2 px-3 py-2"><i class="ti ti-barcode me-1"></i><?= esc($bItem['item_code']); ?></span>
                    <?php else : ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge badge-subtle-secondary fs-2"><i class="ti ti-box me-1"></i><?= esc($bItem['rack'] ?? '-'); ?></span>
                  </td>
                  <td class="text-center">
                    <?php if ($cond === 'rusak') : ?>
                      <span class="badge badge-subtle-warning fs-2 px-3 py-1"><i class="ti ti-alert-triangle me-1"></i>Rusak</span>
                    <?php elseif ($cond === 'hilang') : ?>
                      <span class="badge badge-subtle-danger fs-2 px-3 py-1"><i class="ti ti-circle-x me-1"></i>Hilang</span>
                    <?php else : ?>
                      <span class="badge badge-subtle-success fs-2 px-3 py-1"><i class="ti ti-check me-1"></i>Baik</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center pe-3">
                    <a href="<?= base_url("admin/books/{$bItem['slug']}"); ?>" class="btn btn-outline-primary btn-sm rounded-3 px-3">
                      Detail
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>


            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Fine Summary Card (Di bawah daftar buku) -->
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-receipt-tax text-primary me-2"></i> Status Denda & Sanksi</h5>

        <div class="row align-items-center g-3">
          <div class="col-12 col-md-5 border-end">
            <div class="stat-box mb-3">
              <small class="text-muted d-block fw-semibold mb-1">STATUS KETERLAMBATAN</small>
              <?php if ($isLate) : ?>
                <span class="badge badge-subtle-warning fs-3 px-3 py-2">
                  <i class="ti ti-clock-alert me-1"></i> Terlambat <?= abs($returnDate->difference($dueDate)->getDays()); ?> Hari
                </span>
              <?php else : ?>
                <span class="badge badge-subtle-primary fs-3 px-3 py-2">
                  <i class="ti ti-circle-check me-1"></i> Tepat Waktu
                </span>
              <?php endif; ?>
            </div>
            
            <div class="text-center pt-1">
              <?php if ($hasUnpaidFine) : ?>
                <span class="badge bg-danger rounded-pill px-4 py-2 fw-bold fs-3 mb-2 d-block">Menunggak</span>
                <a href="<?= base_url("admin/fines/pay/{$loan['uid']}"); ?>" class="btn btn-warning w-100 fw-bold rounded-3 shadow-sm py-2">
                  <i class="ti ti-cash me-1"></i> Bayar Denda Sekarang
                </a>
              <?php else : ?>
                <span class="badge bg-success rounded-pill px-4 py-2 fw-bold fs-3 d-block">
                  <?= $totalFineAmount > 0 ? 'Lunas' : 'Selesai / Bebas Denda'; ?>
                </span>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-12 col-md-7 ps-md-4">
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="text-muted">Total Tagihan Denda:</span>
              <span class="fw-bold text-primary fs-4">Rp <?= number_format($totalFineAmount, 0, ',', '.'); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="text-muted">Total Dibayar:</span>
              <span class="fw-bold text-primary fs-4">Rp <?= number_format($totalAmountPaid, 0, ',', '.'); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2">
              <span class="text-muted">Sisa Denda:</span>
              <span class="fw-extrabold text-dark fs-4">Rp <?= number_format(max(0, $totalFineAmount - $totalAmountPaid), 0, ',', '.'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Transaction Dates & Barcode -->
  <div class="col-12 col-lg-4">
    <!-- Dates Card -->
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-clock text-primary me-2"></i> Waktu Transaksi</h5>
        
        <div class="d-flex flex-column gap-3">
          <div class="stat-box">
            <small class="text-muted d-block fw-semibold mb-1"><i class="ti ti-calendar me-1 text-primary"></i> WAKTU PINJAM</small>
            <div class="fw-bold text-dark fs-3"><?= $loanDate->toLocalizedString('d MMMM Y'); ?></div>
            <small class="text-muted"><?= $loanDate->toLocalizedString('HH:mm:ss'); ?> WIB</small>
          </div>

          <div class="stat-box">
            <small class="text-muted d-block fw-semibold mb-1"><i class="ti ti-calendar-due me-1 text-warning"></i> BATAS PENGEMBALIAN (TENGGAT)</small>
            <div class="fw-bold text-dark fs-3"><?= $dueDate->toLocalizedString('d MMMM Y'); ?></div>
            <small class="text-muted">23:59:59 WIB</small>
          </div>

          <div class="stat-box">
            <small class="text-muted d-block fw-semibold mb-1"><i class="ti ti-rotate-clockwise-2 me-1 text-primary"></i> WAKTU DIKEMBALIKAN</small>
            <div class="fw-bold <?= $isLate ? 'text-warning' : 'text-primary'; ?> fs-3"><?= $returnDate->toLocalizedString('d MMMM Y'); ?></div>
            <small class="text-muted"><?= $returnDate->toLocalizedString('HH:mm:ss'); ?> WIB</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Barcode Widget Card -->
    <div class="card info-card border-0 text-center mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-barcode text-primary me-2"></i> Kode Barcode Transaksi</h5>
        <div class="p-3 bg-white rounded-3 border shadow-sm mb-3">
          <div class="d-flex justify-content-center align-items-center mb-2 overflow-hidden" style="max-height: 65px;">
            <?= generateBarcodeSVG($loan['uid'], 60); ?>
          </div>
          <strong class="font-monospace text-dark fs-4 d-block tracking-wider mt-1"><?= esc($loan['uid']); ?></strong>
        </div>
        <a href="<?= base_url("admin/loans/receipt/{$loan['uid']}?print=true"); ?>" target="_blank" class="btn btn-pill-gold fw-bold shadow-sm w-100">
          <i class="ti ti-printer me-1"></i> Cetak / Print Struk Transaksi
        </a>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>