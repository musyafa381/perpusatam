<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Antrean Booking & Reservasi Buku</title>
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
          <i class="ti ti-bookmark me-1"></i> Fitur Prioritas Anggota
        </div>
        <h3 class="text-white fw-bold mb-1">Antrean Booking & Reservasi Buku</h3>
        <p class="text-white-50 mb-0">Kelola pemesanan/booking buku khusus untuk Gold Member dan Platinum Member.</p>
      </div>
      <div>
        <button class="btn btn-light text-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newBookingModal">
          <i class="ti ti-plus me-1"></i> Tambah Transaksi Booking
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Filter & Search Bar -->
<div class="card info-card border-0 mb-4">
  <div class="card-body p-3">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-8">
        <div class="d-flex gap-2 flex-wrap" role="group">
          <a href="<?= base_url('admin/reservations'); ?>" class="btn btn-outline-primary btn-sm rounded-3 <?= empty($statusFilter) ? 'active fw-bold' : ''; ?>">
            Semua Antrean (<?= count($reservations); ?>)
          </a>
          <a href="<?= base_url('admin/reservations?status=pending'); ?>" class="btn btn-outline-warning btn-sm rounded-3 <?= $statusFilter === 'pending' ? 'active fw-bold' : ''; ?>">
            <i class="ti ti-clock me-1"></i> Menunggu (Pending)
          </a>
          <a href="<?= base_url('admin/reservations?status=fulfilled'); ?>" class="btn btn-outline-success btn-sm rounded-3 <?= $statusFilter === 'fulfilled' ? 'active fw-bold' : ''; ?>">
            <i class="ti ti-check me-1"></i> Selesai (Fulfilled)
          </a>
          <a href="<?= base_url('admin/reservations?status=cancelled'); ?>" class="btn btn-outline-secondary btn-sm rounded-3 <?= $statusFilter === 'cancelled' ? 'active fw-bold' : ''; ?>">
            <i class="ti ti-x me-1"></i> Dibatalkan
          </a>
        </div>
      </div>
      <div class="col-12 col-md-4">
        <form action="" method="get">
          <?php if (!empty($statusFilter)) : ?>
            <input type="hidden" name="status" value="<?= esc($statusFilter); ?>">
          <?php endif; ?>
          <div class="input-group search-group">
            <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari member / buku...">
            <button class="btn btn-primary fw-semibold" type="submit"><i class="ti ti-search"></i></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <?php if (empty($reservations)) : ?>
      <div class="text-center py-4 text-muted">
        <i class="ti ti-info-circle fs-7 d-block mb-1"></i>
        <b>Tidak ada data antrean booking ditemukan.</b>
      </div>
    <?php else : ?>
      <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
        <table class="table table-hover align-middle table-assalafiyyah mb-0">
          <thead>
            <tr>
              <th scope="col" class="text-center py-3" style="width: 45px;">#</th>
              <th scope="col" class="py-3">ANGGOTA / PEMESAN</th>
              <th scope="col" class="py-3 text-center">TIER MEMBER</th>
              <th scope="col" class="py-3">JUDUL BUKU</th>
              <th scope="col" class="py-3">TANGGAL RESERVASI</th>
              <th scope="col" class="py-3 text-center">STATUS STOCK</th>
              <th scope="col" class="py-3 text-center">STATUS RESERVASI</th>
              <th scope="col" class="py-3 text-center pe-4" style="width: 170px;">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; ?>
            <?php foreach ($reservations as $r) : ?>
              <tr>
                <th scope="row" class="text-center text-muted fw-bold"><?= $i++; ?></th>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="table-avatar-initial" style="flex-shrink: 0;">
                      <?= strtoupper(substr($r['first_name'] ?? 'A', 0, 1) . substr($r['last_name'] ?? '', 0, 1)); ?>
                    </div>
                    <div>
                      <div class="fw-bold text-dark fs-3 mb-0"><?= esc("{$r['first_name']} {$r['last_name']}"); ?></div>
                      <small class="text-muted font-monospace fs-1"><i class="ti ti-barcode me-1"></i>UID: <?= esc($r['member_uid'] ?? '-'); ?></small>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <?php 
                  $tCode = strtolower($r['tier']['code'] ?? '');
                  if ($tCode === 'gold') : ?>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill shadow-xs"><i class="ti ti-crown me-1"></i>Gold Member (Manual)</span>
                  <?php elseif ($tCode === 'platinum' || $tCode === 'living_library') : ?>
                    <span class="badge bg-primary text-white fw-bold px-3 py-1.5 rounded-pill shadow-xs"><i class="ti ti-star me-1"></i>Platinum Member</span>
                  <?php else : ?>
                    <span class="badge bg-secondary text-white fw-bold px-3 py-1.5 rounded-pill shadow-xs"><i class="ti ti-user me-1"></i><?= esc($r['tier']['name'] ?? 'Member'); ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-bold text-dark fs-3 mb-0" style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($r['book_title']); ?></div>
                  <small class="text-muted fs-1"><i class="ti ti-bookmark me-1"></i>ID Buku: #<?= esc($r['book_id'] ?? '-'); ?></small>
                </td>
                <td>
                  <div class="fw-bold text-dark fs-3 mb-0"><?= \CodeIgniter\I18n\Time::parse($r['created_at'], locale: 'id')->toLocalizedString('dd/MM/Y'); ?></div>
                  <small class="text-muted fs-1"><i class="ti ti-clock me-1"></i><?= \CodeIgniter\I18n\Time::parse($r['created_at'], locale: 'id')->toLocalizedString('HH:mm'); ?></small>
                </td>
                <td class="text-center">
                  <?php if (($r['available_stock'] ?? 0) > 0) : ?>
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-1.5 rounded-pill"><i class="ti ti-check me-1"></i>Tersedia (<?= (int)$r['available_stock']; ?>)</span>
                  <?php else : ?>
                    <span class="badge bg-danger-subtle text-danger fw-bold px-3 py-1.5 rounded-pill"><i class="ti ti-circle-x me-1"></i>Stok Habis</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <?php if ($r['status'] === 'pending') : ?>
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill"><i class="ti ti-clock me-1"></i>Pending</span>
                  <?php elseif ($r['status'] === 'fulfilled') : ?>
                    <span class="badge bg-success text-white fw-bold px-3 py-1.5 rounded-pill"><i class="ti ti-check-double me-1"></i>Selesai</span>
                  <?php else : ?>
                    <span class="badge bg-secondary text-white fw-bold px-3 py-1.5 rounded-pill"><i class="ti ti-x me-1"></i>Dibatalkan</span>
                  <?php endif; ?>
                </td>
                <td class="text-center pe-4">
                  <div class="d-flex justify-content-center align-items-center gap-1 flex-nowrap">
                    <?php if ($r['status'] === 'pending') : ?>
                      <form action="<?= base_url("admin/reservations/{$r['id']}/fulfill"); ?>" method="post" class="m-0">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-success btn-sm fw-bold rounded-pill px-3 shadow-xs d-inline-flex align-items-center gap-1" title="Tandai Selesai / Serahkan Buku">
                          <i class="ti ti-check"></i> Process
                        </button>
                      </form>
                    <?php endif; ?>

                    <button type="button" class="btn btn-outline-primary btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#resDetailModal-<?= $r['id']; ?>" title="Detail Booking">
                      <i class="ti ti-eye"></i>
                    </button>

                    <button type="button" class="btn btn-outline-warning btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="printBookingSticker58mm('<?= addslashes(esc("{$r['first_name']} {$r['last_name']}")); ?>', '<?= addslashes(esc($r['tier']['name'])); ?>', '<?= esc($r['member_uid'] ?? ''); ?>', '<?= addslashes(esc($r['book_title'])); ?>', '<?= addslashes(esc($r['book_author'] ?? '')); ?>', '<?= addslashes(esc($r['book_call_number'] ?? ($r['book_ddc'] ?? ''))); ?>', '<?= esc($r['book_isbn'] ?? ''); ?>', '<?= addslashes(esc($r['book_rack'] ?? '-')); ?>', '<?= esc($r['book_floor'] ?? '-'); ?>', '<?= \CodeIgniter\I18n\Time::parse($r['created_at'], locale: 'id')->toLocalizedString('d MMM Y HH:mm'); ?>', '<?= esc($r['status']); ?>')" title="Cetak Stiker Booking (58mm)">
                      <i class="ti ti-printer"></i>
                    </button>

                    <form action="<?= base_url("admin/reservations/{$r['id']}/delete"); ?>" method="post" class="m-0">
                      <?= csrf_field(); ?>
                      <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-0 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-confirm="Hapus data booking ini secara permanen?" title="Hapus Booking">
                        <i class="ti ti-x"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>

              <!-- Modal Detail Booking & Buku -->
              <div class="modal fade" id="resDetailModal-<?= $r['id']; ?>" tabindex="-1" aria-hidden="true">
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
                            <img src="<?= getBookCover($r['book_cover']); ?>" alt="<?= esc($r['book_title']); ?>" class="img-fluid rounded-3 border shadow-sm" style="max-height: 180px; object-fit: cover;">
                          </div>
                          <div class="col-12 col-md-8">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                              <h4 class="fw-bold text-dark mb-0"><?= esc($r['book_title']); ?></h4>
                              <?php if (!empty($r['book_category'])) : ?>
                                <span class="badge badge-subtle-primary px-2.5 py-1 fs-2">
                                  <i class="ti ti-category me-1"></i><?= esc($r['book_category']); ?>
                                </span>
                              <?php endif; ?>
                              <?php if (stripos($r['book_category'] ?? '', 'novel') !== false) : ?>
                                <span class="badge badge-subtle-warning px-2.5 py-1 fs-2">
                                  <i class="ti ti-lock me-1"></i> Kategori Novel (Silver+)
                                </span>
                              <?php endif; ?>
                            </div>

                            <div class="text-muted fs-2 mb-2"><i class="ti ti-calendar me-1"></i>Tahun Terbit: <strong><?= esc($r['book_year']); ?></strong></div>

                            <div class="row g-2 p-2 bg-light rounded-3 border border-light-subtle fs-2">
                              <div class="col-6">
                                <small class="text-muted d-block"><i class="ti ti-user text-primary me-1"></i>Pengarang</small>
                                <strong class="text-dark"><?= esc($r['book_author'] ?? '-'); ?></strong>
                              </div>
                              <div class="col-6">
                                <small class="text-muted d-block"><i class="ti ti-building text-primary me-1"></i>Penerbit</small>
                                <strong class="text-dark"><?= esc($r['book_publisher'] ?? '-'); ?></strong>
                              </div>
                              <div class="col-6 mt-2">
                                <small class="text-muted d-block"><i class="ti ti-barcode text-primary me-1"></i>ISBN</small>
                                <strong class="text-dark font-monospace"><?= esc($r['book_isbn'] ?: '-'); ?></strong>
                              </div>
                              <div class="col-6 mt-2">
                                <small class="text-muted d-block"><i class="ti ti-building-arch text-primary me-1"></i>Lokasi Default Rak</small>
                                <strong class="text-dark">Rak <?= esc($r['book_rack'] ?? '-'); ?> (Lantai <?= esc($r['book_floor'] ?? '-'); ?>)</strong>
                              </div>
                              <div class="col-12 mt-2 pt-2 border-top border-light-subtle">
                                <small class="text-muted d-block mb-1"><i class="ti ti-map-pin text-primary me-1"></i>Posisi / Ketersediaan Buku saat Ini</small>
                                <?php if (($r['available_stock'] ?? 0) > 0) : ?>
                                  <span class="badge badge-subtle-primary px-3 py-1.5 fs-2"><i class="ti ti-building-store me-1"></i>Tersedia di Perpustakaan (Rak <?= esc($r['book_rack'] ?? '-'); ?> - Lantai <?= esc($r['book_floor'] ?? '-'); ?>)</span>
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
                            <h6 class="fw-bold text-dark mb-1"><?= esc("{$r['first_name']} {$r['last_name']}"); ?></h6>
                            <span class="badge <?= $r['tier']['badge']; ?> mt-1"><i class="ti <?= $r['tier']['icon']; ?> me-1"></i><?= esc($r['tier']['name']); ?></span>
                          </div>
                        </div>
                        <div class="col-12 col-md-6">
                          <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column justify-content-between">
                            <div>
                              <small class="text-muted d-block">Waktu Transaksi Booking</small>
                              <strong class="text-dark"><?= \CodeIgniter\I18n\Time::parse($r['created_at'], locale: 'id')->toLocalizedString('d MMMM Y HH:mm'); ?></strong>
                            </div>
                            <div class="mt-2">
                              <span class="badge badge-subtle-warning px-3 py-2"><i class="ti ti-clock me-1"></i>Status: <?= ucfirst($r['status']); ?></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-warning btn-sm fw-bold me-auto rounded-pill px-3" onclick="printBookingSticker58mm('<?= addslashes(esc("{$r['first_name']} {$r['last_name']}")); ?>', '<?= addslashes(esc($r['tier']['name'])); ?>', '<?= esc($r['member_uid'] ?? ''); ?>', '<?= addslashes(esc($r['book_title'])); ?>', '<?= addslashes(esc($r['book_author'] ?? '')); ?>', '<?= addslashes(esc($r['book_call_number'] ?? ($r['book_ddc'] ?? ''))); ?>', '<?= esc($r['book_isbn'] ?? ''); ?>', '<?= addslashes(esc($r['book_rack'] ?? '-')); ?>', '<?= esc($r['book_floor'] ?? '-'); ?>', '<?= \CodeIgniter\I18n\Time::parse($r['created_at'], locale: 'id')->toLocalizedString('d MMM Y HH:mm'); ?>', '<?= esc($r['status']); ?>')">
                        <i class="ti ti-printer me-1"></i> Cetak Stiker Booking (58mm)
                      </button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Tambah Booking Baru -->
