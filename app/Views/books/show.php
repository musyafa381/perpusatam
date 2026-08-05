<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Detail Buku - <?= $book['title']; ?></title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$coverImageUrl = getBookCoverUrl($book['book_cover']);
?>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Top Action & Navigation Bar -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <a href="<?= base_url('admin/books'); ?>" class="btn btn-outline-primary shadow-sm">
    <i class="ti ti-arrow-left me-1"></i> Kembali ke Katalog Buku
  </a>
  <div class="d-flex gap-2 flex-wrap">
    <button type="button" class="btn btn-warning fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingModal">
      <i class="ti ti-bookmark me-1"></i> Booking Buku Ini (Gold/Platinum)
    </button>
    <a href="<?= base_url("admin/books/{$book['slug']}/edit"); ?>" class="btn btn-primary shadow-sm">
      <i class="ti ti-edit me-1"></i> Edit Buku
    </a>
    <form action="<?= base_url("admin/books/{$book['slug']}"); ?>" method="post" class="d-inline">
      <?= csrf_field(); ?>
      <input type="hidden" name="_method" value="DELETE">
      <button type="submit" class="btn btn-outline-danger shadow-sm" data-confirm="Apakah Anda yakin ingin menghapus buku ini?">
        <i class="ti ti-trash me-1"></i> Hapus
      </button>
    </form>
  </div>
</div>

<!-- Banner Penanda Jika Buku Sedang Dibooking -->
<?php if (!empty($activeReservations)) : ?>
  <div class="alert alert-warning border border-2 border-warning shadow-sm p-3 mb-4 rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
    <div class="d-flex align-items-center">
      <i class="ti ti-bookmark fs-8 text-warning me-3"></i>
      <div>
        <h6 class="fw-bold text-dark mb-1">🔖 BUKU SEDANG DIBOOKING (<?= count($activeReservations); ?> Antrean Member)</h6>
        <p class="mb-0 fs-3">Buku ini telah dibooking oleh anggota berstatus <strong>Gold / Platinum Member</strong>. Silakan periksa antrean di bawah.</p>
      </div>
    </div>
    <button class="btn btn-warning btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingModal">
      <i class="ti ti-plus me-1"></i> Tambah Booking Baru
    </button>
  </div>
<?php endif; ?>

<!-- Section 1: Main Hero Book Overview Card -->
<div class="card border border-2 border-primary-subtle shadow-sm rounded-4 overflow-hidden mb-4">
  <div class="card-body p-4">
    <div class="row g-4 align-items-center">
      <!-- Sampul Buku -->
      <div class="col-12 col-md-4 col-lg-3 text-center">
        <div class="p-2 bg-light rounded-3 border shadow-sm d-inline-block" style="width: fit-content; max-width: 100%;">
          <img src="<?= $coverImageUrl; ?>" alt="<?= esc($book['title']); ?>" class="img-fluid rounded shadow d-block" style="max-height: 340px; width: auto; max-width: 100%; object-fit: contain;">
        </div>
      </div>
      
      <!-- Metadata Detail Buku -->
      <div class="col-12 col-md-8 col-lg-9">
        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
          <h2 class="fw-bold text-dark mb-0"><?= $book['title']; ?></h2>
          <?php if (!empty($book['category'])) : ?>
            <span class="badge badge-subtle-primary px-3 py-2 fs-3">
              <i class="ti ti-category me-1"></i><?= $book['category']; ?>
            </span>
          <?php endif; ?>
          <?php if (stripos($book['category'] ?? '', 'novel') !== false) : ?>
            <span class="badge badge-subtle-warning px-3 py-2 fs-3">
              <i class="ti ti-lock me-1"></i> Kategori Novel (Silver+)
            </span>
          <?php endif; ?>
        </div>

        <p class="text-muted fs-3 mb-3"><i class="ti ti-calendar me-1"></i>Tahun Terbit: <strong><?= $book['year']; ?></strong></p>

        <!-- Metadata & Barcode Dual Column Card -->
        <div class="row g-3">
          <!-- Left: 4-Grid Book Metadata -->
          <div class="col-12 col-lg-7">
            <div class="p-3 bg-light rounded-3 border border-light-subtle h-100">
              <div class="row g-3">
                <div class="col-6">
                  <small class="text-muted d-block mb-1"><i class="ti ti-user text-primary me-1"></i>Pengarang</small>
                  <h6 class="fw-bold text-dark mb-0"><?= esc($book['author']); ?></h6>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block mb-1"><i class="ti ti-building text-primary me-1"></i>Penerbit</small>
                  <h6 class="fw-bold text-dark mb-0"><?= esc($book['publisher']); ?></h6>
                </div>
                <div class="col-6 mt-3">
                  <small class="text-muted d-block mb-1"><i class="ti ti-building-arch text-primary me-1"></i>Lokasi Default Rak</small>
                  <h6 class="fw-bold text-dark mb-0">Rak <?= esc($book['rack']); ?> (Lantai <?= esc($book['floor']); ?>)</h6>
                </div>
                <div class="col-6 mt-3">
                  <small class="text-muted d-block mb-1"><i class="ti ti-barcode text-primary me-1"></i>Nomor ISBN</small>
                  <h6 class="fw-bold text-dark font-monospace mb-0"><?= esc($book['isbn'] ?? '-'); ?></h6>
                </div>
              </div>
            </div>
          </div>

          <!-- Right: Dedicated Barcode Card -->
          <?php if (!empty($book['isbn'])) : ?>
            <div class="col-12 col-lg-5">
              <div class="p-3 bg-white rounded-3 border border-primary-subtle shadow-sm h-100 text-center d-flex flex-column justify-content-between align-items-center">
                <div class="w-100">
                  <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                    <span class="small fw-semibold text-muted"><i class="ti ti-scan me-1"></i>Label Barcode ISBN</span>
                    <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 10px;">Scannable</span>
                  </div>
                  <div class="py-1">
                    <svg id="isbnBarcodeSvg" class="mw-100" style="max-height: 80px;"></svg>
                  </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm fw-bold w-100 mt-2" onclick="printIsbnBarcode()">
                  <i class="ti ti-printer me-1"></i>Cetak Barcode Label
                </button>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Section 2: 3 Stat Cards Widget -->
