<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Data Pemilik Kartu Member</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
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
          <i class="ti ti-id-badge-2 me-1"></i> Keanggotaan Prioritas
        </div>
        <h3 class="text-white fw-bold mb-1">Data Pemilik Kartu Member</h3>
        <p class="text-white-50 mb-0">Kelola cetak & penyerahan kartu keanggotaan Silver, Gold, dan Platinum Member.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <button class="btn btn-light text-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#assignMemberModal">
          <i class="ti ti-user-check me-1"></i> Penetapan Member Manual
        </button>
        <a href="<?= base_url('admin/members'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-users me-1"></i> Semua Anggota
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Modal Penetapan Member Manual -->
<div class="modal fade" id="assignMemberModal" tabindex="-1" aria-labelledby="assignMemberModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="assignMemberModalLabel"><i class="ti ti-user-check text-success me-1"></i> Penetapan Member Manual (Superadmin)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/members/cards/assign'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="member_id" class="form-label fw-semibold">Pilih Anggota</label>
            <select class="form-select select2" id="member_id" name="member_id" required>
              <option value="" disabled selected>--Pilih Anggota Terdaftar--</option>
              <?php foreach ($allMembers as $m) : ?>
                <option value="<?= $m['id']; ?>"><?= esc("{$m['first_name']} {$m['last_name']}"); ?> (<?= esc($m['email'] ?: $m['uid']); ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="manual_tier" class="form-label fw-semibold">Tingkatan Member yang Diangkat</label>
            <select class="form-select border-primary" id="manual_tier" name="manual_tier" required>
              <option value="living_library">🏫 Living Library / Paket Kelas (Maks 50 Buku, 90 Hari)</option>
              <option value="silver">Silver Member (Maks 1 Buku, 7 Hari, Novel)</option>
              <option value="gold">Gold Member (Maks 3 Buku, 10 Hari, Booking)</option>
              <option value="platinum">Platinum Member (Maks 5 Buku, 14 Hari, Prioritas)</option>
              <option value="none">Reguler / Batalkan Penetapan Manual</option>
            </select>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-pill-gold fw-bold px-4">Simpan Penetapan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Filter Tabs & Search Form -->
<div class="card info-card border-0 mb-4">
  <div class="card-body p-3">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-8">
        <div class="d-flex gap-2 flex-wrap" role="group">
          <a href="<?= base_url('admin/members/cards'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= empty($tierFilter) ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            Semua Member (<?= count($members); ?>)
          </a>
          <a href="<?= base_url('admin/members/cards?tier=none'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $tierFilter === 'none' ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <i class="ti ti-user me-1"></i> Reguler
          </a>
          <a href="<?= base_url('admin/members/cards?tier=living_library'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $tierFilter === 'living_library' ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <i class="ti ti-building-community me-1"></i> Living Library
          </a>
          <a href="<?= base_url('admin/members/cards?tier=silver'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $tierFilter === 'silver' ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <i class="ti ti-award me-1"></i> Silver
          </a>
          <a href="<?= base_url('admin/members/cards?tier=gold'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $tierFilter === 'gold' ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <i class="ti ti-medal me-1"></i> Gold
          </a>
          <a href="<?= base_url('admin/members/cards?tier=platinum'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold <?= $tierFilter === 'platinum' ? 'btn-pill-gold' : 'btn-outline-secondary'; ?>">
            <i class="ti ti-crown me-1"></i> Platinum
          </a>

        </div>
      </div>

      <div class="col-12 col-md-4">
        <form action="" method="get">
          <?php if (!empty($tierFilter)) : ?>
            <input type="hidden" name="tier" value="<?= esc($tierFilter); ?>">
          <?php endif; ?>
          <div class="input-group search-group">
            <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari nama member...">
            <button class="btn btn-primary fw-semibold" type="submit"><i class="ti ti-search"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php if (empty($members)) : ?>
  <div class="card info-card border-0">
    <div class="card-body p-5 text-center text-muted">
      <i class="ti ti-info-circle fs-7 d-block mb-2"></i>
      <b>Belum ada anggota yang memenuhi kriteria kartu member ini.</b>
    </div>
  </div>
<?php else : ?>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php foreach ($members as $m) : ?>
      <?php $tier = $m['tier']; ?>
      <div class="col">
        <div class="card info-card border-0 h-100 position-relative overflow-hidden shadow-sm rounded-4">
          <!-- Top Accent Bar with Warm Theme Gradient -->
          <div style="height: 5px; background: linear-gradient(90deg, #c59b27 0%, #8b5e3c 100%);"></div>

          <div class="card-body p-4 d-flex flex-column justify-content-between">
            <div>
              <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <span class="badge <?= $tier['badge']; ?> px-3 py-2 fs-2 shadow-sm">
                  <i class="ti <?= $tier['icon']; ?> me-1"></i> <?= esc($tier['name']); ?>
                </span>
                <span class="badge badge-subtle-primary fs-2 px-3 py-2">
                  <i class="ti ti-heart-handshake me-1"></i><?= $m['donated_books_count']; ?> Buku Donasi
                </span>
              </div>

              <div class="d-flex align-items-center mb-3 p-3 bg-light rounded-3 border">
                <div class="overflow-hidden w-100">
                  <h5 class="fw-bold text-dark mb-1 fs-3 text-truncate"><?= esc("{$m['first_name']} {$m['last_name']}"); ?></h5>
                  <?php if (($tier['code'] ?? '') === 'living_library') : ?>
                    <span class="badge bg-primary text-white fw-bold px-2 py-1 fs-1 rounded">
                      <i class="ti ti-building-community me-1"></i>Member Living Library
                    </span>
                  <?php else : ?>
                    <small class="text-muted d-block text-truncate"><i class="ti ti-id-badge-2 me-1"></i>ID Card: <strong><?= esc($m['uid'] ?? '-'); ?></strong></small>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div class="mt-2">
              <a href="<?= base_url("admin/members/cards/{$m['id']}"); ?>" class="btn btn-pill-gold btn-sm w-100 fw-bold py-2 shadow-sm">
                <i class="ti ti-eye me-1"></i> Lihat Detail Kartu Member
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?= $this->endSection() ?>