<div class="modal fade" id="newBookingModal" tabindex="-1" aria-labelledby="newBookingModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="newBookingModalLabel"><i class="ti ti-plus text-primary me-1"></i> Tambah Transaksi Booking</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="<?= base_url('admin/reservations'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="book_id" class="form-label fw-semibold">Pilih Buku</label>
            <select class="form-select select2" id="book_id" name="book_id" required>
              <option value="" disabled selected>--Pilih Buku--</option>
              <?php foreach ($allBooks as $b) : ?>
                <option value="<?= $b['id']; ?>"><?= esc($b['title']); ?> (<?= esc($b['year']); ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="member_id" class="form-label fw-semibold">Pilih Member Pembooking (Gold & Platinum)</label>
            <select class="form-select select2" id="member_id" name="member_id" required>
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
            <div class="form-text fs-1">Hanya anggota Gold Member dan Platinum Member yang diizinkan melakukan booking.</div>
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary fw-bold px-4 rounded-3">Simpan Booking</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function printBookingSticker58mm(memberName, memberTier, memberUid, bookTitle, bookAuthor, bookCallNo, bookIsbn, rackName, rackFloor, bookingDate, bookingStatus) {
    const tempDiv = document.createElement('div');
    tempDiv.style.position = 'absolute';
    tempDiv.style.left = '-9999px';
    document.body.appendChild(tempDiv);

    let isbnSvgData = '';
    const cleanIsbn = (bookIsbn || '').replace(/[^0-9X]/gi, '');
    if (cleanIsbn && typeof JsBarcode !== 'undefined') {
      const isbnSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      tempDiv.appendChild(isbnSvg);
      try {
        JsBarcode(isbnSvg, cleanIsbn, {
          format: cleanIsbn.length === 13 ? "EAN13" : "CODE128",
          width: 0.9,
          height: 26,
          displayValue: true,
          font: "monospace",
          fontSize: 8.5,
          fontOptions: "bold",
          margin: 1,
          background: "#ffffff",
          lineColor: "#000000"
        });
        isbnSvgData = new XMLSerializer().serializeToString(isbnSvg);
      } catch (e) {
        try {
          JsBarcode(isbnSvg, cleanIsbn, {
            format: "CODE128",
            width: 0.9,
            height: 26,
            displayValue: true,
            font: "monospace",
            fontSize: 8.5,
            fontOptions: "bold",
            margin: 1,
            background: "#ffffff",
            lineColor: "#000000"
          });
          isbnSvgData = new XMLSerializer().serializeToString(isbnSvg);
        } catch (err) {}
      }
    }

    document.body.removeChild(tempDiv);

    const printWin = window.open('', '_blank', 'width=400,height=600');
    printWin.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>Stiker Booking - ${memberName}</title>
        <style>
          @page { size: 58mm auto; margin: 0mm; }
          * { box-sizing: border-box; margin: 0; padding: 0; }
          body {
            font-family: Arial, Helvetica, sans-serif;
            background: #ffffff;
            margin: 0 auto;
            padding: 1mm 2mm;
            width: 44mm;
            max-width: 44mm;
            color: #000000;
            -webkit-font-smoothing: none;
          }
          .no-print {
            text-align: center;
            margin-bottom: 6px;
          }
          .no-print button {
            padding: 6px 14px;
            background: #000000;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            cursor: pointer;
          }
          .sticker-card {
            width: 100%;
            border: 1px solid #000000;
            padding: 3px;
            text-align: center;
            background: #ffffff;
          }
          .header-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #000000;
          }
          .header-sub {
            font-size: 7.5px;
            font-weight: bold;
            color: #000000;
            display: block;
            margin-top: 1px;
          }
          .divider {
            border-bottom: 1px dashed #000000;
            margin: 3px 0;
          }
          .booking-badge {
            border: 1px solid #000000;
            padding: 2px;
            margin: 3px 0;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            background: #ffffff;
          }
          .member-info {
            text-align: left;
            font-size: 8.5px;
            margin: 3px 0;
            line-height: 1.3;
            color: #000000;
          }
          .member-name {
            font-size: 10px;
            font-weight: bold;
            word-wrap: break-word;
          }
          .call-box {
            border: 1px solid #000000;
            padding: 2px;
            margin: 3px 0;
            text-align: center;
          }
          .call-val {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            font-weight: bold;
          }
          .book-info {
            text-align: left;
            font-size: 8.5px;
            margin: 3px 0;
            line-height: 1.3;
            color: #000000;
          }
          .book-title {
            font-size: 10px;
            font-weight: bold;
            word-wrap: break-word;
          }
          .meta-line {
            font-size: 8.5px;
            margin-top: 1.5px;
            color: #000000;
            word-wrap: break-word;
          }
          .barcode-wrap {
            border-top: 1px dashed #000000;
            padding-top: 3px;
            margin-top: 3px;
            text-align: center;
          }
          .barcode-item svg {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
          }
          @media print {
            body { width: 44mm; max-width: 44mm; padding: 1mm 2mm; margin: 0 auto; }
            .no-print { display: none !important; }
            .sticker-card { border: 1px solid #000000; }
          }
        </style>
      </head>
      <body>
        <div class="no-print">
          <button onclick="window.print()">🖨️ Cetak Booking (58mm)</button>
        </div>
        <div class="sticker-card">
          <div class="header-title">
            Perpustakaan Assalafiyyah
            <span class="header-sub">🔖 TANDA STIKER BOOKING BUKU</span>
          </div>
          <div class="divider"></div>

          <div class="booking-badge">
            STATUS: ${bookingStatus ? bookingStatus.toUpperCase() : 'BOOKED'}
          </div>

          <div class="member-info">
            <div class="member-name">👤 ${memberName}</div>
            <div class="meta-line"><strong>Status Member:</strong> ${memberTier}</div>
            ${memberUid ? `<div class="meta-line"><strong>ID Member:</strong> ${memberUid}</div>` : ''}
            <div class="meta-line"><strong>Waktu Booking:</strong> ${bookingDate}</div>
          </div>

          <div class="divider"></div>

          <div class="book-info">
            <div class="book-title">📖 ${bookTitle}</div>
            ${bookAuthor ? `<div class="meta-line"><strong>Pengarang:</strong> ${bookAuthor}</div>` : ''}
            <div class="meta-line"><strong>Lokasi Rak:</strong> Rak ${rackName} (Lt. ${rackFloor})</div>
          </div>

          ${bookCallNo ? `
          <div class="call-box">
            <div style="font-size:7px; font-weight:bold;">NOMOR PANGGIL BUKU</div>
            <div class="call-val">${bookCallNo}</div>
          </div>` : ''}

          ${isbnSvgData ? `
          <div class="barcode-wrap">
            <div style="font-size:7.5px; font-weight:bold; margin-bottom:1px;">BARCODE ISBN</div>
            <div class="barcode-item">${isbnSvgData}</div>
          </div>` : ''}
        </div>
      </body>
      </html>
    `);
    printWin.document.close();
  }
</script>
<?= $this->endSection() ?>