<div class="row g-3 mb-4">
  <div class="col-12 col-md-4">
    <div class="card border border-2 border-primary-subtle shadow-sm rounded-3 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="p-3 badge-subtle-primary rounded-3">
          <i class="ti ti-books fs-8"></i>
        </div>
        <div>
          <small class="text-muted fw-semibold">Total Stok Salinan</small>
          <h3 class="fw-bold text-dark mb-0"><?= $book['quantity']; ?> <span class="fs-3 fw-normal text-muted">Buku</span></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card border border-2 border-warning-subtle shadow-sm rounded-3 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="p-3 badge-subtle-warning rounded-3">
          <i class="ti ti-arrows-exchange fs-8"></i>
        </div>
        <div>
          <small class="text-muted fw-semibold">Sedang Dipinjam</small>
          <h3 class="fw-bold text-dark mb-0"><?= $loanCount; ?> <span class="fs-3 fw-normal text-muted">Salinan</span></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4">
    <div class="card border border-2 border-primary-subtle shadow-sm rounded-3 h-100">
      <div class="card-body p-3 d-flex align-items-center gap-3">
        <div class="p-3 badge-subtle-primary rounded-3">
          <i class="ti ti-box-seam fs-8"></i>
        </div>
        <div>
          <small class="text-muted fw-semibold">Tersedia Fisik di Rak</small>
          <h3 class="fw-bold text-dark mb-0"><?= $bookStock; ?> <span class="fs-3 fw-normal text-muted">Salinan</span></h3>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Section 3: Panel Deteksi Keberadaan Buku Saat Ini ("Buku Sedang Dimana?") -->
<div class="card border border-2 border-primary-subtle shadow-sm rounded-3 mb-4">
  <div class="card-body p-4">
    <h5 class="fw-bold text-dark mb-3">
      <i class="ti ti-map-pin text-primary me-1"></i> Deteksi Keberadaan Buku Saat Ini ("Buku Sedang Dimana?")
    </h5>
    
    <div class="row g-3">
      <!-- Status Dipinjam -->
      <div class="col-12 col-md-6">
        <div class="p-3 bg-light rounded-3 border border-light-subtle h-100">
          <h6 class="fw-bold text-dark mb-2"><i class="ti ti-user-check text-warning me-1"></i> Salinan Dipinjam (<?= count($activeLoansDetail); ?> Peminjaman)</h6>
          <?php if (empty($activeLoansDetail)) : ?>
            <p class="text-muted fs-2 mb-0">Tidak ada salinan yang sedang dipinjam saat ini.</p>
          <?php else : ?>
            <div class="d-flex flex-column gap-2">
              <?php foreach ($activeLoansDetail as $ld) : ?>
                <div class="p-2 bg-white rounded border">
                  <div class="fw-bold text-dark fs-3"><?= $ld['first_name'] . ' ' . $ld['last_name']; ?> <small class="text-muted">(<?= $ld['member_uid']; ?>)</small></div>
                  <div class="d-flex justify-content-between align-items-center mt-1">
                    <span class="badge bg-dark text-white font-monospace fs-1"><i class="ti ti-barcode me-1"></i>Kode: <?= $ld['item_code'] ?? 'Utama'; ?></span>
                    <span class="badge badge-subtle-primary fs-1 fw-semibold"><i class="ti ti-calendar me-1"></i>Est. Kembali: <?= \CodeIgniter\I18n\Time::parse($ld['created_at'])->addDays((int)($ld['duration'] ?? 7))->toLocalizedString('d MMM Y'); ?></span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Status Tersedia di Rak -->
      <div class="col-12 col-md-6">
        <div class="p-3 bg-light rounded-3 border border-light-subtle h-100">
          <h6 class="fw-bold text-dark mb-2"><i class="ti ti-building-store text-primary me-1"></i> Lokasi Stok Tersedia di Perpustakaan</h6>
          <?php if ($bookStock <= 0) : ?>
            <div class="p-3 badge-subtle-danger rounded border border-danger-subtle">
              <i class="ti ti-alert-triangle me-1"></i> Stok fisik di perpustakaan habis (Semua eksemplar sedang dipinjam).
            </div>
          <?php else : ?>
            <div class="p-3 badge-subtle-primary rounded border border-primary-subtle">
              <h6 class="fw-bold mb-1 text-primary"><i class="ti ti-check me-1"></i> <?= $bookStock; ?> Eksemplar Tersedia di Rak</h6>
              <div class="fs-2 text-dark">Posisi: <strong>Rak <?= $book['rack']; ?> - Lantai <?= $book['floor']; ?></strong></div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Section 4: Panel Daftar Antrean Booking Buku -->
