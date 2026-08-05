<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Bayar Denda - Perpustakaan</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="mb-3">
  <a href="<?= base_url('admin/fines'); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
    <i class="ti ti-arrow-left me-1"></i> Kembali ke Manajemen Denda
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

<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div class="badge bg-white text-primary fw-bold px-3 py-1 mb-2 rounded-pill fs-2 shadow-sm">
          <i class="ti ti-cash-banknote me-1"></i> Transaksi Pelunasan Denda
        </div>
        <h3 class="text-white fw-bold mb-1">Pembayaran Tagihan Denda</h3>
        <p class="text-white-50 mb-0">Rincian seluruh pelanggaran buku, kompensasi denda, dan form pencatatan pembayaran.</p>
      </div>
      <div>
        <span class="badge bg-white text-dark fs-3 px-3 py-2 rounded-pill shadow-sm"><i class="ti ti-barcode me-1 text-primary"></i> Kode TRX: <strong><?= esc($primaryUid); ?></strong></span>
      </div>
    </div>
  </div>
</div>

<?php
$fineAmount = intval($totalFineAmount ?? 0);
$amountPaid = intval($totalAmountPaid ?? 0);
$remaining = max(0, $fineAmount - $amountPaid);
?>

<div class="row g-4">
  <!-- Left Side: Member Profile & Transaction Books Breakdown -->
  <div class="col-12 col-lg-7">
    <!-- Member Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
      <div class="card-body p-3">
        <div class="d-flex align-items-center gap-3">
          <div class="table-avatar-initial" style="width: 46px; height: 46px; font-size: 1.1rem;">
            <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1) . substr($member['last_name'] ?? '', 0, 1)); ?>
          </div>
          <div>
            <h6 class="fw-bold text-dark mb-0 fs-4"><?= esc("{$member['first_name']} {$member['last_name']}"); ?></h6>
            <div class="text-muted fs-2"><i class="ti ti-mail me-1"></i><?= esc($member['email'] ?: 'Tidak ada email'); ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Books & Violation Details -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <div class="card-header bg-light border-bottom p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0"><i class="ti ti-books text-primary me-2"></i>Daftar Buku Dipinjam (<?= count($transactionLoans); ?> Judul)</h5>
        <span class="badge badge-subtle-primary fs-2 px-3 py-1 rounded-pill"><?= count($transactionLoans); ?> Buku</span>
      </div>
      <div class="card-body p-3">
        <div class="d-flex flex-column gap-3">
          <?php foreach ($transactionLoans as $idx => $tBook) : ?>
            <?php
            $cond = strtolower($tBook['return_condition'] ?? 'baik');
            $condBadgeClass = ($cond === 'baik') ? 'badge-subtle-success' : (($cond === 'rusak') ? 'badge-subtle-warning text-dark' : 'badge-subtle-danger');
            $condIcon = ($cond === 'baik') ? 'ti-check' : (($cond === 'rusak') ? 'ti-alert-triangle' : 'ti-x');
            $condLabel = ($cond === 'baik') ? 'Baik (Tersedia)' : (($cond === 'rusak') ? 'Rusak (Denda 50%)' : 'Hilang (Denda 100%)');
            ?>
            <div class="p-3 rounded-3 border border-light-subtle bg-light">
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                <div>
                  <h6 class="fw-bold text-dark mb-1 fs-3"><?= ($idx + 1); ?>. <?= esc($tBook['title']); ?></h6>
                  <span class="badge bg-white text-muted border fs-1 px-2 py-1"><i class="ti ti-barcode me-1 text-primary"></i>Kode: <?= esc($tBook['item_code'] ?? 'Umum'); ?></span>
                </div>
                <span class="badge <?= $condBadgeClass; ?> px-3 py-1 rounded-pill fs-2 fw-semibold">
                  <i class="ti <?= $condIcon; ?> me-1"></i>Kondisi: <?= $condLabel; ?>
                </span>
              </div>

              <!-- Dates & Violation Breakdown -->
              <div class="row g-2 fs-2 text-muted mt-1 pt-2 border-top">
                <div class="col-12 col-md-6">
                  <div><i class="ti ti-calendar me-1 text-primary"></i>Pinjam: <strong><?= date('d/m/Y H:i', strtotime($tBook['loan_date'])); ?></strong></div>
                  <div><i class="ti ti-calendar-event me-1 text-warning"></i>Tenggat: <strong><?= date('d/m/Y', strtotime($tBook['due_date'])); ?></strong></div>
                </div>
                <div class="col-12 col-md-6">
                  <div><i class="ti ti-calendar-check me-1 text-success"></i>Kembali: <strong><?= date('d/m/Y H:i', strtotime($tBook['return_date'])); ?></strong></div>
                  <div><i class="ti ti-coin me-1 text-info"></i>Harga Buku: <strong>Rp <?= number_format(floatval($tBook['item_price'] ?? 50000), 0, ',', '.'); ?></strong></div>
                </div>
              </div>

              <!-- Violations & Compensation Calculation -->
              <?php if (!empty($tBook['violation_details'])) : ?>
                <div class="mt-2 pt-2 border-top border-light-subtle">
                  <small class="fw-bold text-danger d-block mb-1 fs-1"><i class="ti ti-receipt-tax me-1"></i>Rincian Kompensasi Denda:</small>
                  <?php foreach ($tBook['violation_details'] as $v) : ?>
                    <div class="d-flex justify-content-between align-items-center bg-white p-2 rounded border mb-1 fs-2">
                      <div>
                        <strong class="text-dark me-2">• <?= esc($v['label']); ?></strong>
                        <small class="text-muted">(<?= esc($v['desc']); ?>)</small>
                      </div>
                      <span class="fw-bold text-danger">+ Rp <?= number_format($v['amount'], 0, ',', '.'); ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Payment Summary & Form -->
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm border-0 rounded-4 h-100 overflow-hidden">
      <div class="card-header bg-light border-bottom p-3">
        <h5 class="fw-bold text-dark mb-0"><i class="ti ti-receipt text-primary me-2"></i>Form Pembayaran Denda</h5>
      </div>
      <div class="card-body p-4">
        <!-- Summary Cards -->
        <div class="row g-2 mb-4">
          <div class="col-4">
            <div class="p-3 rounded-3 border border-light-subtle bg-light text-center">
              <small class="text-muted d-block mb-1 fs-1">Total Denda</small>
              <strong class="text-dark fs-3">Rp <?= number_format($fineAmount, 0, ',', '.'); ?></strong>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 rounded-3 border border-success-subtle bg-success-subtle text-center">
              <small class="text-success d-block mb-1 fs-1">Telah Dibayar</small>
              <strong class="text-success fs-3">Rp <?= number_format($amountPaid, 0, ',', '.'); ?></strong>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 rounded-3 border border-danger-subtle bg-danger-subtle text-center">
              <small class="text-danger d-block mb-1 fs-1">Sisa Tagihan</small>
              <strong class="text-danger fs-3">Rp <?= number_format($remaining, 0, ',', '.'); ?></strong>
            </div>
          </div>
        </div>

        <form action="<?= base_url("admin/fines/{$primaryUid}"); ?>" method="post">
          <?= csrf_field(); ?>
          <input type="hidden" name="_method" value="PUT">

          <div class="mb-4">
            <label for="nominal" class="form-label fw-semibold text-dark">Nominal Pembayaran Denda (Rp)</label>
            <div class="input-group search-group">
              <span class="input-group-text bg-light text-muted fw-bold">Rp</span>
              <input type="number" class="form-control form-control-lg fw-bold text-dark <?php if ($validation->hasError('nominal')) : ?>is-invalid<?php endif ?>" id="nominal" name="nominal" value="<?= $oldInput['nominal'] ?? $remaining; ?>" placeholder="Masukkan nominal pembayaran" min="1000" max="<?= $remaining; ?>" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2 fs-2">
              <span class="text-muted">Minimal bayar: <strong>Rp 1.000</strong></span>
              <button type="button" class="btn btn-sm btn-link p-0 text-primary text-decoration-none fw-bold" onclick="document.getElementById('nominal').value = <?= $remaining; ?>;">
                <i class="ti ti-checks me-1"></i>Isi Pelunasan Penuh (Rp <?= number_format($remaining, 0, ',', '.'); ?>)
              </button>
            </div>
            <div class="invalid-feedback">
              <?= $validation->getError('nominal'); ?>
            </div>
          </div>

          <button type="submit" class="btn btn-pill-gold fw-bold px-4 py-3 shadow w-100 fs-4 d-flex align-items-center justify-content-center" data-confirm="Apakah Anda yakin ingin memproses pembayaran denda ini sebesar Rp <?= number_format($remaining, 0, ',', '.'); ?>?">
            <i class="ti ti-check me-2 fs-5"></i> Simpan Pembayaran Denda
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>