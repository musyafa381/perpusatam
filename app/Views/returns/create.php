<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Konfirmasi Pengembalian Buku</title>
<style>
  .card-gradient-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: #ffffff;
    border-radius: 12px;
  }
  .info-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .info-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
  }
  .member-avatar {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    color: #fff;
    font-size: 1.3rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
  }
  .stat-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
  }
  .fine-banner {
    background: linear-gradient(135deg, #fff5f5 0%, #ffe3e3 100%);
    border: 1px solid #ffc9c9;
    border-radius: 12px;
  }
  .table-custom th {
    background-color: #f8fafc;
    color: #475569;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php

use App\Models\FinesPerDayModel;
use CodeIgniter\I18n\Time;

$now = Time::now(locale: 'id');

$loanCreateDate = Time::parse($loan['loan_date'], locale: 'id');
$loanDueDate = Time::parse($loan['due_date'], locale: 'id');

$isLate = $now->isAfter($loanDueDate);
$daysLate = $now->today()->difference($loanDueDate)->getDays();

$allLoans = $loans ?? [$loan];
$totalBooksCount = 0;
foreach ($allLoans as $l) {
  $totalBooksCount += ($l['quantity'] ?? 1);
}

$initials = strtoupper(substr($loan['first_name'] ?? 'A', 0, 1) . substr($loan['last_name'] ?? '', 0, 1));
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <a href="<?= base_url('admin/returns/new/search'); ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-3">
    <i class="ti ti-arrow-left me-1"></i> Kembali ke Pencarian
  </a>
</div>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<form action="<?= base_url('admin/returns'); ?>" method="post">
  <?= csrf_field(); ?>
  <input type="hidden" name="loan_uid" value="<?= $loan['uid'] ?? $loan['loan_uid'] ?? ''; ?>">
  <input type="hidden" name="date" value="<?= Time::now(locale: 'id'); ?>">

  <!-- Header Banner -->
  <div class="card card-gradient-header shadow-sm mb-4 border-0">
    <div class="card-body p-4">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <div class="badge bg-white text-primary fw-bold px-3 py-1 mb-2 rounded-pill fs-2 shadow-sm">
            <i class="ti ti-rotate-clockwise-2 me-1"></i> Konfirmasi Pengembalian
          </div>
          <h3 class="text-white fw-bold mb-1">Transaksi Pengembalian Buku</h3>
          <p class="text-white-50 mb-0">Verifikasi informasi anggota, status keterlambatan, dan daftar buku yang akan dikembalikan.</p>
        </div>
        <div class="text-end">
          <span class="badge bg-white text-dark fs-3 fw-bold px-3 py-2 rounded-3 shadow-sm">
            <i class="ti ti-books text-primary me-1"></i> <?= count($allLoans); ?> Judul / <?= $totalBooksCount; ?> Eksemplar
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <!-- Member Information Card -->
    <div class="col-12 col-lg-6">
      <div class="info-card h-100 p-4">
        <div class="d-flex align-items-center mb-3">
          <div class="member-avatar me-3">
            <?= $initials; ?>
          </div>
          <div>
            <h5 class="fw-bold text-dark mb-1"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="badge px-2 py-1 fs-2 rounded" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
                <i class="ti ti-id-badge-2 me-1" style="color: #8b5e3c !important;"></i>ID Card: <strong><?= esc($loan['member_uid'] ?? $loan['member_id']); ?></strong>
              </span>
              <span class="badge px-2 py-1 fs-2 rounded" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
                <i class="ti ti-barcode me-1" style="color: #8b5e3c !important;"></i>Kode TRX: <strong><?= esc($loan['uid']); ?></strong>
              </span>
            </div>
          </div>
        </div>

        <hr class="my-3 opacity-25">
        <div class="row g-3">
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block mb-1"><i class="ti ti-mail me-1"></i> Email</small>
              <span class="fw-semibold text-dark text-truncate d-block"><?= esc($loan['email'] ?? '-'); ?></span>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block mb-1"><i class="ti ti-phone me-1"></i> Nomor Telepon</small>
              <span class="fw-semibold text-dark"><?= esc($loan['phone'] ?? '-'); ?></span>
            </div>
          </div>
          <div class="col-12">
            <div class="stat-box">
              <small class="text-muted d-block mb-1"><i class="ti ti-map-pin me-1"></i> Alamat</small>
              <span class="fw-semibold text-dark"><?= esc($loan['address'] ?? '-'); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loan Status Card -->
    <div class="col-12 col-lg-6">
      <div class="info-card h-100 p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="ti ti-calendar-stats text-primary me-2"></i> Status Peminjaman</h6>
        <div class="row g-3 mb-3">
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block mb-1"><i class="ti ti-calendar-event me-1"></i> Tanggal Pinjam</small>
              <span class="fw-bold text-dark"><?= $loanCreateDate->toLocalizedString('dd/MM/y'); ?></span>
              <small class="text-muted d-block fs-2"><?= $loanCreateDate->toLocalizedString('HH:mm:ss'); ?></small>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block mb-1"><i class="ti ti-calendar-due me-1"></i> Tenggat Waktu</small>
              <span class="fw-bold text-dark"><?= $loanDueDate->toLocalizedString('dd/MM/y'); ?></span>
              <small class="text-muted d-block fs-2">Tenggat Pengembalian</small>
            </div>
          </div>
        </div>
        <div class="p-3 rounded-3 d-flex align-items-center justify-content-between" style="background-color: #f8f2e6 !important; border: 1px solid #e8decb !important;">
          <div class="d-flex align-items-center">
            <i class="ti <?= $isLate ? 'ti-clock-alert text-danger' : 'ti-circle-check'; ?> fs-7 me-2" style="<?= $isLate ? '' : 'color: #8b5e3c !important;'; ?>"></i>
            <div>
              <span class="fw-bold d-block" style="<?= $isLate ? 'color: #dc2626;' : 'color: #6e4727 !important;'; ?>">
                <?= $isLate ? 'Status: Terlambat' : 'Status: Tepat Waktu'; ?>
              </span>
              <small style="<?= $isLate ? 'color: #991b1b;' : 'color: #8b5e3c !important;'; ?>">
                <?= $isLate ? 'Keterlambatan ' . abs($daysLate) . ' hari' : 'Peminjaman belum melewati batas tenggat'; ?>
              </small>
            </div>
          </div>
          <?php if ($isLate) : ?>
            <span class="badge bg-danger rounded-pill px-3 py-2 fs-2 fw-semibold">
              <i class="ti ti-alert-triangle me-1"></i> <?= abs($daysLate); ?> Hari
            </span>
          <?php else : ?>
            <span class="badge rounded-pill px-3 py-2 fs-2 fw-semibold text-white" style="background: linear-gradient(135deg, #8b5e3c 0%, #c59b27 100%) !important;">
              <i class="ti ti-check me-1"></i> Normal
            </span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Book List Card -->
  <div class="card info-card mb-4 border-0">
    <div class="card-body p-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold text-dark mb-0">
          <i class="ti ti-book-upload text-primary me-2"></i> Daftar Buku yang Dikembalikan
        </h5>
        <span class="badge fw-semibold px-3 py-2 rounded-pill" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
          <?= count($allLoans); ?> Buku Terpilih
        </span>
      </div>

      <div class="table-responsive rounded-3 border">
        <table class="table table-hover align-middle mb-0 table-custom">
          <thead>
            <tr>
              <th class="ps-3">#</th>
              <th>Judul Buku</th>
              <th>Pengarang & Penerbit</th>
              <th class="text-center">Kode Eksemplar</th>
              <th class="text-center pe-3" style="width: 220px;">Kondisi Pengembalian</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1; foreach ($allLoans as $item) : ?>
              <tr>
                <th scope="row" class="ps-3 text-muted"><?= $no++; ?></th>
                <td>
                  <div class="fw-bold text-dark fs-3"><?= esc($item['title']); ?></div>
                  <?php if (!empty($item['category'])) : ?>
                    <span class="badge bg-light text-muted border fw-normal fs-2 mt-1">
                      <?= esc($item['category']); ?>
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-semibold text-dark"><i class="ti ti-user me-1 text-muted"></i><?= esc($item['author']); ?></div>
                  <small class="text-muted"><i class="ti ti-building me-1"></i><?= esc($item['publisher']); ?></small>
                </td>
                <td class="text-center">
                  <?php if (!empty($item['item_code'])) : ?>
                    <span class="badge fw-semibold fs-2 px-3 py-2" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
                      <i class="ti ti-barcode me-1" style="color: #8b5e3c !important;"></i><?= esc($item['item_code']); ?>
                    </span>
                  <?php else : ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td class="text-center pe-3" style="min-width: 240px;">
                  <select name="conditions[<?= $item['loan_id'] ?? $item['id']; ?>]" class="form-select form-select-sm fw-bold rounded-pill px-3 py-2 text-dark shadow-sm mb-1" style="background-color: #fcf8f2; border: 1.5px solid #d4c4a8; color: #5a3e2b;">
                    <option value="baik" selected>🟢 Baik (Tersedia)</option>
                    <option value="rusak">🟡 Rusak (Denda 50% Buku)</option>
                    <option value="hilang">🔴 Hilang (Denda 100% Buku)</option>
                  </select>
                  <input type="text" name="condition_notes[<?= $item['loan_id'] ?? $item['id']; ?>]" class="form-control form-control-sm fs-1 rounded-3 mt-1" placeholder="Catatan fisik / kerusakan (opsional)...">
                </td>

              </tr>
            <?php endforeach; ?>

          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Fine Breakdown Banner if Late -->
  <?php if ($isLate) :
    $finePerDay = FinesPerDayModel::getAmount();
    $totalFine = abs($daysLate) * $totalBooksCount * $finePerDay;
  ?>
    <div class="card fine-banner shadow-sm mb-4 border-0">
      <div class="card-body p-4">
        <div class="d-flex align-items-center mb-3">
          <div class="bg-danger text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
            <i class="ti ti-receipt-tax fs-5"></i>
          </div>
          <div>
            <h5 class="fw-bold text-danger mb-0">Perhitungan Denda Keterlambatan</h5>
            <small class="text-danger-emphasis">Denda dikenakan per hari untuk setiap eksemplar buku yang terlambat dikembalikan.</small>
          </div>
        </div>

        <div class="row align-items-center g-3 bg-white p-3 rounded-3 border">
          <div class="col-12 col-md-8">
            <div class="row align-items-center text-center g-2">
              <div class="col-4">
                <div class="p-2 bg-light rounded-3 border">
                  <h4 class="fw-bold text-dark mb-0"><?= abs($daysLate); ?></h4>
                  <small class="text-muted fs-2">Hari Terlambat</small>
                </div>
              </div>
              <div class="col-1 fw-bold text-muted fs-5">×</div>
              <div class="col-3">
                <div class="p-2 bg-light rounded-3 border">
                  <h4 class="fw-bold text-dark mb-0"><?= $totalBooksCount; ?></h4>
                  <small class="text-muted fs-2">Total Buku</small>
                </div>
              </div>
              <div class="col-1 fw-bold text-muted fs-5">×</div>
              <div class="col-3">
                <div class="p-2 bg-light rounded-3 border">
                  <h4 class="fw-bold text-dark mb-0">Rp<?= number_format($finePerDay, 0, ',', '.'); ?></h4>
                  <small class="text-muted fs-2">Denda / Hari</small>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4 text-center text-md-end ps-md-4 border-start-md">
            <span class="text-muted fs-2 text-uppercase fw-semibold">Total Tagihan Denda</span>
            <h2 class="text-danger fw-extrabold mb-0" style="font-size: 2.2rem;">Rp <?= number_format($totalFine, 0, ',', '.'); ?></h2>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Submit Action Card -->
  <div class="card info-card p-4 border-0 shadow-sm">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h6 class="fw-bold text-dark mb-1"><i class="ti ti-shield-check text-success me-1"></i> Siap Memproses Pengembalian?</h6>
        <p class="text-muted fs-2 mb-0">Pastikan seluruh buku fisik dalam kondisi baik sebelum mengonfirmasi.</p>
      </div>
      <div>
        <button type="submit" data-confirm="Apakah Anda yakin ingin mengonfirmasi pengembalian <?= $totalBooksCount; ?> buku ini?" class="btn btn-primary btn-lg px-4 py-2 rounded-3 shadow-sm">
          <i class="ti ti-circle-check me-2"></i> Konfirmasi Pengembalian
        </button>
      </div>
    </div>
  </div>
</form>

<?= $this->endSection() ?>