<?php if (!empty($activeReservations)) : ?>
  <div class="card border border-2 border-warning-subtle shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-body p-4">
      <h5 class="fw-bold text-dark mb-3"><i class="ti ti-bookmark text-warning me-1"></i> Daftar Antrean Booking (Gold & Platinum Members)</h5>
      <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
        <table class="table table-hover align-middle table-assalafiyyah mb-0">
          <thead>
            <tr>
              <th scope="col" class="text-center" style="width: 50px;">#</th>
              <th scope="col">Nama Member Pembooking</th>
              <th scope="col">Tingkatan Member</th>
              <th scope="col">Waktu Booking</th>
              <th scope="col" class="text-center">Status Antrean</th>
              <th scope="col" class="text-center pe-4">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php $resIndex = 1; ?>
            <?php foreach ($activeReservations as $res) : ?>
              <tr>
                <th scope="row" class="col-index"><?= $resIndex++; ?></th>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="table-avatar-initial">
                      <?= strtoupper(substr($res['first_name'] ?? 'A', 0, 1) . substr($res['last_name'] ?? '', 0, 1)); ?>
                    </div>
                    <div>
                      <div class="fw-bold text-dark fs-3 mb-0"><?= esc($res['first_name'] . ' ' . $res['last_name']); ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge <?= $res['tier']['badge']; ?> px-3 py-2 fs-2"><i class="ti <?= $res['tier']['icon']; ?> me-1"></i><?= esc($res['tier']['name']); ?></span>
                </td>
                <td>
                  <div class="fw-bold text-dark"><?= \CodeIgniter\I18n\Time::parse($res['created_at'], locale: 'id')->toLocalizedString('dd/MM/y'); ?></div>
                  <small class="text-muted"><i class="ti ti-clock me-1"></i><?= \CodeIgniter\I18n\Time::parse($res['created_at'], locale: 'id')->toLocalizedString('HH:mm'); ?></small>
                </td>
                <td class="text-center">
                  <span class="badge badge-subtle-warning px-3 py-2 rounded-pill"><i class="ti ti-clock me-1"></i>Menunggu Buku Kembalian</span>
                </td>
                <td class="text-center pe-4">
                  <div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#resDetailModal-<?= $res['id']; ?>" title="Detail Booking">
                      <i class="ti ti-eye"></i>
                    </button>
                    <form action="<?= base_url("admin/reservations/{$res['id']}/fulfill"); ?>" method="post" class="m-0">
                      <?= csrf_field(); ?>
                      <button type="submit" class="btn btn-pill-gold btn-sm d-inline-flex align-items-center gap-1 px-3" title="Tandai Selesai / Serahkan Buku">
                        <i class="ti ti-check"></i> Selesai
                      </button>
                    </form>
                    <form action="<?= base_url("admin/reservations/{$res['id']}/delete"); ?>" method="post" class="m-0">
                      <?= csrf_field(); ?>
                      <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2.5" data-confirm="Hapus booking ini secara permanen?" title="Hapus Booking">
                        <i class="ti ti-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>

              <!-- Modal Detail Booking -->
              <div class="modal fade" id="resDetailModal-<?= $res['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title fw-bold text-dark"><i class="ti ti-bookmark text-warning me-1"></i> Detail Antrean Booking</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                      <!-- Exact Book Hero Card Layout -->
                      <div class="card border border-2 border-primary-subtle shadow-sm rounded-3 p-3 mb-4">
                        <div class="row g-3 align-items-center">
                          <div class="col-12 col-md-4 text-center">
                            <img src="<?= getBookCover($book['book_cover']); ?>" alt="<?= esc($book['title']); ?>" class="img-fluid rounded-3 border shadow-sm" style="max-height: 180px; object-fit: cover;">
                          </div>
                          <div class="col-12 col-md-8">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                              <h4 class="fw-bold text-dark mb-0"><?= esc($book['title']); ?></h4>
                              <?php if (!empty($book['category'])) : ?>
                                <span class="badge badge-subtle-primary px-2.5 py-1 fs-2">
                                  <i class="ti ti-category me-1"></i><?= esc($book['category']); ?>
                                </span>
                              <?php endif; ?>
                              <?php if (stripos($book['category'] ?? '', 'novel') !== false) : ?>
                                <span class="badge badge-subtle-warning px-2.5 py-1 fs-2">
                                  <i class="ti ti-lock me-1"></i> Kategori Novel (Silver+)
                                </span>
                              <?php endif; ?>
                            </div>

                            <div class="text-muted fs-2 mb-2"><i class="ti ti-calendar me-1"></i>Tahun Terbit: <strong><?= esc($book['year']); ?></strong></div>

                            <div class="row g-2 p-2 bg-light rounded-3 border border-light-subtle fs-2">
                              <div class="col-6">
                                <small class="text-muted d-block"><i class="ti ti-user text-primary me-1"></i>Pengarang</small>
                                <strong class="text-dark"><?= esc($book['author']); ?></strong>
                              </div>
                              <div class="col-6">
                                <small class="text-muted d-block"><i class="ti ti-building text-primary me-1"></i>Penerbit</small>
                                <strong class="text-dark"><?= esc($book['publisher']); ?></strong>
                              </div>
                              <div class="col-6 mt-2">
                                <small class="text-muted d-block"><i class="ti ti-barcode text-primary me-1"></i>ISBN</small>
                                <strong class="text-dark font-monospace"><?= esc($book['isbn'] ?: '-'); ?></strong>
                              </div>
                              <div class="col-6 mt-2">
                                <small class="text-muted d-block"><i class="ti ti-building-arch text-primary me-1"></i>Lokasi Default Rak</small>
                                <strong class="text-dark">Rak <?= esc($book['rack']); ?> (Lantai <?= esc($book['floor']); ?>)</strong>
                              </div>
                              <div class="col-12 mt-2 pt-2 border-top border-light-subtle">
                                <small class="text-muted d-block mb-1"><i class="ti ti-map-pin text-primary me-1"></i>Posisi / Ketersediaan Buku saat Ini</small>
                                <?php if ($bookStock > 0) : ?>
                                  <span class="badge badge-subtle-primary px-3 py-1.5 fs-2"><i class="ti ti-building-store me-1"></i>Tersedia di Perpustakaan (Rak <?= esc($book['rack']); ?> - Lantai <?= esc($book['floor']); ?>)</span>
                                <?php else : ?>
                                  <span class="badge badge-subtle-warning px-3 py-1.5 fs-2"><i class="ti ti-clock me-1"></i>Sedang Dipinjam</span>
                                <?php endif; ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row g-3">
                        <div class="col-12 col-md-6">
                          <div class="p-3 bg-white rounded-3 border h-100">
                            <small class="text-muted d-block mb-1"><i class="ti ti-user text-primary me-1"></i>Member Pembooking</small>
                            <h6 class="fw-bold text-dark mb-1"><?= esc($res['first_name'] . ' ' . $res['last_name']); ?></h6>
                            <span class="badge <?= $res['tier']['badge']; ?> mt-1"><i class="ti <?= $res['tier']['icon']; ?> me-1"></i><?= esc($res['tier']['name']); ?></span>
                          </div>
                        </div>
                        <div class="col-12 col-md-6">
                          <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                            <div>
                              <small class="text-muted d-block">Waktu Transaksi Booking</small>
                              <strong class="text-dark"><?= \CodeIgniter\I18n\Time::parse($res['created_at'], locale: 'id')->toLocalizedString('d MMMM Y HH:mm'); ?></strong>
                            </div>
                            <div class="mt-2">
                              <span class="badge badge-subtle-warning px-3 py-2"><i class="ti ti-clock me-1"></i>Menunggu Buku Kembalian</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Section 5: Kartu Buku / Salinan Fisik & Non-Fisik -->
