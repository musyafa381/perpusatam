<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Peminjaman Berhasil</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use CodeIgniter\I18n\Time;

if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <i class="ti ti-circle-check me-2 fs-5"></i><?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div class="badge bg-white text-success fw-bold px-3 py-1 mb-2 rounded-pill fs-2 shadow-sm">
          <i class="ti ti-circle-check me-1"></i> Transaksi Berhasil (Langkah 3 dari 3)
        </div>
        <h3 class="text-white fw-bold mb-1">Konfirmasi Peminjaman Buku</h3>
        <p class="text-white-50 mb-0">Seluruh item buku berhasil dicatat ke dalam sistem peminjaman perpustakaan.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('admin/loans'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-list me-1"></i> Ke Daftar Peminjaman
        </a>
        <a href="<?= base_url('admin/loans/new/members/search'); ?>" class="btn btn-light text-success fw-bold shadow-sm">
          <i class="ti ti-plus me-1"></i> Transaksi Baru
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Result Table Card -->
<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-dark mb-4"><i class="ti ti-books text-primary me-2"></i> Rincian Buku yang Dipinjam</h5>

    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom mb-0">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Nama Peminjam</th>
            <th scope="col">Judul Buku & Pengarang</th>
            <th scope="col" class="text-center">Kode Eksemplar</th>
            <th scope="col">Tgl Pinjam</th>
            <th scope="col">Batas Tenggat</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
          foreach ($newLoans as $loan) :
            $loanDate = Time::parse($loan['loan_date'], locale: 'id');
            $dueDate = Time::parse($loan['due_date'], locale: 'id');
          ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="d-flex align-items-center">
                  <div class="member-avatar me-2" style="width: 38px; height: 38px; font-size: 0.95rem;">
                    <?= strtoupper(substr($loan['first_name'] ?? 'A', 0, 1) . substr($loan['last_name'] ?? '', 0, 1)); ?>
                  </div>
                  <div>
                    <a href="<?= base_url("admin/members/{$loan['member_uid']}"); ?>" class="fw-bold text-dark fs-3 text-decoration-none">
                      <?= esc("{$loan['first_name']} {$loan['last_name']}"); ?>
                    </a>
                  </div>
                </div>
              </td>
              <td>
                <a href="<?= base_url("admin/books/{$loan['slug']}"); ?>" class="fw-bold text-dark fs-3 text-decoration-none d-block">
                  <?= esc("{$loan['title']} ({$loan['year']})"); ?>
                </a>
                <small class="text-muted"><i class="ti ti-user me-1"></i>Author: <?= esc($loan['author']); ?></small>
              </td>
              <td class="text-center">
                <span class="badge badge-subtle-primary fs-3 px-3 py-2">
                  <i class="ti ti-barcode me-1"></i><?= esc($loan['item_code'] ?? '-'); ?>
                </span>
              </td>
              <td>
                <div class="fw-semibold text-dark"><?= $loanDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i><?= $loanDate->toLocalizedString('HH:mm'); ?></small>
              </td>
              <td>
                <div class="fw-semibold text-warning-emphasis"><?= $dueDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i><?= $dueDate->toLocalizedString('HH:mm'); ?></small>
              </td>
              <td class="text-center pe-3">
                <div class="d-flex justify-content-center gap-2">
                  <a href="<?= base_url("admin/loans/{$loan['uid']}"); ?>" class="btn btn-primary btn-sm rounded-3 px-3">
                    <i class="ti ti-eye me-1"></i> Detail
                  </a>
                  <form action="<?= base_url("admin/loans/{$loan['uid']}"); ?>" method="post" class="m-0">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3" data-confirm="Apakah Anda yakin ingin membatalkan peminjaman ini?">
                      <i class="ti ti-x me-1"></i> Batalkan
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>