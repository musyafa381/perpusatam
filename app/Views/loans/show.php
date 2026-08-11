<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Detail Peminjaman - <?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></title>
<style>
  #qr-code {
    background-image: url(<?= base_url(LOANS_QR_CODE_URI . $loan['loan_qr_code']); ?>);
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    width: 200px;
    height: 200px;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use CodeIgniter\I18n\Time;

$now = Time::now(locale: 'id');
$loanDate = Time::parse($loan['loan_date'], locale: 'id');
$dueDate = Time::parse($loan['due_date'], locale: 'id');

$isLate = $now->isAfter($dueDate);
$isDueDate = $now->today()->equals($dueDate);

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
          <i class="ti ti-arrows-exchange me-1"></i> Transaksi Peminjaman Aktif
        </div>
        <h3 class="text-white fw-bold mb-1">Detail Peminjaman Buku</h3>
        <p class="text-white-50 mb-0">Peminjam: <strong><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></strong> • Total <?= count($allSessionLoans); ?> Eksemplar Buku</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('admin/loans'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-arrow-left me-1"></i> Kembali
        </a>
        <a href="<?= base_url("admin/loans/receipt/{$loan['uid']}?print=true"); ?>" target="_blank" class="btn btn-outline-light fw-bold shadow-sm">
          <i class="ti ti-printer me-1"></i> Cetak / Print Struk
        </a>
        <a href="<?= base_url("admin/returns/new?loan-uid={$loan['uid']}"); ?>" class="btn btn-light text-success fw-bold shadow-sm">
          <i class="ti ti-check me-1"></i> Selesaikan Pengembalian
        </a>
        <form action="<?= base_url("admin/loans/{$loan['uid']}"); ?>" method="post" class="m-0">
          <?= csrf_field(); ?>
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="btn btn-outline-light fw-bold shadow-sm" data-confirm="Apakah Anda yakin ingin membatalkan SELURUH transaksi peminjaman ini (semua <?= count($allSessionLoans); ?> buku)? Buku-buku akan dikembalikan ke stok/rak.">
            <i class="ti ti-x me-1"></i> Batalkan Transaksi
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

<div class="row g-4 mb-4">
  <!-- Left Side: Data Peminjam Card -->
  <div class="col-12 col-lg-8">
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4"><i class="ti ti-user-check text-primary me-2"></i> Informasi Peminjam</h5>

        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3 border">
          <div class="member-avatar me-3" style="width: 54px; height: 54px; font-size: 1.3rem;">
            <?= strtoupper(substr($loan['first_name'] ?? 'A', 0, 1) . substr($loan['last_name'] ?? '', 0, 1)); ?>
          </div>
          <div>
            <h4 class="fw-bold text-dark mb-0"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></h4>
            <div class="mt-1">
              <?php if (($loan['member_type'] ?? 'siswa') === 'siswa') : ?>
                <span class="badge badge-subtle-primary fs-2"><i class="ti ti-school me-1"></i> Siswa / Santri</span>
              <?php else : ?>
                <span class="badge badge-subtle-success fs-2"><i class="ti ti-user-cog me-1"></i> Petugas / Staf</span>
              <?php endif; ?>
              <span class="badge badge-subtle-secondary fs-2 ms-1"><i class="ti ti-barcode me-1"></i>UID: <?= esc($loan['member_uid']); ?></span>
            </div>
          </div>
        </div>

        <div class="row g-3">
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">EMAIL ANGGOTA</small>
              <div class="fw-bold text-dark"><i class="ti ti-mail me-1 text-primary"></i><?= esc(!empty($loan['email']) && strpos($loan['email'], 'student_') === false ? $loan['email'] : '-'); ?></div>
            </div>
          </div>
          <div class="col-12 col-sm-6">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">NOMOR TELEPON</small>
              <div class="fw-bold text-dark"><i class="ti ti-phone me-1 text-primary"></i><?= esc(!empty($loan['phone']) && $loan['phone'] !== '-' ? $loan['phone'] : '-'); ?></div>
            </div>
          </div>
          <div class="col-12">
            <div class="stat-box">
              <small class="text-muted d-block fw-semibold mb-1">ALAMAT LENGKAP</small>
              <div class="fw-bold text-dark"><i class="ti ti-map-pin me-1 text-primary"></i><?= esc(!empty($loan['address']) ? $loan['address'] : '-'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Peminjaman Metrics Grid -->
    <div class="row g-3">
      <div class="col-12 col-sm-4">
        <div class="card info-card border-0 text-center h-100">
          <div class="card-body p-3">
            <div class="member-avatar mx-auto mb-2" style="width: 42px; height: 42px; font-size: 1.1rem; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
              <i class="ti ti-books"></i>
            </div>
            <div class="fw-bold text-dark fs-4 mb-0"><?= count($allSessionLoans); ?> Buku</div>
            <small class="text-muted fw-semibold fs-1">Total Eksemplar</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-4">
        <div class="card info-card border-0 text-center h-100">
          <div class="card-body p-3">
            <div class="member-avatar mx-auto mb-2" style="width: 42px; height: 42px; font-size: 1.1rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
              <i class="ti ti-calendar-check"></i>
            </div>
            <div class="fw-bold text-dark fs-3 mb-0"><?= $loanDate->toLocalizedString('d MMMM y'); ?></div>
            <small class="text-muted fw-semibold fs-1">Waktu Pinjam (<?= $loanDate->toLocalizedString('HH:mm'); ?>)</small>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-4">
        <div class="card info-card border-0 text-center h-100">
          <div class="card-body p-3">
            <div class="member-avatar mx-auto mb-2" style="width: 42px; height: 42px; font-size: 1.1rem; background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%);">
              <i class="ti ti-calendar-due"></i>
            </div>
            <div class="fw-bold text-dark fs-3 mb-0"><?= $dueDate->toLocalizedString('d MMMM y'); ?></div>
            <small class="text-muted fw-semibold fs-1">Tenggat Pengembalian</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Side: Status & QR Code -->
  <div class="col-12 col-lg-4">
    <div class="card info-card border-0 text-center mb-3">
      <div class="card-body p-4">
        <small class="text-muted d-block fw-semibold mb-2">STATUS PEMINJAMAN</small>
        <?php if ($now->isBefore($dueDate) && !$isDueDate) : ?>
          <span class="badge bg-success rounded-pill px-4 py-2 fs-3 shadow-sm"><i class="ti ti-circle-check me-1"></i> Normal (Aktif)</span>
        <?php elseif ($isDueDate) : ?>
          <span class="badge bg-warning text-dark rounded-pill px-4 py-2 fs-3 shadow-sm"><i class="ti ti-clock-exclamation me-1"></i> Jatuh Tempo Hari Ini</span>
        <?php else : ?>
          <span class="badge bg-danger rounded-pill px-4 py-2 fs-3 shadow-sm"><i class="ti ti-alert-triangle me-1"></i> Terlambat Kembalikan</span>
        <?php endif; ?>
      </div>
    </div>

    <div class="card info-card border-0 text-center">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-barcode text-primary me-2"></i> Kode Barcode Transaksi</h5>
        <div class="p-3 bg-white rounded-3 border shadow-sm mb-3">
          <div class="d-flex justify-content-center align-items-center mb-2 overflow-hidden" style="max-height: 65px;">
            <?= generateBarcodeSVG($loan['uid'], 60); ?>
          </div>
          <strong class="font-monospace text-dark fs-4 d-block tracking-wider mt-1"><?= esc($loan['uid']); ?></strong>
        </div>
        <div class="mt-2">
          <a href="<?= base_url("admin/loans/receipt/{$loan['uid']}?print=true"); ?>" target="_blank" class="btn btn-pill-gold fw-bold shadow-sm w-100">
            <i class="ti ti-printer me-1"></i> Cetak / Print Struk Peminjaman
          </a>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Books Table Card -->
<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
      <div>
        <h5 class="fw-bold text-dark mb-1">
          <i class="ti ti-books text-primary me-2"></i> Daftar Buku & Eksemplar Dipinjam (<?= count($allSessionLoans); ?> Buku)
        </h5>
        <div class="d-flex align-items-center gap-1 mt-1">
          <span class="badge <?= $tierDetails['badge'] ?? 'bg-primary text-white'; ?> fs-2"><i class="ti <?= $tierDetails['icon'] ?? 'ti-id'; ?> me-1"></i><?= esc($tierDetails['name'] ?? 'Member'); ?> (Maks <?= $tierDetails['max_loans'] ?? 1; ?> Buku)</span>
          <span class="badge <?= ($activeLoansCount >= ($tierDetails['max_loans'] ?? 1)) ? 'bg-danger text-white' : 'badge-subtle-success'; ?> fs-2"><i class="ti ti-book me-1"></i>Aktif Dipinjam: <?= $activeLoansCount; ?> / <?= $tierDetails['max_loans'] ?? 1; ?> Buku</span>
        </div>
      </div>
      <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-pill-gold btn-sm fw-bold shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addItemModal">
          <i class="ti ti-plus"></i> Tambah Buku Dipinjam
        </button>
      </div>
    </div>

    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom mb-0">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Sampul</th>
            <th scope="col">Judul Buku & Kategori</th>
            <th scope="col" class="text-center">Kode Eksemplar</th>
            <th scope="col">Pengarang / Penerbit</th>
            <th scope="col" class="text-center">Status Unit</th>
            <th scope="col">Lokasi Rak</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $bIdx = 1; ?>
          <?php foreach ($allSessionLoans as $sLoan) : ?>
            <?php
            $coverImageUrl = getBookCover($sLoan['book_cover'] ?? '');
            $isSingleReturned = !empty($sLoan['return_date']);
            ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $bIdx++; ?></th>
              <td>
                <img src="<?= $coverImageUrl; ?>" alt="<?= esc($sLoan['book_title'] ?? $sLoan['title'] ?? 'Buku'); ?>" class="rounded-2 shadow-sm border" style="width: 50px; height: 70px; object-fit: cover;">
              </td>
              <td>
                <a href="<?= base_url("admin/books/" . ($sLoan['book_slug'] ?? $sLoan['slug'] ?? '')); ?>" class="fw-bold text-dark fs-3 text-decoration-none d-block">
                  <?= esc($sLoan['book_title'] ?? $sLoan['title']); ?> (<?= esc($sLoan['book_year'] ?? $sLoan['year']); ?>)
                </a>
                <?php if (!empty($sLoan['category_name']) || !empty($sLoan['category'])) : ?>
                  <span class="badge badge-subtle-primary fs-2 mt-1"><i class="ti ti-category me-1"></i><?= esc($sLoan['category_name'] ?? $sLoan['category']); ?></span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <span class="badge badge-subtle-primary fs-3 px-3 py-2">
                  <i class="ti ti-barcode me-1"></i><?= esc($sLoan['item_code'] ?? '-'); ?>
                </span>
              </td>
              <td>
                <div class="fw-semibold text-dark"><i class="ti ti-user me-1"></i><?= esc($sLoan['book_author'] ?? $sLoan['author']); ?></div>
                <small class="text-muted"><i class="ti ti-building me-1"></i><?= esc($sLoan['book_publisher'] ?? $sLoan['publisher']); ?></small>
              </td>
              <td class="text-center">
                <?php if ($isSingleReturned) : ?>
                  <span class="badge bg-success text-white px-3 py-2 rounded-pill fs-2 shadow-sm">
                    <i class="ti ti-circle-check me-1"></i>Dikembalikan (<?= date('d/m/Y', strtotime($sLoan['return_date'])); ?>)
                  </span>
                <?php else : ?>
                  <span class="badge bg-primary text-white px-3 py-2 rounded-pill fs-2 shadow-sm">
                    <i class="ti ti-book me-1"></i>Masih Dipinjam
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-light text-dark border fs-2">
                  <i class="ti ti-box me-1"></i>Rak <?= esc($sLoan['rack_name'] ?? $sLoan['rack'] ?? '-'); ?> (Lantai <?= esc($sLoan['rack_floor'] ?? $sLoan['floor'] ?? '-'); ?>)
                </span>
              </td>
              <td class="text-center pe-3">
                <?php if ($isSingleReturned) : ?>
                  <span class="badge bg-light text-success border fs-2 px-3 py-2 rounded-pill fw-semibold">
                    <i class="ti ti-check me-1"></i>Selesai
                  </span>
                <?php else : ?>
                  <div class="d-flex justify-content-center align-items-center gap-1">
                    <button type="button" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#returnSingleModal<?= $sLoan['id']; ?>">
                      <i class="ti ti-rotate-clockwise-2"></i> Kembalikan
                    </button>
                    <form action="<?= base_url("admin/loans/{$loan['uid']}/remove-item/{$sLoan['id']}"); ?>" method="post" class="d-inline m-0">
                      <?= csrf_field(); ?>
                      <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2.5" data-confirm="Apakah Anda yakin ingin membatalkan/menghapus buku '<?= esc($sLoan['book_title'] ?? $sLoan['title']); ?>' dari transaksi peminjaman ini?" title="Batalkan / Hapus Item Buku Ini">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  </div>

                  <!-- Modal Form Pengembalian Buku Satuan -->
                  <div class="modal fade" id="returnSingleModal<?= $sLoan['id']; ?>" tabindex="-1" aria-labelledby="returnSingleModalLabel<?= $sLoan['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content rounded-4 border-0 shadow">
                        <form action="<?= base_url('admin/returns'); ?>" method="post">
                          <?= csrf_field(); ?>
                          <input type="hidden" name="loan_uid" value="<?= esc($loan['uid']); ?>">
                          <input type="hidden" name="target_loan_id" value="<?= esc($sLoan['id']); ?>">
                          <input type="hidden" name="date" value="<?= date('Y-m-d H:i:s'); ?>">

                          <div class="modal-header bg-warning text-dark rounded-top-4">
                            <h5 class="modal-title fw-bold" id="returnSingleModalLabel<?= $sLoan['id']; ?>">
                              <i class="ti ti-rotate-clockwise-2 me-1"></i> Pengembalian Buku Satuan
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>

                          <div class="modal-body text-start p-4">
                            <div class="p-3 bg-light rounded-3 border mb-3">
                              <h6 class="fw-bold text-dark mb-1"><?= esc($sLoan['book_title'] ?? $sLoan['title']); ?></h6>
                              <div class="text-muted fs-2 mb-1"><i class="ti ti-barcode me-1"></i>Kode Eksemplar: <code><?= esc($sLoan['item_code'] ?: '-'); ?></code></div>
                              <small class="text-muted d-block"><i class="ti ti-user me-1"></i>Peminjam: <strong><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></strong></small>
                            </div>

                            <div class="mb-3">
                              <label class="form-label fw-bold text-dark fs-2">Pilih Kondisi Fisik Buku Saat Dikembalikan:</label>
                              <select name="conditions[<?= $sLoan['id']; ?>]" class="form-select form-select-md fw-bold rounded-3 mb-2">
                                <option value="baik" selected>🟢 Baik (Siap Kembali ke Rak)</option>
                                <option value="rusak">🟡 Rusak (Denda 50% Harga Buku)</option>
                                <option value="hilang">🔴 Hilang (Denda 100% Harga Buku)</option>
                              </select>
                              <input type="text" name="condition_notes[<?= $sLoan['id']; ?>]" class="form-control form-control-sm fs-2 rounded-3" placeholder="Catatan detail kerusakan (misal: Cover sobek 2cm)...">
                            </div>

                            <?php
                            helper('library');
                            $fineCalc = calculate_loan_fine($sLoan);
                            if ($fineCalc['late_days'] > 0) :
                            ?>
                              <div class="alert alert-warning mb-2 rounded-3 fs-2 border-warning text-dark">
                                <i class="ti ti-clock-alert me-1 text-warning"></i> Terlambat <strong><?= $fineCalc['late_days']; ?> hari kalender</strong> (Hari Kena Denda: <strong><?= $fineCalc['charge_days']; ?> hari</strong> setelah potong libur gender/perpus).<br>
                                <span class="fw-bold text-danger">Estimasi Denda: Rp <?= number_format($fineCalc['fine_amount'], 0, ',', '.'); ?></span>
                              </div>

                              <div class="form-check form-switch mb-0 p-2 bg-light rounded-3 border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="dispensation[<?= $sLoan['id']; ?>]" value="1" id="dispense_<?= $sLoan['id']; ?>">
                                <label class="form-check-label fw-bold text-dark fs-2" for="dispense_<?= $sLoan['id']; ?>">
                                  ⚡ Berikan Dispensasi / Pemutihan Denda (Bebas Denda Rp 0)
                                </label>
                              </div>
                            <?php endif; ?>
                          </div>

                          <div class="modal-footer bg-light rounded-bottom-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 fw-bold btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold btn-sm shadow-sm">
                              <i class="ti ti-check me-1"></i> Proses Pengembalian
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Form Tambah Buku Dipinjam -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <form action="<?= base_url("admin/loans/{$loan['uid']}/add-item"); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-header card-gradient-header rounded-top-4 p-3 text-white">
          <h5 class="modal-title fw-bold text-white mb-0" id="addItemModalLabel">
            <i class="ti ti-plus me-2"></i> Tambah Buku ke Transaksi Peminjaman Ini
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" style="background-color: #fcf8f2;">
          <div class="p-3 bg-white rounded-3 border mb-3 shadow-sm">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
              <div>
                <h6 class="fw-bold text-dark mb-1"><i class="ti ti-user me-1"></i>Peminjam: <?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></h6>
                <span class="badge <?= $tierDetails['badge'] ?? 'bg-primary'; ?> fs-2"><i class="ti <?= $tierDetails['icon'] ?? 'ti-id'; ?> me-1"></i><?= esc($tierDetails['name'] ?? 'Member'); ?></span>
              </div>
              <div class="text-end">
                <div class="fs-2 text-muted fw-bold">Status Kuota Peminjaman:</div>
                <span class="badge <?= ($activeLoansCount >= ($tierDetails['max_loans'] ?? 1)) ? 'bg-danger' : 'bg-success'; ?> px-3 py-1 fs-3">
                  <?= $activeLoansCount; ?> dari <?= $tierDetails['max_loans'] ?? 1; ?> Buku Dipinjam
                </span>
              </div>
            </div>
          </div>

          <?php if ($activeLoansCount >= ($tierDetails['max_loans'] ?? 1)) : ?>
            <div class="alert alert-danger rounded-3 p-3 mb-0">
              <i class="ti ti-alert-triangle fs-5 me-2"></i>
              <strong>Batas Maksimal Kuota Tercapai!</strong> Member ini (<?= esc($tierDetails['name'] ?? 'Member'); ?>) sudah mencapai batas maksimal <?= $tierDetails['max_loans'] ?? 1; ?> buku dipinjam. Selesaikan/kembalikan buku yang dipinjam terlebih dahulu sebelum menambah buku baru.
            </div>
          <?php else : ?>
            <div class="mb-3">
              <label class="form-label fw-bold text-dark fs-2">Pilih Buku & Kode Eksemplar yang Tersedia di Rak:</label>
              <select name="book_item_id" id="bookItemSelect" class="form-select form-select-lg border-primary shadow-sm rounded-3 select2" required>
                <option value="" disabled selected>-- Cari / Pilih Buku & Kode Eksemplar --</option>
                <?php if (!empty($availableItems)) : ?>
                  <?php foreach ($availableItems as $item) : ?>
                    <option value="<?= $item['id']; ?>">
                      <?= esc($item['book_title']); ?> (Kode: <?= esc($item['item_code']); ?>) - Rak <?= esc($item['rack_name'] ?? '-'); ?>
                    </option>
                  <?php endforeach; ?>
                <?php else : ?>
                  <option value="" disabled>-- Tidak ada stok eksemplar buku yang tersedia --</option>
                <?php endif; ?>
              </select>
              <small class="text-muted d-block mt-2"><i class="ti ti-info-circle me-1"></i>Ketik judul atau kode eksemplar untuk mencari buku. Hanya buku berstatus 'Tersedia' yang ditampilkan.</small>
            </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer bg-light rounded-bottom-4">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Batal</button>
          <?php if ($activeLoansCount < ($tierDetails['max_loans'] ?? 1)) : ?>
            <button type="submit" class="btn btn-pill-gold px-4 fw-bold shadow-sm">
              <i class="ti ti-check me-1"></i> Tambahkan Buku Sekarang
            </button>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection() ?>