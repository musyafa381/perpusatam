<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Detail Anggota - <?= esc($member['first_name'] . ' ' . $member['last_name']); ?></title>
<style>
  #qr-code {
    background-image: url(<?= base_url(MEMBERS_QR_CODE_URI . $member['qr_code']); ?>);
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    width: 220px;
    height: 220px;
  }
</style>
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

<?php
$tier = $tierDetails ?? \App\Models\MemberModel::getTierDetails($member);
$typeBadge = ($member['member_type'] ?? 'siswa') === 'siswa'

  ? '<span class="badge badge-subtle-primary fs-2"><i class="ti ti-school me-1"></i> Siswa / Santri</span>'
  : '<span class="badge badge-subtle-primary fs-2"><i class="ti ti-user-cog me-1"></i> Petugas / Staf</span>';

$dobString = '-';
if (!empty($member['date_of_birth']) && $member['date_of_birth'] !== '0000-00-00') {
  try {
    $parsedDob = Time::parse($member['date_of_birth'], locale: 'id');
    if ($parsedDob) {
      $dobString = $parsedDob->toLocalizedString('d MMMM Y');
    }
  } catch (\Throwable $e) {
    $dobString = $member['date_of_birth'];
  }
}
?>

<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
        <?php if (($tier['code'] ?? '') === 'living_library') : ?>
          <div class="shadow d-flex align-items-center justify-content-center text-white rounded-3" style="width: 64px; height: 64px; font-size: 1.8rem; background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: 2px solid #e8decb;">
            <i class="ti ti-building-community"></i>
          </div>
        <?php else : ?>
          <div class="member-avatar shadow" style="width: 64px; height: 64px; font-size: 1.6rem; border: 3px solid rgba(255,255,255,0.8); background: linear-gradient(135deg, #c59b27 0%, #8b5e3c 100%);">
            <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1) . substr($member['last_name'] ?? '', 0, 1)); ?>
          </div>
        <?php endif; ?>
        <div>

          <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <h3 class="text-white fw-bold mb-0"><?= esc("{$member['first_name']} {$member['last_name']}"); ?></h3>
            <?php if (($tier['code'] ?? '') === 'living_library') : ?>
              <span class="badge px-3 py-1 fs-2 shadow-sm fw-bold" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
                <i class="ti ti-building-community me-1" style="color: #8b5e3c !important;"></i> Member Living Library
              </span>
            <?php else : ?>
              <span class="badge <?= $tier['badge']; ?> px-3 py-1 fs-2 shadow-sm"><i class="ti <?= $tier['icon']; ?> me-1"></i> <?= esc($tier['name']); ?></span>
            <?php endif; ?>
          </div>
          <div class="d-flex align-items-center gap-3 text-white-50 flex-wrap">

            <span><i class="ti ti-barcode me-1"></i>UID: <?= esc($member['uid']); ?></span>
            <span>•</span>
            <span><?= $typeBadge; ?></span>
          </div>
        </div>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('admin/members'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-arrow-left me-1"></i> Kembali
        </a>
        <?php if (($tier['code'] ?? 'none') !== 'none') : ?>
          <a href="<?= base_url("admin/members/cards/{$member['id']}"); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
            <i class="ti ti-id-badge-2 me-1"></i> Kartu Member
          </a>
        <?php endif; ?>
        <a href="<?= base_url("admin/members/id-card/{$member['uid']}?print=true"); ?>" target="_blank" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-id me-1"></i> Cetak ID Card
        </a>
        <a href="<?= base_url("admin/members/{$member['uid']}/edit"); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-edit me-1"></i> Edit
        </a>
        <form action="<?= base_url("admin/members/{$member['uid']}"); ?>" method="post" class="m-0">
          <?= csrf_field(); ?>
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="btn btn-outline-light fw-bold shadow-sm" data-confirm="Apakah Anda yakin ingin menghapus anggota ini?">
            <i class="ti ti-trash me-1"></i> Hapus
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Stat Cards Grid -->
<div class="row g-3 mb-4">
  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-3 text-center">
        <div class="member-avatar mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #c59b27 0%, #8b5e3c 100%); color: #fff;">
          <i class="ti ti-book"></i>
        </div>
        <div class="fw-bold text-dark fs-4 mb-0"><?= $totalBooksLent; ?></div>
        <small class="text-muted fw-semibold fs-1">Buku Dipinjam</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-3 text-center">
        <div class="member-avatar mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); color: #fff;">
          <i class="ti ti-arrows-exchange"></i>
        </div>
        <div class="fw-bold text-dark fs-4 mb-0"><?= $loanCount; ?></div>
        <small class="text-muted fw-semibold fs-1">Tx Peminjaman</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-3 text-center">
        <div class="member-avatar mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #c59b27 0%, #d4af37 100%); color: #fff;">
          <i class="ti ti-check"></i>
        </div>
        <div class="fw-bold text-dark fs-4 mb-0"><?= $returnCount; ?></div>
        <small class="text-muted fw-semibold fs-1">Tx Pengembalian</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-3 text-center">
        <div class="member-avatar mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #b45309 0%, #8b5e3c 100%); color: #fff;">
          <i class="ti ti-clock-alert"></i>
        </div>
        <div class="fw-bold text-dark fs-4 mb-0"><?= $lateCount; ?></div>
        <small class="text-muted fw-semibold fs-1">Jml Terlambat</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-3 text-center">
        <div class="member-avatar mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #b91c1c 0%, #881337 100%); color: #fff;">
          <i class="ti ti-report-money"></i>
        </div>
        <div class="fw-bold text-danger fs-3 mb-0">Rp <?= number_format($unpaidFines, 0, ',', '.'); ?></div>
        <small class="text-muted fw-semibold fs-1">Denda Menunggak</small>
      </div>
    </div>
  </div>
  <div class="col-6 col-sm-4 col-xl-2">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-3 text-center">
        <div class="member-avatar mx-auto mb-2" style="width: 40px; height: 40px; font-size: 1rem; background: linear-gradient(135deg, #8b5e3c 0%, #c59b27 100%); color: #fff;">
          <i class="ti ti-cash"></i>
        </div>
        <div class="fw-bold text-primary fs-3 mb-0">Rp <?= number_format($paidFines, 0, ',', '.'); ?></div>
        <small class="text-muted fw-semibold fs-1">Denda Lunas</small>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Left Side: Member Detail Info -->
  <div class="col-12 col-lg-8">
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4"><i class="ti ti-user-check text-primary me-2"></i> Informasi Lengkap Anggota</h5>

        <div class="row g-3">
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">NAMA LENGKAP</small>
              <div class="fw-bold text-dark fs-3"><?= esc("{$member['first_name']} {$member['last_name']}"); ?></div>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">TIPE ANGGOTA</small>
              <div><?= $typeBadge; ?></div>
            </div>
          </div>

          <?php if (($member['member_type'] ?? 'siswa') === 'siswa') : ?>
            <div class="col-12 col-sm-6">
              <div class="stat-box">
                <small class="text-muted d-block fw-semibold mb-1">INSTANSI PENDIDIKAN</small>
                <div class="fw-bold text-dark"><i class="ti ti-building me-1 text-primary"></i><?= esc($member['institution'] ?? '-'); ?></div>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <div class="stat-box">
                <small class="text-muted d-block fw-semibold mb-1">KELAS / SEMESTER</small>
                <div class="fw-bold text-dark"><i class="ti ti-school me-1 text-primary"></i><?= esc($member['class_level'] ?? '-'); ?></div>
              </div>
            </div>
          <?php endif; ?>

          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">STATUS MEMBERSHIP</small>
              <div>
                <?php if (($tier['code'] ?? '') === 'living_library') : ?>
                  <span class="badge px-3 py-1 fs-2 fw-bold" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
                    <i class="ti ti-building-community me-1" style="color: #8b5e3c !important;"></i> Member Living Library
                  </span>
                <?php else : ?>
                  <span class="badge <?= $tier['badge']; ?> px-3 py-1 fs-2"><i class="ti <?= $tier['icon']; ?> me-1"></i> <?= esc($tier['name']); ?></span>
                <?php endif; ?>
              </div>
            </div>
          </div>


          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">BUKU DIDONASIKAN</small>
              <div class="fw-bold text-primary"><i class="ti ti-heart-handshake me-1"></i><?= $member['donated_books_count'] ?? 0; ?> Buku</div>
            </div>
          </div>

          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">JENIS KELAMIN</small>
              <div class="fw-bold text-dark"><?= ($member['gender'] == '1' || $member['gender'] == 'Male') ? 'Laki-laki' : 'Perempuan'; ?></div>
            </div>
          </div>

          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">TANGGAL LAHIR</small>
              <div class="fw-bold text-dark"><i class="ti ti-calendar me-1 text-primary"></i><?= esc($dobString); ?></div>
            </div>
          </div>

          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">EMAIL ANGGOTA</small>
              <div class="fw-bold text-dark"><i class="ti ti-mail me-1 text-primary"></i><?= esc(!empty($member['email']) && strpos($member['email'], 'student_') === false ? $member['email'] : '-'); ?></div>
            </div>
          </div>

          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">NOMOR TELEPON</small>
              <div class="fw-bold text-dark"><i class="ti ti-phone me-1 text-primary"></i><?= esc(!empty($member['phone']) && $member['phone'] !== '-' ? $member['phone'] : '-'); ?></div>
            </div>
          </div>

          <div class="col-12">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">ALAMAT LENGKAP</small>
              <div class="fw-bold text-dark"><i class="ti ti-map-pin me-1 text-primary"></i><?= esc(!empty($member['address']) ? $member['address'] : '-'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Barcode ID Card Card -->
  <div class="col-12 col-lg-4">
    <div class="card info-card border-0 text-center mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-barcode text-primary me-2"></i> Kode Barcode ID Card</h5>
        <div class="p-3 bg-white rounded-3 border shadow-sm mb-3">
          <div class="d-flex justify-content-center align-items-center mb-2 overflow-hidden" style="max-height: 65px;">
            <?= generateBarcodeSVG($member['uid'], 60); ?>
          </div>
          <strong class="font-monospace text-dark fs-4 d-block tracking-wider mt-1"><?= esc($member['uid']); ?></strong>
        </div>
        <span class="badge badge-subtle-primary fs-2 px-3 py-2">
          <i class="ti ti-scan me-1"></i> Siap Ditembak Scanner Fisik
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Donated Books Table Card -->
<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-dark mb-3">
      <i class="ti ti-heart-handshake text-primary me-2"></i> Daftar Buku yang Didonasikan (<?= count($donatedItems ?? []); ?> Salinan Fisik)
    </h5>
    
    <?php if (empty($donatedItems)) : ?>
      <div class="p-4 bg-light rounded-3 text-center text-muted border">
        <i class="ti ti-info-circle fs-6 d-block mb-1"></i>
        <b>Anggota ini belum mendonasikan salinan fisik buku.</b>
      </div>
    <?php else : ?>
      <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
        <table class="table table-hover align-middle table-assalafiyyah mb-0">
          <thead>
            <tr>
              <th scope="col" class="text-center" style="width: 50px;">#</th>
              <th scope="col">Kode Barcode</th>
              <th scope="col">Judul Buku</th>
              <th scope="col">Pengarang</th>
              <th scope="col">Rak Penempatan</th>
              <th scope="col" class="text-center">Kondisi</th>
              <th scope="col" class="text-center pe-4">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($donatedItems as $item) : ?>
              <tr>
                <th scope="row" class="col-index"><?= $i++; ?></th>
                <td><strong class="text-primary"><i class="ti ti-barcode me-1"></i><?= esc($item['item_code']); ?></strong></td>
                <td>
                  <a href="<?= base_url("admin/books/{$item['slug']}"); ?>" class="fw-bold text-dark text-decoration-none fs-3">
                    <?= esc($item['title']); ?> (<?= esc($item['year']); ?>)
                  </a>
                </td>
                <td><small class="text-muted"><i class="ti ti-user me-1"></i><?= esc($item['author']); ?></small></td>
                <td><span class="badge badge-subtle-secondary fs-2"><i class="ti ti-box me-1"></i><?= esc($item['rack_name'] ?? 'Belum diset'); ?></span></td>
                <td class="text-center">
                  <?php if ($item['condition'] === 'baik') : ?>
                    <span class="badge badge-subtle-primary fs-2 px-3 py-1">Baik</span>
                  <?php elseif ($item['condition'] === 'rusak') : ?>
                    <span class="badge badge-subtle-warning fs-2 px-3 py-1">Rusak</span>
                  <?php else : ?>
                    <span class="badge badge-subtle-danger fs-2 px-3 py-1">Hilang</span>
                  <?php endif; ?>
                </td>
                <td class="text-center pe-4">
                  <?php if ($item['status'] === 'tersedia') : ?>
                    <span class="badge badge-subtle-primary rounded-pill px-3 py-1">Tersedia</span>
                  <?php elseif ($item['status'] === 'dipinjam') : ?>
                    <span class="badge badge-subtle-warning rounded-pill px-3 py-1">Dipinjam</span>
                  <?php else : ?>
                    <span class="badge badge-subtle-secondary rounded-pill px-3 py-1">Diperbaiki</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>