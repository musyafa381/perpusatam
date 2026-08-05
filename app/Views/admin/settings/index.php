<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Pengaturan Perpustakaan</title>
<style>
  .settings-nav .nav-link {
    color: #6e4727 !important;
    background-color: #f7f3ed !important;
    border: 1.5px solid #e2d7c5 !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 10px 22px !important;
    transition: all 0.25s ease;
  }
  .settings-nav .nav-link:hover {
    background-color: #eee4d5 !important;
    transform: translateY(-1px);
  }
  .settings-nav .nav-link.active {
    background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%) !important;
    color: #ffffff !important;
    border-color: #6e4727 !important;
    box-shadow: 0 4px 12px rgba(110, 71, 39, 0.25) !important;
  }

  .day-checkbox-input {
    display: none !important;
  }
  .day-checkbox-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 20px;
    border: 1.5px solid #e0d5c3;
    background: #ffffff;
    font-size: 0.85rem;
    font-weight: 600;
    color: #5a4636;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
  }
  .day-checkbox-label:hover {
    border-color: #8b5e3c;
    background: #faf6f0;
  }

  .day-checkbox-input-female:checked + .day-checkbox-label {
    background: #d81b60 !important;
    color: #ffffff !important;
    border-color: #c2185b !important;
    box-shadow: 0 3px 8px rgba(194, 24, 91, 0.3) !important;
  }
  .day-checkbox-input-male:checked + .day-checkbox-label {
    background: #1976d2 !important;
    color: #ffffff !important;
    border-color: #1565c0 !important;
    box-shadow: 0 3px 8px rgba(21, 101, 192, 0.3) !important;
  }
  .day-checkbox-input-closed:checked + .day-checkbox-label {
    background: #d32f2f !important;
    color: #ffffff !important;
    border-color: #c62828 !important;
    box-shadow: 0 3px 8px rgba(198, 40, 40, 0.3) !important;
  }
  .day-checkbox-input-type:checked + .day-checkbox-label {
    background: #6e4727 !important;
    color: #ffffff !important;
    border-color: #5d3d23 !important;
    box-shadow: 0 3px 8px rgba(110, 71, 39, 0.3) !important;
  }

  .helper-text-custom {
    font-size: 0.82rem !important;
    line-height: 1.45 !important;
    color: #795548 !important;
    margin-top: 6px !important;
  }

  .setting-input-custom {
    border: 1.5px solid #dcd1be !important;
    background-color: #fcfaf7 !important;
    color: #3e2723 !important;
    font-size: 0.92rem !important;
    border-radius: 10px !important;
  }
  .setting-input-custom:focus {
    border-color: #8b5e3c !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 0.2rem rgba(139, 94, 60, 0.15) !important;
  }

  .card-setting-box {
    background: #ffffff;
    border: 1.5px solid #e8decb !important;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    transition: all 0.2s ease;
  }
  .card-setting-box:hover {
    border-color: #d4c4b0 !important;
  }

  /* Interactive Calendar Styling */
  .calendar-grid-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
    font-size: 0.85rem;
  }
  .calendar-grid-body {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
  }
  .cal-day-cell {
    aspect-ratio: 1.1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    border: 1.5px solid #eae2d6;
    background: #ffffff;
    font-weight: 600;
    font-size: 0.9rem;
    color: #4a3424;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    position: relative;
  }
  .cal-day-cell:hover {
    border-color: #d32f2f;
    background: #fff5f5;
    transform: scale(1.04);
  }
  .cal-day-cell.other-month {
    opacity: 0.3;
    background: #fbf9f6;
    cursor: default;
  }
  .cal-day-cell.is-holiday {
    background: linear-gradient(135deg, #e53935 0%, #c62828 100%) !important;
    color: #ffffff !important;
    border-color: #b71c1c !important;
    box-shadow: 0 3px 8px rgba(198, 40, 40, 0.35) !important;
  }
  .cal-day-badge {
    font-size: 0.65rem;
    font-weight: 700;
    line-height: 1;
    margin-top: 3px;
    background: rgba(255, 255, 255, 0.25);
    padding: 2px 6px;
    border-radius: 10px;
  }
  .holiday-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    background: #ffebee;
    color: #c62828;
    border: 1.5px solid #ffcdd2;
    font-size: 0.82rem;
    font-weight: 600;
  }
  .holiday-chip-remove {
    cursor: pointer;
    opacity: 0.75;
    transition: opacity 0.2s;
  }
  .holiday-chip-remove:hover {
    opacity: 1;
    color: #b71c1c;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-12">
    <!-- Header Banner -->
    <div class="position-relative overflow-hidden rounded-4 mb-4 p-4 text-white shadow-sm" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%);">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <div class="badge bg-white fw-bold px-3 py-1 mb-2 rounded-pill shadow-sm" style="color: #6e4727 !important; font-size: 0.75rem;">
            <i class="ti ti-settings me-1"></i> CENTRAL SYSTEM CONTROL
          </div>
          <h3 class="text-white fw-bold mb-1">Pengaturan Perpustakaan</h3>
          <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Kelola jadwal operasional gender, aturan peminjaman, kalkulasi denda, dan identitas perpustakaan.</p>
        </div>
      </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success rounded-3 p-3 mb-4 shadow-sm border-0 d-flex align-items-center gap-2">
        <i class="ti ti-circle-check fs-5"></i>
        <div class="fw-semibold"><?= session()->getFlashdata('success'); ?></div>
      </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/settings'); ?>" method="post">
      <?= csrf_field(); ?>

      <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff; border: 1.5px solid #e8decb !important;">
        <div class="card-header bg-transparent border-bottom p-3 p-md-4" style="border-color: #e8decb !important;">
          <!-- Navigation Tabs -->
          <ul class="nav nav-pills settings-nav gap-2" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-schedule-tab" data-bs-toggle="tab" data-bs-target="#tab-schedule" type="button" role="tab">
                <i class="ti ti-calendar-event me-1.5 fs-5"></i> Jadwal & Akses Gender
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-loans-tab" data-bs-toggle="tab" data-bs-target="#tab-loans" type="button" role="tab">
                <i class="ti ti-book me-1.5 fs-5"></i> Aturan Pinjam & Denda
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-identity-tab" data-bs-toggle="tab" data-bs-target="#tab-identity" type="button" role="tab">
                <i class="ti ti-building me-1.5 fs-5"></i> Identitas & Struk
              </button>
            </li>
          </ul>
        </div>

        <div class="card-body p-4">
          <div class="tab-content" id="settingsTabsContent">
            
            <!-- TAB 1: JADWAL & GENDER -->
            <div class="tab-pane fade show active" id="tab-schedule" role="tabpanel">
              <div class="mb-4">
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: #4a3424;">
                  <i class="ti ti-clock-check text-warning fs-5"></i> Pengaturan Hari Operasional Berdasarkan Gender
                </h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Pilih hari operasional perpustakaan untuk masing-masing gender. Hari yang tidak dipilih akan <strong>otomatis bebas denda (Rp 0)</strong> saat kalkulasi pengembalian.</p>
              </div>

              <div class="row g-4">
                <!-- Hari Buka Santriwati -->
                <div class="col-12 col-lg-6">
                  <div class="p-3.5 card-setting-box h-100 p-4">
                    <div class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #6e4727; font-size: 0.95rem;">
                      <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="background: #fce4ec; color: #c2185b; width: 32px; height: 32px;"><i class="ti ti-woman fs-5"></i></span>
                      Hari Buka Perempuan / Santriwati
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                      <?php 
                        $daysMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                        foreach ($daysMap as $dNum => $dName):
                          $isChecked = in_array($dNum, $femaleDays);
                      ?>
                        <div>
                          <input type="checkbox" class="day-checkbox-input day-checkbox-input-female" name="female_open_days[]" value="<?= $dNum; ?>" id="fem_day_<?= $dNum; ?>" <?= $isChecked ? 'checked' : ''; ?>>
                          <label class="day-checkbox-label" for="fem_day_<?= $dNum; ?>">
                            <i class="ti ti-check fs-6 <?= $isChecked ? '' : 'd-none'; ?>"></i> <?= $dName; ?>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>

                <!-- Hari Buka Santriwan -->
                <div class="col-12 col-lg-6">
                  <div class="p-3.5 card-setting-box h-100 p-4">
                    <div class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #6e4727; font-size: 0.95rem;">
                      <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="background: #e3f2fd; color: #1565c0; width: 32px; height: 32px;"><i class="ti ti-man fs-5"></i></span>
                      Hari Buka Laki-laki / Santriwan
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                      <?php 
                        foreach ($daysMap as $dNum => $dName):
                          $isChecked = in_array($dNum, $maleDays);
                      ?>
                        <div>
                          <input type="checkbox" class="day-checkbox-input day-checkbox-input-male" name="male_open_days[]" value="<?= $dNum; ?>" id="male_day_<?= $dNum; ?>" <?= $isChecked ? 'checked' : ''; ?>>
                          <label class="day-checkbox-label" for="male_day_<?= $dNum; ?>">
                            <i class="ti ti-check fs-6 <?= $isChecked ? '' : 'd-none'; ?>"></i> <?= $dName; ?>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>

                <!-- Hari Libur Umum -->
                <div class="col-12 col-lg-6">
                  <div class="p-3.5 card-setting-box h-100 p-4">
                    <div class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #6e4727; font-size: 0.95rem;">
                      <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="background: #ffebee; color: #c62828; width: 32px; height: 32px;"><i class="ti ti-bell-off fs-5"></i></span>
                      Hari Libur Rutin Umum (Tutup untuk Semua)
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                      <?php 
                        foreach ($daysMap as $dNum => $dName):
                          $isChecked = in_array($dNum, $generalClosedDays);
                      ?>
                        <div>
                          <input type="checkbox" class="day-checkbox-input day-checkbox-input-closed" name="general_closed_days[]" value="<?= $dNum; ?>" id="closed_day_<?= $dNum; ?>" <?= $isChecked ? 'checked' : ''; ?>>
                          <label class="day-checkbox-label" for="closed_day_<?= $dNum; ?>">
                            <i class="ti ti-x fs-6 <?= $isChecked ? '' : 'd-none'; ?>"></i> <?= $dName; ?>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>

                <!-- Penerapan Aturan Gender -->
                <div class="col-12 col-lg-6">
                  <div class="p-3.5 card-setting-box h-100 p-4">
                    <div class="fw-bold mb-2 d-flex align-items-center gap-2" style="color: #6e4727; font-size: 0.95rem;">
                      <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="background: #efebe9; color: #5d4037; width: 32px; height: 32px;"><i class="ti ti-users fs-5"></i></span>
                      Terapkan Aturan Akses Gender Khusus Untuk Tipe:
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                      <?php 
                        foreach ($availableMemberTypes as $tKey => $tLabel):
                          $isChecked = in_array($tKey, $applyGenderTypes);
                      ?>
                        <div>
                          <input type="checkbox" class="day-checkbox-input day-checkbox-input-type" name="apply_gender_types[]" value="<?= $tKey; ?>" id="type_<?= $tKey; ?>" <?= $isChecked ? 'checked' : ''; ?>>
                          <label class="day-checkbox-label" for="type_<?= $tKey; ?>">
                            <i class="ti ti-check fs-6 <?= $isChecked ? '' : 'd-none'; ?>"></i> <?= $tLabel; ?>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                    <div class="helper-text-custom">
                      *Centang tipe anggota yang wajib mengikuti jadwal gender. Tipe anggota yang <strong>tidak dicentang</strong> (misal: <strong>Petugas / Staf</strong>) bebas bertransaksi setiap hari perpustakaan buka.
                    </div>
                  </div>
                </div>

                <!-- Special Holidays Calendar Widget -->
                <div class="col-12">
                  <div class="p-3.5 card-setting-box p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                      <div class="fw-bold d-flex align-items-center gap-2" style="color: #6e4727; font-size: 0.95rem;">
                        <span class="badge rounded-circle p-2 d-inline-flex align-items-center justify-content-center" style="background: #ffebee; color: #c62828; width: 32px; height: 32px;"><i class="ti ti-calendar-event fs-5"></i></span>
                        Kalender Libur Khusus / Libur Pesantren
                      </div>
                    </div>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="special_holidays" id="special_holidays" value="<?= esc($settings['special_holidays'] ?? ''); ?>">

                    <!-- Interactive Calendar Card -->
                    <div class="p-3 rounded-4 border bg-white shadow-sm mb-3">
                      <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                        <button type="button" id="prevCalMonth" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">
                          <i class="ti ti-chevron-left"></i> Bulan Sebelumnya
                        </button>
                        <h6 class="fw-bold mb-0 text-dark fs-4" id="calMonthYearTitle">Agustus 2026</h6>
                        <button type="button" id="nextCalMonth" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold">
                          Bulan Berikutnya <i class="ti ti-chevron-right"></i>
                        </button>
                      </div>

                      <!-- Days Header -->
                      <div class="calendar-grid-header text-center fw-bold text-muted mb-2">
                        <div class="text-danger">Minggu</div>
                        <div>Senin</div>
                        <div>Selasa</div>
                        <div>Rabu</div>
                        <div>Kamis</div>
                        <div class="text-success">Jumat</div>
                        <div class="text-primary">Sabtu</div>
                      </div>

                      <!-- Calendar Grid -->
                      <div class="calendar-grid-body" id="calendarGridDays">
                        <!-- Rendered by JS -->
                      </div>
                    </div>

                    <div class="helper-text-custom mb-2">
                      <i class="ti ti-info-circle text-danger me-1"></i> Klik pada tanggal di kalender interaktif untuk <strong>menandai / membatalkan libur</strong>. Tanggal berlatar <strong>Merah (Libur)</strong> otomatis bebas denda Rp 0.
                    </div>

                    <!-- Selected Holiday Chips/Badges Container -->
                    <div class="mt-3">
                      <div class="fw-bold text-muted fs-7 mb-2">Daftar Tanggal Libur Khusus Terpilih:</div>
                      <div class="d-flex flex-wrap gap-2" id="selectedHolidaysContainer">
                        <!-- Populated by JS -->
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <!-- TAB 2: ATURAN PINJAM & DENDA -->
            <div class="tab-pane fade" id="tab-loans" role="tabpanel">
              <div class="mb-4">
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: #4a3424;">
                  <i class="ti ti-calculator text-warning fs-5"></i> Kebijakan Peminjaman & Rumus Denda
                </h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Atur durasi pinjam default, batas kuota buku, tarif denda per hari, serta denda kondisi fisik buku.</p>
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Maksimal Pinjam Buku (per Anggota)</label>
                    <div class="input-group">
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="max_books_per_member" value="<?= esc($settings['max_books_per_member'] ?? 2); ?>" min="1">
                      <span class="input-group-text bg-light text-muted fw-semibold" style="border-radius: 0 10px 10px 0;">Buku</span>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Durasi Pinjam Default</label>
                    <div class="input-group">
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="default_loan_duration" value="<?= esc($settings['default_loan_duration'] ?? 7); ?>" min="1">
                      <span class="input-group-text bg-light text-muted fw-semibold" style="border-radius: 0 10px 10px 0;">Hari</span>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Maksimal Perpanjangan Pinjam</label>
                    <div class="input-group">
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="max_loan_extensions" value="<?= esc($settings['max_loan_extensions'] ?? 1); ?>" min="0">
                      <span class="input-group-text bg-light text-muted fw-semibold" style="border-radius: 0 10px 10px 0;">Kali</span>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Tarif Denda Terlambat (per Hari)</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted fw-bold" style="border-radius: 10px 0 0 10px;">Rp</span>
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="fine_per_day" value="<?= esc($settings['fine_per_day'] ?? 1000); ?>" min="0">
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Batas Maksimal Denda (Fine Cap)</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted fw-bold" style="border-radius: 10px 0 0 10px;">Rp</span>
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="max_fine_amount" value="<?= esc($settings['max_fine_amount'] ?? 20000); ?>" min="0">
                    </div>
                    <div class="helper-text-custom">*Batas maksimal akumulasi denda per buku.</div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Masa Tenggang (Grace Period)</label>
                    <div class="input-group">
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="grace_period_days" value="<?= esc($settings['grace_period_days'] ?? 0); ?>" min="0">
                      <span class="input-group-text bg-light text-muted fw-semibold" style="border-radius: 0 10px 10px 0;">Hari Gratis</span>
                    </div>
                  </div>
                </div>

                <div class="col-12 col-md-4">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Denda Buku Rusak Ringan</label>
                    <div class="input-group">
                      <span class="input-group-text bg-light text-muted fw-bold" style="border-radius: 10px 0 0 10px;">Rp</span>
                      <input type="number" class="form-control setting-input-custom px-3 py-2 fw-bold" name="damaged_book_fine" value="<?= esc($settings['damaged_book_fine'] ?? 5000); ?>" min="0">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TAB 3: IDENTITAS & STRUK -->
            <div class="tab-pane fade" id="tab-identity" role="tabpanel">
              <div class="mb-4">
                <h5 class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: #4a3424;">
                  <i class="ti ti-id text-warning fs-5"></i> Profil Perpustakaan & Cetak Struk
                </h5>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">Lengkapi identitas perpustakaan yang akan tercetak pada struk peminjaman dan kartu anggota.</p>
              </div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Nama Resmi Perpustakaan</label>
                    <input type="text" class="form-control setting-input-custom px-3 py-2 fw-bold" name="library_name" value="<?= esc($settings['library_name'] ?? 'Perpustakaan Assalafiyyah'); ?>">
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Nomor Telepon / WA Pengurus</label>
                    <input type="text" class="form-control setting-input-custom px-3 py-2 fw-medium" name="library_contact" value="<?= esc($settings['library_contact'] ?? '08123456789'); ?>">
                  </div>
                </div>

                <div class="col-12">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Alamat Lengkap Perpustakaan</label>
                    <input type="text" class="form-control setting-input-custom px-3 py-2 fw-medium" name="library_address" value="<?= esc($settings['library_address'] ?? 'Jl. Pesantren Assalafiyyah'); ?>">
                  </div>
                </div>

                <div class="col-12">
                  <div class="p-3 card-setting-box">
                    <label class="form-label fw-bold mb-1" style="color: #6e4727; font-size: 0.85rem;">Catatan Footnote pada Struk Peminjaman & Kartu</label>
                    <textarea class="form-control setting-input-custom px-3 py-2 fw-medium" name="struk_footer_note" rows="3"><?= esc($settings['struk_footer_note'] ?? 'Terima kasih telah membaca. Jagalah buku dengan baik!'); ?></textarea>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="card-footer bg-transparent border-top p-3 p-md-4 d-flex justify-content-end" style="border-color: #e8decb !important;">
          <button type="submit" class="btn btn-primary px-4 py-2.5 fw-bold rounded-3 text-white shadow-sm" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none; font-size: 0.95rem;">
            <i class="ti ti-device-floppy me-1.5 fs-5"></i> Simpan Seluruh Pengaturan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function initSettingsCalendar() {
  const hiddenInput = document.getElementById('special_holidays');
  const container = document.getElementById('selectedHolidaysContainer');
  const calendarGrid = document.getElementById('calendarGridDays');
  const monthYearTitle = document.getElementById('calMonthYearTitle');
  const prevBtn = document.getElementById('prevCalMonth');
  const nextBtn = document.getElementById('nextCalMonth');
  const addInput = document.getElementById('addHolidayDateInput');
  const addBtn = document.getElementById('btnAddHolidayDate');

  if (!calendarGrid || !hiddenInput) return;

  let selectedDates = [];
  if (hiddenInput && hiddenInput.value.trim()) {
    selectedDates = hiddenInput.value.split(',').map(d => d.trim()).filter(d => d.length > 0);
  }

  let currentDate = new Date();
  let currentYear = currentDate.getFullYear();
  let currentMonth = currentDate.getMonth();

  const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];

  function syncHiddenInput() {
    selectedDates.sort();
    if (hiddenInput) {
      hiddenInput.value = selectedDates.join(',');
    }
    renderChips();
    renderCalendar();
  }

  function toggleDate(dateStr) {
    const idx = selectedDates.indexOf(dateStr);
    if (idx > -1) {
      selectedDates.splice(idx, 1);
    } else {
      selectedDates.push(dateStr);
    }
    syncHiddenInput();
  }

  function renderChips() {
    if (!container) return;
    container.innerHTML = '';
    if (selectedDates.length === 0) {
      container.innerHTML = '<span class="text-muted fs-7 fst-italic">Belum ada tanggal libur khusus yang dipilih. Klik tanggal pada kalender di atas.</span>';
      return;
    }

    selectedDates.forEach(dateStr => {
      const parts = dateStr.split('-');
      let labelStr = dateStr;
      if (parts.length === 3) {
        const y = parts[0];
        const m = parseInt(parts[1], 10) - 1;
        const d = parseInt(parts[2], 10);
        if (monthNames[m]) {
          labelStr = `${d} ${monthNames[m]} ${y}`;
        }
      }

      const chip = document.createElement('div');
      chip.className = 'holiday-chip';
      chip.innerHTML = `
        <i class="ti ti-calendar-event text-danger"></i> ${labelStr}
        <i class="ti ti-x holiday-chip-remove ms-1" data-date="${dateStr}"></i>
      `;

      $(chip).off('click', '.holiday-chip-remove').on('click', '.holiday-chip-remove', function(e) {
        e.stopPropagation();
        const dToRemove = this.getAttribute('data-date');
        selectedDates = selectedDates.filter(d => d !== dToRemove);
        syncHiddenInput();
      });

      container.appendChild(chip);
    });
  }

  function renderCalendar() {
    if (!calendarGrid || !monthYearTitle) return;

    monthYearTitle.textContent = `${monthNames[currentMonth]} ${currentYear}`;
    calendarGrid.innerHTML = '';

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const prevMonthDays = new Date(currentYear, currentMonth, 0).getDate();

    // Previous month padding days
    for (let i = firstDay - 1; i >= 0; i--) {
      const pDay = prevMonthDays - i;
      const cell = document.createElement('div');
      cell.className = 'cal-day-cell other-month';
      cell.textContent = pDay;
      calendarGrid.appendChild(cell);
    }

    // Current month days
    for (let d = 1; d <= daysInMonth; d++) {
      const mStr = String(currentMonth + 1).padStart(2, '0');
      const dStr = String(d).padStart(2, '0');
      const fullDate = `${currentYear}-${mStr}-${dStr}`;

      const isHoliday = selectedDates.includes(fullDate);

      const cell = document.createElement('div');
      cell.className = `cal-day-cell ${isHoliday ? 'is-holiday' : ''}`;
      cell.dataset.date = fullDate;
      cell.innerHTML = `
        <span>${d}</span>
        ${isHoliday ? '<span class="cal-day-badge">Libur</span>' : ''}
      `;

      calendarGrid.appendChild(cell);
    }

    // Next month padding days to fill grid
    const totalCells = firstDay + daysInMonth;
    const remainingCells = (totalCells % 7 === 0) ? 0 : 7 - (totalCells % 7);
    for (let i = 1; i <= remainingCells; i++) {
      const cell = document.createElement('div');
      cell.className = 'cal-day-cell other-month';
      cell.textContent = i;
      calendarGrid.appendChild(cell);
    }
  }

  $(document).off('click', '#prevCalMonth').on('click', '#prevCalMonth', function() {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    renderCalendar();
  });

  $(document).off('click', '#nextCalMonth').on('click', '#nextCalMonth', function() {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    renderCalendar();
  });

  $(document).off('click', '#btnAddHolidayDate').on('click', '#btnAddHolidayDate', function() {
    const addInput = document.getElementById('addHolidayDateInput');
    if (!addInput) return;
    const val = addInput.value.trim();
    if (val && !selectedDates.includes(val)) {
      selectedDates.push(val);
      syncHiddenInput();
      addInput.value = '';
    }
  });

  $(document).off('click', '.cal-day-cell:not(.other-month)').on('click', '.cal-day-cell:not(.other-month)', function() {
    const d = this.dataset.date;
    if (d) toggleDate(d);
  });

  // Initial render
  renderChips();
  renderCalendar();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSettingsCalendar);
} else {
  initSettingsCalendar();
}
</script>
<?= $this->endSection(); ?>
