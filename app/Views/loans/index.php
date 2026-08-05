<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Data Peminjaman</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use CodeIgniter\I18n\Time;
?>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show rounded-3 shadow-sm border-0 fs-3 fw-semibold" role="alert">
      <i class="<?= (session()->getFlashdata('error') ?? false) ? 'ti ti-alert-triangle' : 'ti ti-circle-check'; ?> me-2 fs-5"></i>
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
          <i class="ti ti-arrows-exchange me-1"></i> Data Transaksi
        </div>
        <h3 class="text-white fw-bold mb-1">Data Peminjaman Buku</h3>
        <p class="text-white-50 mb-0">Daftar peminjaman aktif anggota perpustakaan yang sedang berjalan.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <form action="" method="get" class="me-2">
          <div class="input-group search-group">
            <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari nama peminjam..." aria-label="Cari peminjaman">
            <button class="btn btn-light text-primary fw-bold" type="submit"><i class="ti ti-search"></i> Cari</button>
          </div>
        </form>
        <a href="<?= base_url('admin/loans/new/members/search'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-plus me-1"></i> Peminjaman Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0">
  <div class="card-body p-4">
    <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
      <table class="table table-hover align-middle table-assalafiyyah mb-0">
        <thead>
          <tr>
            <th scope="col" class="text-center" style="width: 50px;">#</th>
            <th scope="col">Nama Peminjam</th>
            <th scope="col" class="text-center">Jumlah Buku</th>
            <th scope="col">Tgl Pinjam</th>
            <th scope="col">Tgl Kembali</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col" class="text-center pe-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1 + ($itemPerPage * ($currentPage - 1));
          $now = Time::now(locale: 'id');
          ?>
          <?php if (empty($loans)) : ?>
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">
                <i class="ti ti-info-circle fs-6 d-block mb-1"></i>
                Tidak ada data peminjaman.
              </td>
            </tr>
          <?php endif; ?>
          <?php
          foreach ($loans as $loan) :
            $loanCreateDate = Time::parse($loan['loan_date'], locale: 'id');
            $loanDueDate = Time::parse($loan['due_date'], locale: 'id');

            $isLate = $now->isAfter($loanDueDate);
            $isDueDate = $now->today()->equals($loanDueDate);
          ?>
            <tr>
              <th scope="row" class="col-index"><?= $i++; ?></th>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="table-avatar-initial">
                    <?= strtoupper(substr($loan['first_name'] ?? 'A', 0, 1) . substr($loan['last_name'] ?? '', 0, 1)); ?>
                  </div>
                  <div class="fw-bold text-dark fs-3"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></div>
                </div>
              </td>
              <td class="text-center">
                <?php if (($loan['returned_books'] ?? 0) > 0) : ?>
                  <span class="badge bg-warning text-dark fs-2 px-3 py-2 rounded-pill shadow-sm" title="<?= esc($loan['returned_books']); ?> dari <?= esc($loan['total_books']); ?> buku telah dikembalikan">
                    <i class="ti ti-clock-pause me-1"></i>Dikembalikan Sebagian (<?= esc($loan['returned_books']); ?>/<?= esc($loan['total_books']); ?>)
                  </span>
                <?php else : ?>
                  <span class="badge badge-subtle-primary fs-3 px-3 py-2 rounded-3">
                    <i class="ti ti-books me-1"></i><?= $loan['total_books'] ?? 1; ?> Buku
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-bold text-dark"><?= $loanCreateDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i><?= $loanCreateDate->toLocalizedString('HH:mm'); ?></small>
              </td>
              <td>
                <div class="fw-bold text-dark"><?= $loanDueDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i>23:59</small>
              </td>
              <td class="text-center">
                <?php if (($loan['returned_books'] ?? 0) > 0) : ?>
                  <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-bold"><i class="ti ti-check me-1"></i><?= esc($loan['returned_books']); ?> Kembali, <?= esc($loan['active_books']); ?> Pinjam</span>
                <?php elseif ($now->isBefore($loanDueDate) && !$isDueDate) : ?>
                  <span class="btn-pill-brown btn-sm px-3 py-1 d-inline-block">Aktif</span>
                <?php elseif ($isDueDate) : ?>
                  <span class="badge badge-subtle-warning px-3 py-2 rounded-pill">Jatuh Tempo</span>
                <?php else : ?>
                  <span class="badge badge-subtle-danger px-3 py-2 rounded-pill">Terlambat</span>
                <?php endif; ?>
              </td>
              <td class="text-center pe-4" style="width: 130px;">
                <a href="<?= base_url("admin/loans/{$loan['uid']}"); ?>" class="btn btn-pill-gold btn-sm d-inline-flex align-items-center justify-content-center gap-1">
                  <i class="ti ti-eye"></i> Detail
                </a>
              </td>


            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager->links('loans', 'my_pager'); ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>