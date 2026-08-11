<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Detail Kartu Member - <?= $member['first_name'] . ' ' . $member['last_name']; ?></title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@700;800;900&family=Montserrat:wght@700;800;900&display=swap');

  .card-template-wrapper {
    position: relative;
    width: 100%;
    max-width: 520px;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    margin: 0 auto;
  }

  .card-template-img {
    width: 100%;
    height: auto;
    display: block;
  }

  .card-overlay-code {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    letter-spacing: 2px;
    background: transparent;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    z-index: 10;
  }

  .card-overlay-name {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    background: transparent;
    z-index: 10;
  }

  /* Platinum Member Card Positioning & Serif Typography */
  .card-tier-platinum .card-overlay-code {
    font-family: 'Cinzel', 'Trajan Pro', 'Georgia', serif;
    top: 50.5%;
    left: 6.8%;
    width: 37%;
    height: 11%;
    color: #f7e39a;
    font-size: clamp(0.55rem, 1.6vw, 0.88rem);
    letter-spacing: 0.5px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.95);
    padding: 0 2px;
  }

  .card-tier-platinum .card-overlay-name {
    font-family: 'Cinzel', 'Trajan Pro', 'Georgia', serif;
    top: 63.5%;
    left: 6.5%;
    width: 38%;
    height: 10%;
    color: #f7e39a;
    font-size: clamp(0.6rem, 1.8vw, 0.95rem);
    letter-spacing: 1px;
    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.95);
  }

  /* Silver Member Card Positioning & Modern Sans-Serif Typography */
  .card-tier-silver .card-overlay-code {
    font-family: 'Montserrat', 'Outfit', 'Inter', 'Arial', sans-serif;
    top: 68.5%;
    left: 31%;
    width: 38%;
    height: 11%;
    color: #1e1e1e;
    font-size: clamp(0.55rem, 1.6vw, 0.88rem);
    letter-spacing: 0.5px;
    text-shadow: none;
    padding: 0 2px;
  }

  .card-tier-silver .card-overlay-name {
    font-family: 'Montserrat', 'Outfit', 'Inter', 'Arial', sans-serif;
    top: 56.0%;
    left: 20%;
    width: 60%;
    height: 8%;
    color: #d4af37;
    font-size: clamp(0.6rem, 1.8vw, 0.95rem);
    letter-spacing: 1px;
    text-shadow: none;
  }

  /* Gold Member Card Positioning & Modern Sans-Serif Typography */
  .card-tier-gold .card-overlay-code {
    font-family: 'Montserrat', 'Outfit', 'Inter', 'Arial', sans-serif;
    top: 60.5%;
    left: 49%;
    width: 35%;
    height: 11%;
    color: #2d3748;
    font-size: clamp(0.55rem, 1.6vw, 0.88rem);
    letter-spacing: 0.5px;
    text-shadow: none;
    padding: 0 2px;
  }

  .card-tier-gold .card-overlay-name {
    font-family: 'Montserrat', 'Outfit', 'Inter', 'Arial', sans-serif;
    top: 73.5%;
    left: 42%;
    width: 49%;
    height: 10%;
    color: #2d3748;
    font-size: clamp(0.6rem, 1.8vw, 0.95rem);
    letter-spacing: 1px;
    text-shadow: none;
  }

  @media print {
    body * {
      visibility: hidden !important;
    }
    .printable-card-area, .printable-card-area * {
      visibility: visible !important;
    }
    .printable-card-area {
      position: fixed !important;
      left: 50% !important;
      top: 30% !important;
      transform: translate(-50%, -50%) !important;
      width: 100% !important;
      max-width: 500px !important;
    }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-4">
  <div>
    <a href="<?= base_url('admin/members/cards'); ?>" class="btn btn-light text-primary fw-bold shadow-sm me-2">
      <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Kartu Member
    </a>
  </div>
  <div>
    <a href="<?= base_url("admin/members/{$member['uid']}"); ?>" class="btn btn-outline-secondary rounded-pill">
      <i class="ti ti-user me-1"></i> Lihat Profil Lengkap Anggota
    </a>
  </div>
</div>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<div class="row g-4">
  <!-- Kolom Kiri: Kartu Visual & Dropdown Status -->
  <div class="col-12 col-lg-6">
    <!-- Visual Kartu Member Card -->
    <div class="card mb-4 border border-2 border-primary-subtle shadow-sm rounded-4">
      <div class="card-body text-center p-4">
        <h5 class="card-title fw-bold text-dark mb-3"><i class="ti ti-id-badge-2 text-primary me-1"></i> Desain Visual Kartu Member</h5>
        
        <?php
        $tierCode = strtolower($tier['code'] ?? 'silver');
        $cardImageName = 'silver_card.png';
        if ($tierCode === 'gold') {
            $cardImageName = 'gold_card.png';
        } elseif ($tierCode === 'platinum') {
            $cardImageName = 'platinum_card.png';
        }
        ?>

        <div class="printable-card-area">
          <?php if ($tierCode === 'living_library') : ?>
            <div class="card-template-wrapper mb-3 p-4 rounded-4 shadow text-center position-relative" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); color: #ffffff; min-height: 220px; border: 2px solid #6e4727;">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-white text-dark fw-bold px-3 py-1 fs-2 shadow-sm"><i class="ti ti-building-community me-1"></i>KARTU KELAS</span>
                <span class="fw-bold fs-2 tracking-wider text-warning">LIVING LIBRARY</span>
              </div>
              <div class="my-3 py-2">
                <h3 class="fw-extrabold text-white mb-1 tracking-wide" style="font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.4);"><?= esc(strtoupper("{$member['first_name']} {$member['last_name']}")); ?></h3>
                <span class="badge px-3 py-1 fs-3 rounded-pill fw-bold" style="background-color: #f8f2e6 !important; color: #6e4727 !important;">Member Living Library</span>
              </div>
              <div class="mt-3 pt-2 border-top border-white-50 d-flex justify-content-between align-items-center fs-2 text-white-50">
                <span>BARCODE ID: <strong class="text-white"><?= esc($member['uid']); ?></strong></span>
                <span class="text-white">Maks: 50 Buku / 90 Hari</span>
              </div>
            </div>
          <?php elseif ($tierCode === 'none') : ?>
            <div class="card-template-wrapper mb-3 p-4 rounded-4 shadow text-center position-relative" style="background: linear-gradient(135deg, #f8f2e6 0%, #e8decb 100%); color: #6e4727; min-height: 220px; border: 2px solid #d4c4a8;">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-white text-secondary fw-bold px-3 py-1 fs-2 shadow-sm"><i class="ti ti-user me-1"></i>KARTU ANGGOTA</span>
                <span class="fw-bold fs-2 tracking-wider text-muted">REGULER</span>
              </div>
              <div class="my-3 py-2">
                <h3 class="fw-bold text-dark mb-1 tracking-wide" style="font-size: 1.4rem;"><?= esc(strtoupper("{$member['first_name']} {$member['last_name']}")); ?></h3>
                <span class="badge bg-secondary text-white px-3 py-1 fs-3 rounded-pill">Anggota Reguler</span>
              </div>
              <div class="mt-3 pt-2 border-top border-secondary-subtle d-flex justify-content-between align-items-center fs-2 text-muted">
                <span>BARCODE ID: <strong class="text-dark"><?= esc($member['uid']); ?></strong></span>
                <span>Maks: 1 Buku / 7 Hari</span>
              </div>
            </div>
          <?php else : ?>
            <div class="card-template-wrapper mb-3 position-relative rounded-4 overflow-hidden shadow card-tier-<?= $tierCode; ?>">
              <img src="<?= base_url('assets/images/cards/' . $cardImageName); ?>" alt="Kartu Member <?= $tier['name']; ?>" class="card-template-img">
              
              <!-- Dynamic Member Code Barcode Overlay -->
              <div class="card-overlay-code">
                <?= esc($member['uid']); ?>
              </div>

              <!-- Dynamic Member Full Name Overlay -->
              <div class="card-overlay-name">
                <?= esc(strtoupper("{$member['first_name']} {$member['last_name']}")); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>



        <div class="d-flex justify-content-center align-items-center gap-2 mt-3 flex-wrap">
          <span class="badge <?= $tier['badge']; ?> px-3 py-2 fs-3">
            <i class="ti <?= $tier['icon']; ?> me-1"></i> <?= $tier['name']; ?>
          </span>
          <button type="button" class="btn btn-pill-gold btn-sm px-4 fw-bold shadow-sm" onclick="window.print();">
            <i class="ti ti-printer me-1"></i> Cetak Kartu Member
          </button>
        </div>
      </div>
    </div>

    <!-- Form Dropdown Status Pencetakan & Penyerahan -->
    <div class="card border border-2 border-warning-subtle shadow-sm rounded-4">
      <div class="card-body p-4">
        <h5 class="card-title fw-bold text-dark mb-3"><i class="ti ti-adjustments text-warning me-1"></i> Kelola Status Pencetakan & Penyerahan</h5>
        
        <form action="<?= base_url("admin/members/cards/{$member['id']}/status"); ?>" method="post">
          <?= csrf_field(); ?>
          
          <div class="mb-3">
            <label for="manual_tier" class="form-label fw-semibold">Penetapan Tingkatan Member (Superadmin Override)</label>
            <?php $currentManualTier = $member['manual_tier'] ?? 'none'; ?>
            <select class="form-select border-2 border-primary" id="manual_tier" name="manual_tier">
              <option value="none" <?= $currentManualTier === 'none' ? 'selected' : ''; ?>>Otomatis (Berdasarkan Donasi Buku)</option>
              <option value="living_library" <?= $currentManualTier === 'living_library' ? 'selected' : ''; ?>>🏫 Living Library (Paket Kelas)</option>
              <option value="silver" <?= $currentManualTier === 'silver' ? 'selected' : ''; ?>>Silver Member (Manual)</option>
              <option value="gold" <?= $currentManualTier === 'gold' ? 'selected' : ''; ?>>Gold Member (Manual)</option>
              <option value="platinum" <?= $currentManualTier === 'platinum' ? 'selected' : ''; ?>>Platinum Member (Manual)</option>
            </select>

            <div class="form-text fs-1">Mengabaikan ambang batas donasi buku jika dipilih manual.</div>
          </div>

          <div class="mb-3">
            <label for="card_print_status" class="form-label fw-semibold">Status Pencetakan Kartu Fisik</label>
            <?php $printStatus = $member['card_print_status'] ?? 'belum_dicetak'; ?>
            <select class="form-select border-2 <?php if ($printStatus === 'sudah_dicetak') : ?>border-primary text-primary fw-bold<?php else : ?>border-secondary text-secondary<?php endif; ?>" id="card_print_status" name="card_print_status">
              <option value="belum_dicetak" <?= $printStatus === 'belum_dicetak' ? 'selected' : ''; ?>>🔴 Belum Dicetak</option>
              <option value="sudah_dicetak" <?= $printStatus === 'sudah_dicetak' ? 'selected' : ''; ?>>🟢 Sudah Dicetak</option>
            </select>
          </div>

          <div class="mb-4">
            <label for="card_delivery_status" class="form-label fw-semibold">Status Penyerahan Kartu ke Member</label>
            <?php $deliveryStatus = $member['card_delivery_status'] ?? 'menunggu'; ?>
            <select class="form-select border-2 <?php if ($deliveryStatus === 'sudah_diberikan') : ?>border-primary text-primary fw-bold<?php else : ?>border-warning text-warning fw-bold<?php endif; ?>" id="card_delivery_status" name="card_delivery_status">
              <option value="menunggu" <?= $deliveryStatus === 'menunggu' ? 'selected' : ''; ?>>⏳ Menunggu Penyerahan (Belum Diberikan)</option>
              <option value="sudah_diberikan" <?= $deliveryStatus === 'sudah_diberikan' ? 'selected' : ''; ?>>✅ Sudah Diberikan</option>
            </select>
          </div>

          <button type="submit" class="btn btn-pill-gold w-100 fw-bold py-2">
            <i class="ti ti-device-floppy me-1"></i> Simpan Status Kartu
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Data Diri & Feedback Privileges -->
  <div class="col-12 col-lg-6">
    <!-- Data Diri Pemilik Kartu Member -->
    <div class="card mb-4 shadow-sm rounded-4 border-0">
      <div class="card-body p-4">
        <h5 class="card-title fw-bold text-dark mb-3"><i class="ti ti-user-check text-primary me-1"></i> Data Diri Pemilik Kartu Member</h5>
        
        <table class="table table-borderless table-sm fs-3 mb-0">
          <tr>
            <td style="width: 160px; color: #8b5e3c !important;" class="fw-semibold">Nama Lengkap</td>
            <td style="width: 10px;">:</td>
            <td><strong class="text-dark fs-4"><?= $member['first_name'] . ' ' . $member['last_name']; ?></strong></td>
          </tr>
          <tr>
            <td style="color: #8b5e3c !important;" class="fw-semibold">Tipe Anggota</td>
            <td>:</td>
            <td>
              <?php if (($member['member_type'] ?? 'siswa') === 'siswa') : ?>
                <span class="badge badge-subtle-primary">Siswa / Santri</span>
              <?php else : ?>
                <span class="badge badge-subtle-primary">Petugas / Staf</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php if (($member['member_type'] ?? 'siswa') === 'siswa') : ?>
            <tr>
              <td style="color: #8b5e3c !important;" class="fw-semibold">Instansi & Kelas</td>
              <td>:</td>
              <td><strong><?= $member['institution'] ?? '-'; ?> <?= !empty($member['class_level']) ? '(' . $member['class_level'] . ')' : ''; ?></strong></td>
            </tr>
          <?php endif; ?>
          <tr>
            <td style="color: #8b5e3c !important;" class="fw-semibold">Total Donasi Buku</td>
            <td>:</td>
            <td><span class="badge badge-subtle-secondary fw-bold"><i class="ti ti-heart-handshake me-1"></i><?= $member['donated_books_count'] ?? 0; ?> Buku</span></td>
          </tr>
          <tr>
            <td style="color: #8b5e3c !important;" class="fw-semibold">Jenis Kelamin</td>
            <td>:</td>
            <td><?= $member['gender'] == '1' || $member['gender'] == 'Male' ? 'Laki-laki' : 'Perempuan'; ?></td>
          </tr>
          <?php if (!empty($member['email']) && strpos($member['email'], 'student_') === false) : ?>
            <tr>
              <td style="color: #8b5e3c !important;" class="fw-semibold">Email</td>
              <td>:</td>
              <td><?= $member['email']; ?></td>
            </tr>
          <?php endif; ?>
          <?php if (!empty($member['phone']) && $member['phone'] !== '-') : ?>
            <tr>
              <td style="color: #8b5e3c !important;" class="fw-semibold">Nomor Telepon</td>
              <td>:</td>
              <td><?= $member['phone']; ?></td>
            </tr>
          <?php endif; ?>
        </table>
      </div>
    </div>

    <!-- Feedback & Hak Akses Memiliki Kartu Member -->
    <div class="card border border-2 border-primary-subtle shadow-sm mb-4 rounded-4">
      <div class="card-body p-4">
        <h5 class="card-title fw-bold text-dark mb-3">
          <i class="ti ti-gift text-primary me-1"></i> Feedback & Hak Akses <?= $tier['name']; ?>
        </h5>
        <p class="text-muted fs-2 mb-3">Berikut adalah keuntungan dan batas peminjaman yang didapatkan oleh pemilik kartu ini:</p>

        <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-book-2 text-primary me-2"></i> Akses Peminjaman Buku Novel</span>
            <span class="badge badge-subtle-primary fw-bold">✅ Boleh Meminjam</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-books text-primary me-2"></i> Batas Maksimal Pinjaman Aktif</span>
            <span class="badge badge-subtle-primary fs-3"><?= $tier['max_loans']; ?> Buku</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-clock text-primary me-2"></i> Batas Maksimal Durasi Pinjam</span>
            <span class="badge badge-subtle-warning fs-3"><?= $tier['max_days']; ?> Hari</span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-bookmark text-primary me-2"></i> Fitur Booking / Reservasi Buku</span>
            <?php if ($tier['can_book']) : ?>
              <span class="badge badge-subtle-primary fw-bold">✅ Aktif</span>
            <?php else : ?>
              <span class="badge badge-subtle-secondary">❌ Belum Tersedia</span>
            <?php endif; ?>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-star text-primary me-2"></i> Layanan Prioritas Utama</span>
            <?php if (!empty($tier['is_priority'])) : ?>
              <span class="badge badge-subtle-warning fw-bold">⭐ Ya (Prioritas Utama)</span>
            <?php else : ?>
              <span class="badge badge-subtle-secondary">Standar</span>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
      </div>
    </div>

    <!-- Ringkasan Salinan Fisik yang Didonasikan -->
    <?php if (!empty($donatedItems)) : ?>
      <div class="card shadow-sm">
        <div class="card-body p-4">
          <h5 class="card-title fw-bold text-dark mb-3"><i class="ti ti-heart-handshake text-danger me-1"></i> Buku yang Didonasikan (<?= count($donatedItems); ?> Buku)</h5>
          <ul class="list-group list-group-numbered fs-2">
            <?php foreach ($donatedItems as $item) : ?>
              <li class="list-group-item d-flex justify-content-between align-items-start">
                <div class="ms-2 me-auto">
                  <div class="fw-bold"><?= $item['title']; ?> (<?= $item['year']; ?>)</div>
                  <small class="text-muted">Kode: <?= $item['item_code']; ?> | Rak: <?= $item['rack_name'] ?? '-'; ?></small>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>
