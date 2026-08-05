<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Manajemen Denda</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php

use CodeIgniter\I18n\Time;

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
          <i class="ti ti-receipt-tax me-1"></i> Keuangan & Tagihan
        </div>
        <h3 class="text-white fw-bold mb-1">Manajemen Denda Keterlambatan</h3>
        <p class="text-white-50 mb-0">Kelola tagihan denda, status pembayaran, dan riwayat sanksi keterlambatan.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <?php if (auth()->user()->inGroup('superadmin')) : ?>
          <a href="<?= base_url('admin/fines/settings'); ?>" class="btn btn-light text-danger fw-bold shadow-sm">
            <i class="ti ti-settings me-1"></i> Pengaturan Denda
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <div class="row align-items-center mb-4 g-2">
      <div class="col-12 col-md-6">
        <div class="d-flex gap-2 align-items-center">
          <span class="text-muted fw-semibold me-1">Status Denda:</span>
          <a href="<?= $paidOffFilter ? base_url('admin/fines?paid-off=false') : '#'; ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= !$paidOffFilter ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <?php if (!$paidOffFilter) : ?><i class="ti ti-check me-1"></i><?php endif; ?>
            Belum Lunas
          </a>
          <a href="<?= $paidOffFilter ? '#' : base_url('admin/fines?paid-off=true'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $paidOffFilter ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <?php if ($paidOffFilter) : ?><i class="ti ti-check me-1"></i><?php endif; ?>
            Lunas
          </a>

        </div>
      </div>
      <div class="col-12 col-md-6">
        <form action="" method="get" class="d-flex justify-content-md-end">
          <input type="hidden" name="paid-off" value="<?= $paidOffFilter ? 'true' : 'false'; ?>">
          <div class="input-group search-group" style="max-width: 320px;">
            <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari denda / peminjam...">
            <button class="btn btn-primary fw-semibold" type="submit"><i class="ti ti-search"></i></button>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom mb-0">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Nama Peminjam</th>
            <th scope="col" class="text-center">Total Buku</th>
            <th scope="col">Tgl Pengembalian</th>
            <th scope="col">Denda Dibayar</th>
            <th scope="col">Jumlah Denda</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1 + ($itemPerPage * ($currentPage - 1));
          $now = Time::now(locale: 'id');
          ?>
          <?php if (empty($fines)) : ?>
            <tr>
              <td class="text-center py-4" colspan="7">
                <i class="ti ti-info-circle fs-6 d-block mb-1 text-muted"></i>
                <b>Tidak ada data denda ditemukan</b>
              </td>
            </tr>
          <?php endif; ?>
          <?php
          foreach ($fines as $fine) :
            $loanReturnDate = Time::parse($fine['return_date'], locale: 'id');
            $loanDueDate = Time::parse($fine['due_date'], locale: 'id');
          ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="fw-bold text-dark fs-3"><?= esc("{$fine['first_name']} {$fine['last_name']}"); ?></div>
              </td>

              <td class="text-center">
                <span class="badge badge-subtle-primary fs-2 px-3 py-2">
                  <i class="ti ti-books me-1"></i><?= esc($fine['total_books'] ?? 1); ?> Buku
                </span>
              </td>
              <td>
                <div class="fw-bold text-dark fs-2 mb-1"><i class="ti ti-calendar me-1 text-primary"></i><?= $loanReturnDate->toLocalizedString('dd/MM/y'); ?></div>
                <div class="d-flex flex-wrap gap-1">
                  <?php
                  $condStr = strtolower($fine['return_conditions'] ?? $fine['return_condition'] ?? 'baik');
                  $hasDamaged = (strpos($condStr, 'rusak') !== false);
                  $hasLost = (strpos($condStr, 'hilang') !== false);

                  $daysLate = 0;
                  if ($loanReturnDate->isAfter($loanDueDate)) {
                      $daysLate = abs($loanReturnDate->difference($loanDueDate)->getDays());
                  }

                  if ($daysLate > 0) : ?>
                    <span class="badge badge-subtle-danger fs-1 px-2 py-1"><i class="ti ti-clock-alert me-1"></i>Terlambat <?= $daysLate; ?> Hari</span>
                  <?php endif; ?>
                  <?php if ($hasDamaged) : ?>
                    <span class="badge badge-subtle-warning text-dark border px-2 py-1 fs-1"><i class="ti ti-alert-triangle me-1 text-warning"></i>Buku Rusak (50%)</span>
                  <?php endif; ?>
                  <?php if ($hasLost) : ?>
                    <span class="badge badge-subtle-danger border px-2 py-1 fs-1"><i class="ti ti-circle-x me-1"></i>Buku Hilang (100%)</span>
                  <?php endif; ?>
                  <?php if ($daysLate <= 0 && !$hasDamaged && !$hasLost) : ?>
                    <span class="badge badge-subtle-secondary fs-1 px-2 py-1"><i class="ti ti-info-circle me-1"></i>Denda Peminjaman</span>
                  <?php endif; ?>
                </div>
              </td>

              <td>
                <div class="fw-bold text-dark">Rp <?= number_format($fine['amount_paid'] ?? 0, 0, ',', '.'); ?></div>
                <?php if ($paidOffFilter || ($fine['amount_paid'] ?? 0) >= $fine['fine_amount']) : ?>
                  <span class="badge badge-subtle-success fs-1 mt-1"><i class="ti ti-check me-1"></i>Lunas</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-extrabold text-danger fs-4">Rp <?= number_format($fine['fine_amount'], 0, ',', '.'); ?></div>
              </td>
              <td class="text-center pe-3">
                <div class="d-flex justify-content-center gap-2">
                  <?php if (!$paidOffFilter && ($fine['amount_paid'] ?? 0) < $fine['fine_amount']) : ?>
                    <a href="<?= base_url("admin/fines/pay/{$fine['uid']}"); ?>" class="btn btn-pill-gold btn-sm px-3 fw-bold">
                      <i class="ti ti-cash me-1"></i> Bayar
                    </a>
                  <?php endif; ?>
                  <a href="<?= base_url("admin/returns/{$fine['uid']}"); ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold">
                    <i class="ti ti-eye me-1"></i> Detail
                  </a>
                </div>

              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager; ?>
    </div>

  </div>
</div>
<?= $this->endSection() ?>