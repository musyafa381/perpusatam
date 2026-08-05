<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Buku Tamu & Booking Reservasi Buku</title>
<style>
  .trans-nav .nav-link {
    color: #6e4727 !important;
    background-color: #f7f3ed !important;
    border: 1.5px solid #e2d5c3 !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 10px 22px !important;
    transition: all 0.25s ease;
  }
  .trans-nav .nav-link:hover {
    background-color: #eee4d5 !important;
  }
  .trans-nav .nav-link.active {
    background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%) !important;
    color: #ffffff !important;
    border-color: #6e4727 !important;
    box-shadow: 0 4px 12px rgba(110, 71, 39, 0.25) !important;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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
          <i class="ti ti-id-badge-2 me-1"></i> MANAJEMEN KUNJUNGAN & RESERVASI
        </div>
        <h3 class="text-white fw-bold mb-1">Buku Tamu & Booking Reservasi</h3>
        <p class="text-white-50 mb-0">Kelola riwayat presensi pengunjung perpustakaan dan antrean booking buku anggota.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('buku-tamu'); ?>" target="_blank" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-external-link me-1"></i> Buka Kiosk Buku Tamu
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Main Card Container with Tabs -->
<div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff; border: 1.5px solid #e8decb !important;">
  <div class="card-header bg-transparent border-bottom p-3 p-md-4" style="border-color: #e8decb !important;">
    <ul class="nav nav-pills trans-nav gap-2" id="visitorTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= ($activeTab === 'visitors' || empty($activeTab)) ? 'active' : ''; ?>" id="tab-visitors-tab" data-bs-toggle="tab" data-bs-target="#tab-visitors" type="button" role="tab">
          <i class="ti ti-id-badge-2 me-1.5 fs-5"></i> Buku Tamu & Kunjungan (<?= count($logs); ?>)
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link <?= ($activeTab === 'reservations') ? 'active' : ''; ?>" id="tab-reservations-tab" data-bs-toggle="tab" data-bs-target="#tab-reservations" type="button" role="tab">
          <i class="ti ti-bookmark me-1.5 fs-5"></i> Booking & Reservasi Buku (<?= count($reservations); ?>)
        </button>
      </li>
    </ul>
  </div>

  <div class="card-body p-4">
    <div class="tab-content" id="visitorTabsContent">
      
      <!-- TAB 1: BUKU TAMU / KUNJUNGAN -->
      <div class="tab-pane fade <?= ($activeTab === 'visitors' || empty($activeTab)) ? 'show active' : ''; ?>" id="tab-visitors" role="tabpanel">
        
        <!-- Card Kontrol Sesi Kehadiran -->
        <div class="card info-card border-0 mb-4" style="background: linear-gradient(135deg, #fcf8f2 0%, #f8f2e6 100%); border: 1px solid #e8decb !important;">
          <div class="card-body p-3.5 p-md-4">
            <div class="row align-items-center gy-3">
              <div class="col-lg-6">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 46px; height: 46px; background: linear-gradient(135deg, #6e4727, #8b5e3c);">
                    <i class="ti ti-clock-play fs-4"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold mb-1" style="color: #6e4727;">Status Sesi Kunjungan Hari Ini</h6>
                    <?php if (!empty($activeSession)) : ?>
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold fs-2"><i class="ti ti-point-filled me-1"></i> DIBUKA</span>
                        <strong class="text-dark fs-3"><?= esc($activeSession['session_name']); ?></strong>
                      </div>
                    <?php else : ?>
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-danger text-white px-2.5 py-1 rounded-pill fw-bold fs-2"><i class="ti ti-circle-x me-1"></i> DITUTUP</span>
                        <span class="text-muted fs-3">Belum ada sesi presensi yang dibuka hari ini</span>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 text-lg-end">
                <?php if (!empty($activeSession)) : ?>
                  <form action="<?= base_url("admin/visitors/sessions/close/{$activeSession['id']}"); ?>" method="post" class="d-inline">
                    <?= csrf_field(); ?>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-pill px-3 py-2 shadow-sm" onclick="return confirm('Apakah Anda yakin ingin menutup sesi presensi ini?')">
                      <i class="ti ti-player-stop me-1"></i> Tutup Sesi '<?= esc($activeSession['session_name']); ?>'
                    </button>
                  </form>
                <?php endif; ?>
                <button type="button" class="btn btn-pill-gold btn-sm fw-bold rounded-pill px-3 py-2 shadow-sm ms-2" data-bs-toggle="modal" data-bs-target="#openSessionModal">
                  <i class="ti ti-plus me-1"></i> Buka Sesi Baru
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter Presensi -->
        <form action="<?= base_url('admin/visitors'); ?>" method="get" class="row g-3 align-items-end mb-4">
          <input type="hidden" name="tab" value="visitors">
          <div class="col-12 col-md-3">
            <label for="start_date" class="form-label fw-semibold">Tanggal Mulai</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($startDate); ?>">
          </div>
          <div class="col-12 col-md-3">
            <label for="end_date" class="form-label fw-semibold">Tanggal Selesai</label>
            <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($endDate); ?>">
          </div>
          <div class="col-12 col-md-3">
            <label for="institution" class="form-label fw-semibold">Instansi</label>
            <select class="form-select" id="institution" name="institution">
              <option value="">-- Semua Instansi --</option>
              <option value="MTs" <?= $institution === 'MTs' ? 'selected' : ''; ?>>MTs</option>
              <option value="MA" <?= $institution === 'MA' ? 'selected' : ''; ?>>MA</option>
              <option value="SMK" <?= $institution === 'SMK' ? 'selected' : ''; ?>>SMK</option>
              <option value="PAUD" <?= $institution === 'PAUD' ? 'selected' : ''; ?>>PAUD</option>
              <option value="PDF" <?= $institution === 'PDF' ? 'selected' : ''; ?>>PDF</option>
              <option value="Ma'had Aly" <?= $institution === "Ma'had Aly" ? 'selected' : ''; ?>>Ma'had Aly</option>
            </select>
          </div>
          <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-pill-gold fw-bold w-100 shadow-sm"><i class="ti ti-filter me-1"></i> Filter</button>
            <a href="<?= base_url('admin/visitors?tab=visitors'); ?>" class="btn btn-light fw-semibold border"><i class="ti ti-refresh"></i></a>
          </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold text-dark mb-0">Total Ditemukan: <?= count($logs); ?> Kunjungan</h6>
          <span class="badge btn-pill-brown px-3 py-1.5 fs-2 fw-bold">Hari Ini: <?= $todayCount; ?> Kunjungan</span>
        </div>

        <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
          <table class="table table-hover align-middle table-assalafiyyah mb-0">
            <thead>
              <tr>
                <th scope="col" class="text-center" style="width: 50px;">#</th>
                <th scope="col">Nama Pengunjung</th>
                <th scope="col">Waktu & Tanggal</th>
                <th scope="col">Kode Barcode UID</th>
                <th scope="col">Sesi Kunjungan</th>
                <th scope="col">Instansi</th>
                <th scope="col">Kelas / Semester</th>
                <th scope="col" class="text-center pe-4">Tipe Anggota</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1 + ($itemPerPage * ($currentPage - 1));
              ?>
              <?php if (empty($logs)) : ?>
                <tr>
                  <td colspan="8" class="text-center py-4 text-muted">
                    <i class="ti ti-info-circle fs-6 d-block mb-1"></i>
                    Belum ada data kunjungan pada rentang tanggal tersebut.
                  </td>
                </tr>
              <?php else : ?>
                <?php foreach ($logs as $log) : ?>
                  <tr>
                    <th scope="row" class="col-index"><?= $i++; ?></th>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="table-avatar-initial">
                          <?= strtoupper(substr($log['visitor_name'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="fw-bold text-dark fs-3"><?= esc($log['visitor_name']); ?></div>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold text-dark"><?= \CodeIgniter\I18n\Time::parse($log['created_at'], locale: 'id')->toLocalizedString('dd/MM/y'); ?></div>
                      <small class="text-muted"><i class="ti ti-clock me-1"></i><?= \CodeIgniter\I18n\Time::parse($log['created_at'], locale: 'id')->toLocalizedString('HH:mm'); ?></small>
                    </td>
                    <td>
                      <?php if (!empty($log['uid'])) : ?>
                        <code class="font-monospace fw-bold px-2 py-1 rounded" style="background-color: #f8f2e6; color: #6e4727; border: 1px solid #e8decb;"><?= esc($log['uid']); ?></code>
                      <?php else : ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border px-2 py-1 fw-bold"><?= esc($log['session_name'] ?: 'Umum'); ?></span>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold"><?= esc($log['institution'] ?: 'Umum'); ?></span>
                    </td>
                    <td class="fw-semibold text-dark">
                      <?= esc($log['class_level'] ?: '-'); ?>
                    </td>
                    <td class="text-center pe-4">
                      <span class="badge badge-subtle-primary text-capitalize px-3 py-2 rounded-pill fs-2"><?= esc($log['member_type'] ?? 'Pengunjung'); ?></span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="mt-4 d-flex justify-content-center">
          <?= $pager->links('visitors', 'my_pager'); ?>
        </div>
      </div>

      <!-- TAB 2: BOOKING / RESERVASI -->
      <div class="tab-pane fade <?= ($activeTab === 'reservations') ? 'show active' : ''; ?>" id="tab-reservations" role="tabpanel">
        
        <!-- Filter Status & Search Reservasi -->
        <div class="row g-2 align-items-center mb-3">
          <div class="col-12 col-md-8">
            <div class="d-flex gap-2 flex-wrap" role="group">
              <a href="<?= base_url('admin/visitors?tab=reservations'); ?>" class="btn btn-outline-primary btn-sm rounded-3 <?= empty($statusFilter) ? 'active fw-bold' : ''; ?>">
                Semua Antrean (<?= count($reservations); ?>)
              </a>
              <a href="<?= base_url('admin/visitors?tab=reservations&status=pending'); ?>" class="btn btn-outline-warning btn-sm rounded-3 <?= $statusFilter === 'pending' ? 'active fw-bold' : ''; ?>">
                <i class="ti ti-clock me-1"></i> Pending
              </a>
              <a href="<?= base_url('admin/visitors?tab=reservations&status=fulfilled'); ?>" class="btn btn-outline-success btn-sm rounded-3 <?= $statusFilter === 'fulfilled' ? 'active fw-bold' : ''; ?>">
                <i class="ti ti-check me-1"></i> Fulfilled
              </a>
              <a href="<?= base_url('admin/visitors?tab=reservations&status=cancelled'); ?>" class="btn btn-outline-secondary btn-sm rounded-3 <?= $statusFilter === 'cancelled' ? 'active fw-bold' : ''; ?>">
                <i class="ti ti-x me-1"></i> Dibatalkan
              </a>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <form action="<?= base_url('admin/visitors'); ?>" method="get">
              <input type="hidden" name="tab" value="reservations">
              <?php if (!empty($statusFilter)) : ?>
                <input type="hidden" name="status" value="<?= esc($statusFilter); ?>">
              <?php endif; ?>
              <div class="input-group">
                <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari member / buku...">
                <button class="btn btn-primary fw-semibold" type="submit"><i class="ti ti-search"></i></button>
              </div>
            </form>
          </div>
        </div>

        <?php if (empty($reservations)) : ?>
          <div class="text-center py-4 text-muted border rounded-3 bg-light">
            <i class="ti ti-info-circle fs-6 d-block mb-1"></i>
            <b>Tidak ada data antrean booking ditemukan.</b>
          </div>
        <?php else : ?>
          <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
            <table class="table table-hover align-middle table-assalafiyyah mb-0">
              <thead>
                <tr>
                  <th scope="col" class="ps-3">#</th>
                  <th scope="col">Anggota / Pemesan</th>
                  <th scope="col">Tier Member</th>
                  <th scope="col">Judul Buku</th>
                  <th scope="col">Tanggal Reservasi</th>
                  <th scope="col">Status Stock</th>
                  <th scope="col">Status Reservasi</th>
                  <th scope="col" class="text-center pe-3">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservations as $idx => $r) : ?>
                  <tr>
                    <td class="ps-3 fw-bold text-muted"><?= $idx + 1; ?></td>
                    <td>
                      <div class="fw-bold text-dark"><?= esc($r['first_name'] . ' ' . $r['last_name']); ?></div>
                      <small class="text-muted">UID: <?= esc($r['member_uid']); ?></small>
                    </td>
                    <td>
                      <span class="badge rounded-pill px-2.5 py-1 text-white fw-bold" style="background: <?= esc($r['tier']['badge_color'] ?? '#6c757d'); ?>;">
                        <?= esc($r['tier']['name'] ?? 'Member'); ?>
                      </span>
                    </td>
                    <td>
                      <div class="fw-bold text-dark text-truncate" style="max-width: 200px;"><?= esc($r['book_title']); ?></div>
                      <small class="text-muted">ID Buku: #<?= $r['book_id']; ?></small>
                    </td>
                    <td>
                      <div class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($r['created_at'])); ?></div>
                    </td>
                    <td>
                      <?php if ($r['available_stock'] > 0) : ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">
                          <i class="ti ti-circle-check me-1"></i>Stok Ada (<?= $r['available_stock']; ?>)
                        </span>
                      <?php else : ?>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">
                          <i class="ti ti-circle-x me-1"></i>Stok Habis
                        </span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($r['status'] === 'pending') : ?>
                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill fw-bold">Pending</span>
                      <?php elseif ($r['status'] === 'fulfilled') : ?>
                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill fw-bold">Fulfilled</span>
                      <?php else : ?>
                        <span class="badge bg-secondary text-white px-2.5 py-1 rounded-pill fw-bold">Cancelled</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center pe-3">
                      <?php if ($r['status'] === 'pending') : ?>
                        <form action="<?= base_url('admin/reservations/' . $r['id'] . '/fulfill'); ?>" method="post" class="d-inline">
                          <?= csrf_field(); ?>
                          <button type="submit" class="btn btn-sm btn-success rounded-2 me-1" title="Proses Pinjam">
                            <i class="ti ti-check"></i> Process
                          </button>
                        </form>
                        <form action="<?= base_url('admin/reservations/' . $r['id'] . '/cancel'); ?>" method="post" class="d-inline">
                          <?= csrf_field(); ?>
                          <button type="submit" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Batalkan reservasi ini?')" title="Batalkan">
                            <i class="ti ti-x"></i>
                          </button>
                        </form>
                      <?php else: ?>
                        <span class="text-muted fs-7">-</span>
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
  </div>
</div>

<!-- Modal Buka Sesi Baru -->
<div class="modal fade" id="openSessionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg" style="background-color: #fcf8f2;">
      <form action="<?= base_url('admin/visitors/sessions/open'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title fw-bold" style="color: #6e4727;"><i class="ti ti-clock-play me-2"></i> Buka Sesi Kunjungan Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4">
          <label class="form-label fw-bold" style="color: #6e4727;">Pilih atau Ketik Nama Sesi</label>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('session_name_input').value='Sesi Pagi (08:00 - 12:00)'">Sesi Pagi</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('session_name_input').value='Sesi Siang (13:00 - 16:00)'">Sesi Siang</button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="document.getElementById('session_name_input').value='Sesi Malam (19:30 - 21:00)'">Sesi Malam</button>
          </div>
          <input type="text" name="session_name" id="session_name_input" class="form-control form-control-lg border-warning shadow-sm" placeholder="Contoh: Sesi Pagi Jam Buka Perpus" required>
          <small class="text-muted d-block mt-2">Membuka sesi baru akan otomatis menutup sesi sebelumnya pada hari ini.</small>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-pill-gold fw-bold px-4"><i class="ti ti-check me-1"></i> Buka Sesi Sekarang</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
