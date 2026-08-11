<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Data Pengembalian</title>
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
          <i class="ti ti-rotate-clockwise-2 me-1"></i> Riwayat Pengembalian
        </div>
        <h3 class="text-white fw-bold mb-1">Data Pengembalian Buku</h3>
        <p class="text-white-50 mb-0">Daftar riwayat transaksi pengembalian buku oleh anggota perpustakaan.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form action="" method="get" class="me-2">
          <div class="input-group search-group">
            <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari data pengembalian..." aria-label="Cari pengembalian">
            <button class="btn btn-light text-primary fw-bold" type="submit"><i class="ti ti-search"></i> Cari</button>
          </div>
        </form>
        <a href="<?= base_url('admin/returns/new/search'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-plus me-1"></i> Pengembalian Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0">
  <div class="card-body p-4">
    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Nama Peminjam</th>
            <th scope="col" class="text-center">Jumlah Buku</th>
            <th scope="col">Tgl Pinjam</th>
            <th scope="col">Tgl Kembali</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1 + ($itemPerPage * ($currentPage - 1));
          $now = Time::now(locale: 'id');
          ?>
          <?php if (empty($transactions)) : ?>
            <tr>
              <td class="text-center py-4" colspan="7">
                <i class="ti ti-info-circle fs-6 d-block mb-1 text-muted"></i>
                <b>Tidak ada data pengembalian ditemukan</b>
              </td>
            </tr>
          <?php endif; ?>
          <?php
          foreach ($transactions as $trx) :
            $loanCreateDate = Time::parse($trx['loan_date'], locale: 'id');
            $loanDueDate = Time::parse($trx['due_date'], locale: 'id');
            $loanReturnDate = Time::parse($trx['return_date'], locale: 'id');

            $isFined = $trx['is_fined'];
            $isFinePaid = $trx['is_fine_paid'];
            $hasUnpaidFine = ($isFined && !$isFinePaid);

            $isLate = Time::parse($trx['return_date'])->isAfter($loanDueDate);
            $totalBooks = count($trx['items']);
          ?>
            <tr <?php if ($hasUnpaidFine) : ?>style="background-color: #fff8ee !important;" class="border-start border-3 border-warning"<?php endif; ?>>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="d-flex align-items-center">
                  <div class="member-avatar me-2" style="width: 38px; height: 38px; font-size: 0.95rem;">
                    <?= strtoupper(substr($trx['first_name'] ?? 'A', 0, 1) . substr($trx['last_name'] ?? '', 0, 1)); ?>
                  </div>
                  <div class="fw-bold text-dark fs-3"><?= esc("{$trx['first_name']} {$trx['last_name']}"); ?></div>
                </div>
              </td>
              <td class="text-center">
                <span class="badge badge-subtle-primary fs-3 px-3 py-2">
                  <i class="ti ti-books me-1"></i><?= $totalBooks; ?> Buku
                </span>
              </td>
              <td>
                <div class="fw-semibold text-dark"><?= $loanCreateDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i><?= $loanCreateDate->toLocalizedString('HH:mm'); ?></small>
              </td>
              <td>
                <div class="fw-semibold <?= $isLate ? 'text-danger' : 'text-dark'; ?>"><?= $loanReturnDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i><?= $loanReturnDate->toLocalizedString('HH:mm'); ?></small>
              </td>
              <td class="text-center">
                <span class="badge bg-success rounded-pill fw-semibold px-3 py-2"><i class="ti ti-check me-1"></i>Selesai</span>
                <?php if ($hasUnpaidFine) : ?>
                  <span class="badge bg-warning text-dark rounded-pill fw-semibold px-2 py-1 fs-1 ms-1" title="Memiliki Denda Belum Lunas"><i class="ti ti-receipt-tax me-1"></i>Ada Denda</span>
                <?php endif; ?>
              </td>
              <td class="text-center pe-3">
                <a href="<?= base_url("admin/returns/{$trx['primary_uid']}"); ?>" class="btn btn-primary btn-sm px-3 rounded-3">
                  Detail
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>