<div class="card shadow-sm rounded-4 mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <div>
        <h5 class="fw-bold text-dark mb-1"><i class="ti ti-id-badge-2 text-primary me-1"></i> Kartu Buku / Salinan Fisik & Non-Fisik</h5>
        <p class="text-muted fs-2 mb-0">Kelola eksemplar fisik/digital yang terdaftar untuk buku ini</p>
      </div>
      <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addCopyModal">
        <i class="ti ti-plus me-1"></i> Tambah Kartu Buku Baru
      </button>
    </div>

    <?php if (empty($items)) : ?>
      <div class="alert alert-warning text-center py-4 rounded-3">
        <i class="ti ti-id-badge-2 fs-7 d-block mb-2 text-warning"></i>
        Belum ada kartu buku terdaftar untuk buku ini. Silakan klik tombol <strong>"Tambah Kartu Buku Baru"</strong> di atas.
      </div>
    <?php else : ?>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        <?php foreach ($items as $item) : ?>
          <div class="col">
            <div class="card border border-2 border-primary-subtle h-100 shadow-sm rounded-3 overflow-hidden">
              <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fs-1 text-muted fw-bold"><i class="ti ti-barcode me-1"></i>BARCODE KARTU</span>
                    <div>
                      <span class="badge badge-subtle-primary me-1">
                        <?= ($item['copy_type'] ?? 'fisik') === 'non_fisik' ? 'Non-Fisik' : 'Fisik'; ?>
                      </span>
                      <span class="badge badge-subtle-secondary">
                        <?= ucfirst($item['acquisition'] ?? 'pembelian'); ?>
                      </span>
                    </div>
                  </div>

                  <div class="text-center my-2 p-2 bg-light rounded border border-light-subtle">
                    <h4 class="fw-bold font-monospace mb-1 text-dark"><?= $item['item_code']; ?></h4>
                    <small class="text-muted fs-1"><i class="ti ti-building-arch me-1"></i>Rak: <?= $item['rack_name'] ?? $book['rack']; ?> (Lantai <?= $item['rack_floor'] ?? $book['floor']; ?>)</small>
                  </div>

                  <div class="d-flex justify-content-between align-items-center mb-2 fs-2">
                    <span class="text-muted">Kondisi:</span>
                    <?php if ($item['condition'] === 'baik') : ?>
                      <span class="badge badge-subtle-primary fw-semibold"><i class="ti ti-check me-1"></i>Baik</span>
                    <?php elseif ($item['condition'] === 'rusak') : ?>
                      <span class="badge badge-subtle-warning fw-semibold"><i class="ti ti-alert-triangle me-1"></i>Rusak</span>
                    <?php else : ?>
                      <span class="badge badge-subtle-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Hilang</span>
                    <?php endif; ?>
                  </div>

                  <?php if (!empty($item['condition_note'])) : ?>
                    <div class="p-2 bg-light-subtle rounded border border-warning-subtle text-dark fs-1 mb-2">
                      <i class="ti ti-notes text-warning me-1"></i><strong>Catatan Fisik:</strong> <?= esc($item['condition_note']); ?>
                    </div>
                  <?php endif; ?>

                  <div class="d-flex justify-content-between align-items-center mb-2 fs-2">
                    <span class="text-muted">Status:</span>
                    <?php if ($item['status'] === 'tersedia') : ?>
                      <span class="badge badge-subtle-primary"><i class="ti ti-check me-1"></i>Tersedia</span>
                    <?php else : ?>
                      <span class="badge badge-subtle-warning"><i class="ti ti-clock me-1"></i>Dipinjam</span>
                    <?php endif; ?>
                  </div>

                  <!-- Dynamic Row Under Status: Display Harga Buku always, and Pendonasi if donasi/hibah -->
                  <div class="d-flex justify-content-between align-items-center mb-1 fs-2 pt-1 border-top border-light-subtle">
                    <span class="text-muted"><i class="ti ti-coin text-warning me-1"></i>Harga Buku:</span>
                    <strong class="text-dark">Rp <?= number_format(floatval($item['price'] ?? 0), 0, ',', '.'); ?></strong>
                  </div>
                  <?php if (in_array(strtolower($item['acquisition'] ?? 'pembelian'), ['donasi', 'hibah'])) : ?>
                    <div class="d-flex justify-content-between align-items-center mb-2 fs-2">
                      <span class="text-muted"><i class="ti ti-heart-handshake text-primary me-1"></i>Pendonasi:</span>
                      <strong class="text-primary"><?= !empty($item['donor_first_name']) ? esc($item['donor_first_name'] . ' ' . $item['donor_last_name']) : 'Anonim'; ?></strong>
                    </div>
                  <?php endif; ?>
                </div>

                <div>
                  <div class="border-top pt-2 mt-2 text-muted fs-1">
                    <div>Dibuat: <?= !empty($item['created_at']) ? \CodeIgniter\I18n\Time::parse($item['created_at'], locale: 'id')->toLocalizedString('d MMM Y HH:mm') : '-'; ?></div>
                    <div>Diupdate: <?= !empty($item['updated_at']) ? \CodeIgniter\I18n\Time::parse($item['updated_at'], locale: 'id')->toLocalizedString('d MMM Y HH:mm') : '-'; ?></div>
                  </div>

                  <div class="d-flex gap-2 mt-3 pt-2 border-top">
                    <button type="button" class="btn btn-outline-info btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#conditionLogModal-<?= $item['id']; ?>" title="Lihat Riwayat Kerusakan & Perubahan Kondisi">
                      <i class="ti ti-history me-1"></i>Riwayat
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#editCopyModal-<?= $item['id']; ?>">
                      <i class="ti ti-edit me-1"></i>Edit
                    </button>
                    <form action="<?= base_url("admin/books/copies/{$item['id']}/delete"); ?>" method="post" class="flex-fill">
                      <?= csrf_field(); ?>
                      <button type="submit" class="btn btn-outline-danger btn-sm w-100" data-confirm="Hapus kartu buku ini?">
                        <i class="ti ti-trash me-1"></i>Hapus
                      </button>
                    </form>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- Modal Riwayat Kondisi & Kerusakan Eksemplar -->
          <div class="modal fade" id="conditionLogModal-<?= $item['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header card-gradient-header rounded-top-4 p-3">
                  <h5 class="modal-title fw-bold text-white d-flex align-items-center mb-0">
                    <i class="ti ti-history me-2 fs-5 text-white"></i> Riwayat Kondisi & Kerusakan Eksemplar
                  </h5>
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #fcf8f2;">
                  <div class="p-3 rounded-3 border mb-3" style="background-color: #f8f2e6; border-color: #e8decb !important;">
                    <div class="fw-bold fs-3 mb-1" style="color: #6e4727;"><?= esc($book['title']); ?></div>
                    <div class="fs-2" style="color: #8b5e3c;"><i class="ti ti-barcode me-1"></i>Kode Eksemplar: <code class="font-monospace fw-bold px-2 py-1 rounded" style="background-color: #e8decb; color: #6e4727;"><?= esc($item['item_code']); ?></code></div>
                  </div>

                  <?php $logs = $itemLogs[$item['id']] ?? []; ?>
                  <?php if (empty($logs)) : ?>
                    <div class="text-center py-4 text-muted">
                      <i class="ti ti-clipboard-x fs-8 d-block mb-2 text-secondary opacity-50"></i>
                      Belum ada catatan riwayat perubahan kondisi fisik untuk eksemplar ini.
                    </div>
                  <?php else : ?>
                    <div class="table-responsive rounded-3 border" style="border-color: #e8decb !important;">
                      <table class="table table-hover align-middle mb-0 fs-2">
                        <thead>
                          <tr style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%); color: #ffffff;">
                            <th class="py-2 text-white">Tanggal</th>
                            <th class="py-2 text-center text-white">Kondisi</th>
                            <th class="py-2 text-white">Catatan Kerusakan</th>
                            <th class="py-2 text-white">Penanggung Jawab / Member</th>
                            <th class="py-2 text-white">Petugas Pencatat</th>
                          </tr>
                        </thead>
                        <tbody style="background-color: #ffffff;">
                          <?php foreach ($logs as $lg) : ?>
                            <tr>
                              <td class="fw-bold text-nowrap" style="color: #2d241e;">
                                <?= \CodeIgniter\I18n\Time::parse($lg['created_at'], locale: 'id')->toLocalizedString('d/MM/y HH:mm'); ?>
                              </td>
                              <td class="text-center">
                                <?php if ($lg['condition_state'] === 'baik') : ?>
                                  <span class="badge bg-success text-white px-2 py-1 rounded-pill"><i class="ti ti-check me-1"></i>Baik</span>
                                <?php elseif ($lg['condition_state'] === 'rusak') : ?>
                                  <span class="badge bg-warning text-dark px-2 py-1 rounded-pill"><i class="ti ti-alert-triangle me-1"></i>Rusak</span>
                                <?php else : ?>
                                  <span class="badge bg-danger text-white px-2 py-1 rounded-pill"><i class="ti ti-circle-x me-1"></i>Hilang</span>
                                <?php endif; ?>
                              </td>
                              <td class="fw-semibold" style="color: #2d241e;">
                                <?= esc($lg['condition_note'] ?: '-'); ?>
                              </td>
                              <td>
                                <?php if (!empty($lg['first_name'])) : ?>
                                  <div class="fw-bold" style="color: #6e4727;"><?= esc("{$lg['first_name']} {$lg['last_name']}"); ?></div>
                                  <small class="font-monospace fw-semibold" style="color: #8b5e3c;"><?= esc($lg['member_uid']); ?></small>
                                <?php else : ?>
                                  <span class="text-muted">-</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <small class="fw-semibold" style="color: #2d241e;"><?= esc($lg['staff_user'] ?? 'Petugas'); ?></small>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php endif; ?>
                </div>
                <div class="modal-footer rounded-bottom-4" style="background-color: #f8f2e6; border-top: 1px solid #e8decb;">
                  <button type="button" class="btn text-white rounded-pill px-4 btn-sm fw-bold" style="background-color: #8b5e3c; border-color: #6e4727;" data-bs-dismiss="modal">Tutup</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Edit Kartu Buku -->
          <div class="modal fade" id="editCopyModal-<?= $item['id']; ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title fw-bold"><i class="ti ti-edit text-primary me-1"></i> Edit Kartu Buku (<?= $item['item_code']; ?>)</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?= base_url("admin/books/copies/{$item['id']}/update"); ?>" method="post">
                  <?= csrf_field(); ?>
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="form-label fw-semibold">Kode Kartu Buku</label>
                      <input type="text" class="form-control" name="item_code" value="<?= $item['item_code']; ?>" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Salinan (Fisik / Non-Fisik)</label>
                      <select class="form-select" name="copy_type" required>
                        <option value="fisik" <?= ($item['copy_type'] ?? 'fisik') === 'fisik' ? 'selected' : ''; ?>>Fisik</option>
                        <option value="non_fisik" <?= ($item['copy_type'] ?? 'fisik') === 'non_fisik' ? 'selected' : ''; ?>>Non-Fisik</option>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Perolehan Buku</label>
                      <?php $curAcq = strtolower($item['acquisition'] ?? 'pembelian'); ?>
                      <select class="form-select" name="acquisition_type" id="acq_edit_<?= $item['id']; ?>" onchange="toggleDonorEdit(<?= $item['id']; ?>)" required>
                        <option value="pembelian" <?= $curAcq === 'pembelian' ? 'selected' : ''; ?>>Pembelian</option>
                        <option value="donasi" <?= $curAcq === 'donasi' ? 'selected' : ''; ?>>Donasi</option>
                        <option value="hibah" <?= $curAcq === 'hibah' ? 'selected' : ''; ?>>Hibah</option>
                      </select>
                    </div>

                    <div class="mb-3 <?= in_array($curAcq, ['donasi', 'hibah']) ? '' : 'd-none'; ?>" id="donor_section_edit_<?= $item['id']; ?>">
                      <label class="form-label fw-semibold">Anggota Pendonasi (Cukup Nama)</label>
                      <select class="form-select" name="donated_by_member_id">
                        <option value="">-- Pilih Anggota Pendonasi --</option>
                        <?php foreach ($allMembers as $m) : ?>
                          <option value="<?= $m['id']; ?>" <?= $item['donated_by_member_id'] == $m['id'] ? 'selected' : ''; ?>>
                            <?= esc($m['first_name'] . ' ' . $m['last_name']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3" id="price_section_edit_<?= $item['id']; ?>">
                      <label class="form-label fw-semibold">Harga Buku / Nilai Kompensasi (Rp)</label>
                      <input type="number" class="form-control" name="price" value="<?= $item['price'] ?? 0; ?>" placeholder="Masukkan harga / nilai buku">
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Lokasi Rak</label>
                      <select class="form-select" name="rack_id" required>
                        <?php foreach ($racks as $r) : ?>
                          <option value="<?= $r['id']; ?>" <?= $item['rack_id'] == $r['id'] ? 'selected' : ''; ?>>
                            Rak <?= $r['name']; ?> (Lantai <?= $r['floor']; ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Kondisi Fisik</label>
                      <select class="form-select" name="condition" required>
                        <option value="baik" <?= $item['condition'] === 'baik' ? 'selected' : ''; ?>>Baik</option>
                        <option value="rusak" <?= $item['condition'] === 'rusak' ? 'selected' : ''; ?>>Rusak</option>
                        <option value="hilang" <?= $item['condition'] === 'hilang' ? 'selected' : ''; ?>>Hilang</option>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label class="form-label fw-semibold">Catatan Fisik / Detail Kerusakan</label>
                      <textarea class="form-control" name="condition_note" rows="2" placeholder="Contoh: Sampul depan sobek 2cm, halaman 15 terlipat"><?= esc($item['condition_note'] ?? ''); ?></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-pill-gold fw-bold">Simpan Perubahan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah Kartu Buku Baru -->
<div class="modal fade" id="addCopyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="ti ti-plus text-primary me-1"></i> Tambah Kartu Buku Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url("admin/books/{$book['id']}/copies"); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Kode Kartu Buku (Opsional)</label>
            <input type="text" class="form-control" name="item_code" placeholder="Kosongkan untuk otomatis">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Salinan (Fisik / Non-Fisik)</label>
            <select class="form-select" name="copy_type" required>
              <option value="fisik" selected>Fisik</option>
              <option value="non_fisik">Non-Fisik</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Perolehan Buku</label>
            <select class="form-select" name="acquisition_type" id="acq_add" onchange="toggleDonorAdd()" required>
              <option value="pembelian" selected>Pembelian</option>
              <option value="donasi">Donasi</option>
              <option value="hibah">Hibah</option>
            </select>
          </div>

          <div class="mb-3 d-none" id="donor_section_add">
            <label class="form-label fw-semibold">Anggota Pendonasi (Cukup Nama)</label>
            <select class="form-select select2" name="donated_by_member_id">
              <option value="">-- Pilih Anggota Pendonasi --</option>
              <?php foreach ($allMembers as $m) : ?>
                <option value="<?= $m['id']; ?>"><?= esc($m['first_name'] . ' ' . $m['last_name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3" id="price_section_add">
            <label class="form-label fw-semibold">Harga Buku / Nilai Kompensasi (Rp)</label>
            <input type="number" class="form-control" name="price" value="0" placeholder="Masukkan harga / nilai buku">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Lokasi Rak</label>
            <select class="form-select" name="rack_id" required>
              <?php foreach ($racks as $r) : ?>
                <option value="<?= $r['id']; ?>" <?= $book['rack_id'] == $r['id'] ? 'selected' : ''; ?>>
                  Rak <?= $r['name']; ?> (Lantai <?= $r['floor']; ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Kondisi Fisik</label>
            <select class="form-select" name="condition" required>
              <option value="baik" selected>Baik</option>
              <option value="rusak">Rusak</option>
              <option value="hilang">Hilang</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-pill-gold fw-bold">Tambah Kartu Buku</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Form Booking Buku (Gold & Platinum) -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="bookingModalLabel"><i class="ti ti-bookmark text-warning me-1"></i> Form Booking Buku (Gold & Platinum)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/reservations'); ?>" method="post">
        <?= csrf_field(); ?>
        <input type="hidden" name="book_id" value="<?= $book['id']; ?>">
        <div class="modal-body">
          <div class="p-3 bg-light rounded border border-light-subtle mb-3">
            <h6 class="fw-bold text-dark mb-1"><?= $book['title']; ?> (<?= $book['year']; ?>)</h6>
            <small class="text-muted">Pengarang: <?= $book['author']; ?> | Stok Fisik Tersedia: <?= $bookStock; ?></small>
          </div>

          <div class="mb-3">
            <label for="member_id_booking" class="form-label fw-semibold">Pilih Member Pembooking (Gold & Platinum Only)</label>
            <select class="form-select select2" id="member_id_booking" name="member_id" required>
              <option value="" disabled selected>--Pilih Member Layak Booking--</option>
              <?php foreach ($allMembers as $m) : ?>
                <?php $mTier = \App\Models\MemberModel::getTierDetails($m); ?>
                <?php if ($mTier['can_book']) : ?>
                  <option value="<?= $m['id']; ?>">
                    <?= esc($m['first_name'] . ' ' . $m['last_name']); ?> (<?= esc($mTier['name']); ?>)
                  </option>
                <?php endif; ?>
              <?php endforeach; ?>
            </select>
            <div class="form-text fs-1">Fitur booking hanya terbuka bagi pemegang kartu Gold Member (donasi min. 7) dan Platinum Member (donasi min. 15 / manual).</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-warning fw-bold">Simpan Transaksi Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleDonorAdd() {
  const acq = document.getElementById('acq_add').value;
  const donorSec = document.getElementById('donor_section_add');
  if (acq === 'donasi' || acq === 'hibah') {
    if (donorSec) donorSec.classList.remove('d-none');
  } else {
    if (donorSec) donorSec.classList.add('d-none');
  }
}

function toggleDonorEdit(id) {
  const acq = document.getElementById('acq_edit_' + id).value;
  const donorSec = document.getElementById('donor_section_edit_' + id);
  if (acq === 'donasi' || acq === 'hibah') {
    if (donorSec) donorSec.classList.remove('d-none');
  } else {
    if (donorSec) donorSec.classList.add('d-none');
  }
}
</script>
<script src="<?= base_url('assets/libs/jsbarcode/JsBarcode.all.min.js'); ?>"></script>
<script>
  function renderIsbnBarcode() {
    const svgEl = document.querySelector('#isbnBarcodeSvg');
    if (!svgEl) return;
    const rawIsbn = '<?= $book['isbn'] ?? ''; ?>';
    const cleanIsbn = rawIsbn.replace(/[^0-9X]/gi, '');
    if (!cleanIsbn) return;

    try {
      JsBarcode("#isbnBarcodeSvg", cleanIsbn, {
        format: cleanIsbn.length === 13 ? "EAN13" : "CODE128",
        width: 1.8,
        height: 50,
        displayValue: true,
        font: "monospace",
        fontSize: 14,
        margin: 6,
        background: "#ffffff",
        lineColor: "#000000"
      });
    } catch (e) {
      JsBarcode("#isbnBarcodeSvg", cleanIsbn, {
        format: "CODE128",
        width: 1.8,
        height: 50,
        displayValue: true,
        font: "monospace",
        fontSize: 14,
        margin: 6,
        background: "#ffffff",
        lineColor: "#000000"
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderIsbnBarcode);
  } else {
    renderIsbnBarcode();
  }

  function printIsbnBarcode() {
    const svgEl = document.querySelector('#isbnBarcodeSvg');
    if (!svgEl) return;
    const svgData = new XMLSerializer().serializeToString(svgEl);
    const printWin = window.open('', '_blank', 'width=550,height=420');
    printWin.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Cetak Barcode ISBN - <?= esc($book['title']); ?></title>
        <style>
          body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; padding: 20px; text-align: center; }
          .barcode-card { border: 2px dashed #0d6efd; padding: 20px 30px; border-radius: 12px; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
          h3 { margin: 0 0 6px 0; font-size: 18px; color: #1e293b; }
          p { margin: 0 0 15px 0; font-size: 13px; color: #64748b; font-weight: 600; }
        </style>
      </head>
      <body>
        <div class="barcode-card">
          <h3><?= esc($book['title']); ?></h3>
          <p>PERPUSTAKAAN ASSALAFIYYAH &bull; ISBN: <?= esc($book['isbn']); ?></p>
          ${svgData}
        </div>
        <script>
          window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 500);
          };
        <\/script>
      </body>
      </html>
    `);
    printWin.document.close();
  }
</script>
<?= $this->endSection() ?>