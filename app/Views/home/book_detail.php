<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title><?= esc($book['title']); ?> - Perpustakaan Assalafiyyah Mlangi</title>
<meta name="description" content="<?= esc(mb_strimwidth(strip_tags($book['synopsis'] ?? $book['title']), 0, 160, '...')); ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper(['upload_helper']); ?>

<div class="container py-4">

  <!-- Top Action Navigation -->
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <a href="<?= base_url('book'); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold bg-white shadow-sm transition-all" style="border-color: #d4c4b0; color: #6e4727;">
      <i class="ti ti-arrow-left me-1"></i> Kembali ke Katalog Buku
    </a>
    <div class="d-flex align-items-center flex-wrap gap-2">
      <a href="<?= base_url('buku-tamu'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold shadow-sm text-white" style="background-color: #c59b27; border: none;">
        <i class="ti ti-id-badge-2 me-1"></i> Buku Tamu Digital
      </a>
      <a href="<?= base_url('login'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold shadow-sm text-white" style="background-color: #6e4727; border: none;">
        <i class="ti ti-login me-1"></i> Login Admin
      </a>
    </div>
  </div>

  <?php
    $rawCover = $book['book_cover'] ?? '';
    $coverUrl = getBookCoverUrl($rawCover);
    $hasCover = !empty($rawCover) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
    $stockCount = (int)($book['quantity'] ?? 0);
  ?>

  <!-- Main Book Detail Card -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: #ffffff; border: 1.5px solid #e2d5c3 !important; box-shadow: 0 8px 24px rgba(110, 71, 39, 0.08) !important;">
    <div class="card-body p-4 p-md-5">
      <div class="row g-4 align-items-start">
        
        <!-- Book Cover Column -->
        <div class="col-12 col-md-4 col-lg-3 text-center">
          <div class="position-relative d-flex align-items-center justify-content-center overflow-hidden p-3 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #faf5ee 0%, #eee4d5 100%); min-height: 320px; border: 1px solid #e8decb;">
            <?php if ($hasCover): ?>
              <img src="<?= $coverUrl; ?>" alt="<?= esc($book['title']); ?>" class="img-fluid rounded-3 shadow-md" style="max-height: 360px; object-fit: contain; filter: drop-shadow(0 8px 16px rgba(110, 71, 39, 0.25));">
            <?php else: ?>
              <div class="d-flex flex-column align-items-center justify-content-center text-center p-4 w-100 h-100 rounded-3" style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%); color: #ffffff; min-height: 280px;">
                <i class="ti ti-book fs-1 mb-3" style="color: #c59b27; font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-white mb-0" style="font-family: 'Georgia', serif;"><?= esc($book['title']); ?></h5>
              </div>
            <?php endif; ?>
          </div>

          <!-- Availability Status Badge -->
          <div class="mt-3">
            <?php if ($stockCount > 0): ?>
              <div class="alert border-0 rounded-3 py-2 px-3 mb-0 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6 !important;">
                <i class="ti ti-circle-check fs-5"></i>
                <span class="fw-bold fs-7">Tersedia di Perpustakaan (<?= $stockCount; ?> Eksemplar)</span>
              </div>
            <?php else: ?>
              <div class="alert border-0 rounded-3 py-2 px-3 mb-0 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf !important;">
                <i class="ti ti-circle-x fs-5"></i>
                <span class="fw-bold fs-7">Sedang Dipinjam Semua</span>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Book Meta & Blurb Column -->
        <div class="col-12 col-md-8 col-lg-9">
          
          <!-- Category & Title Header -->
          <div class="mb-3">
            <span class="badge px-3 py-2 rounded-pill fw-bold text-white shadow-sm mb-2" style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 50%, #c59b27 100%); font-size: 0.75rem; letter-spacing: 0.5px;">
              <i class="ti ti-bookmark me-1"></i> <?= esc($book['category'] ?: 'Umum'); ?>
            </span>
            <h1 class="fw-extrabold mb-2" style="color: #4a3424 !important; font-family: 'Georgia', serif; font-size: 1.85rem; line-height: 1.3;">
              <?= esc($book['title']); ?>
            </h1>
            <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3 fs-7" style="color: #7c6857;">
              <span><i class="ti ti-user me-1" style="color: #c59b27;"></i> Pengarang: <strong style="color: #4a3424;"><?= esc($book['author'] ?: 'Tak Diketahui'); ?></strong></span>
              <span class="d-none d-sm-inline">•</span>
              <span><i class="ti ti-building me-1" style="color: #c59b27;"></i> Penerbit: <strong style="color: #4a3424;"><?= esc($book['publisher'] ?: 'Tak Diketahui'); ?></strong></span>
            </div>
          </div>

          <hr style="border-color: #e8decb; margin: 1.25rem 0;">

          <!-- Key Metadata Grid (Compact 2x2 Grid on Mobile with Spacing) -->
          <div class="row g-3 gy-3 mb-4">
            <!-- NOMOR PANGGIL / CALL NUMBER -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-3 h-100 border transition-all" style="background: linear-gradient(135deg, #fffdfa 0%, #faf4eb 100%); border-color: #e2d5c3 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #6e4727; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-tag fs-5" style="color: #c59b27;"></i> Nomor Panggil
                  </span>
                  <?php if (!empty($book['ddc'])): ?>
                    <span class="badge px-1.5 py-0.5 rounded-2" style="background: #e8decb; color: #6e4727; font-size: 0.62rem;">DDC: <?= esc($book['ddc']); ?></span>
                  <?php endif; ?>
                </div>
                <div class="fw-extrabold ps-1 text-truncate" style="color: #4a3424; font-size: 0.9rem; font-family: 'Courier New', monospace; letter-spacing: 0.5px;">
                  <?= esc($book['call_number'] ?: ($book['ddc'] ?: 'Belum diatur')); ?>
                </div>
              </div>
            </div>

            <!-- LOKASI RAK & LANTAI -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-3 h-100 border transition-all" style="background: #faf8f5; border-color: #e8decb !important;">
                <div class="mb-2">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #8b5e3c; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-columns fs-5" style="color: #c59b27;"></i> Lokasi Rak & Lantai
                  </span>
                </div>
                <div class="fw-bold text-truncate ps-1" style="color: #4a3424; font-size: 0.88rem;">
                  Rak <?= esc($book['rack'] ?: '-'); ?> <?= esc($book['floor'] ? '(Lantai ' . $book['floor'] . ')' : ''); ?>
                </div>
              </div>
            </div>

            <!-- ISBN -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-3 h-100 border transition-all" style="background: #faf8f5; border-color: #e8decb !important;">
                <div class="mb-2">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #8b5e3c; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-barcode fs-5" style="color: #c59b27;"></i> ISBN
                  </span>
                </div>
                <div class="fw-bold text-truncate ps-1" style="color: #4a3424; font-size: 0.88rem; font-family: 'Courier New', monospace;"><?= esc($book['isbn'] ?: '-'); ?></div>
              </div>
            </div>

            <!-- TAHUN TERBIT -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-3 h-100 border transition-all" style="background: #faf8f5; border-color: #e8decb !important;">
                <div class="mb-2">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #8b5e3c; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-calendar fs-5" style="color: #c59b27;"></i> Tahun Terbit
                  </span>
                </div>
                <div class="fw-bold text-truncate ps-1" style="color: #4a3424; font-size: 0.88rem;"><?= esc($book['year'] ?: '-'); ?></div>
              </div>
            </div>
          </div>

          <!-- Synopsis / Blurb Section Card -->
          <div class="card border-0 rounded-4 shadow-none p-4" style="background: linear-gradient(135deg, #fffdfa 0%, #faf6f0 100%); border: 1.5px solid #e8decb !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #e8decb !important;">
              <h5 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: #4a3424; font-family: 'Georgia', serif;">
                <i class="ti ti-feather text-warning fs-4" style="color: #c59b27 !important;"></i> Sinopsis / Blurb Buku
              </h5>
              <span class="badge rounded-pill px-2.5 py-1 fw-semibold" style="background: #f0e6d6; color: #6e4727 !important; font-size: 0.675rem; letter-spacing: 0.4px;">Ringkasan Pustaka</span>
            </div>

            <?php if (!empty($book['synopsis'])): ?>
              <div class="lh-lg fs-7 ps-2" style="color: #3d2f24; white-space: pre-line; text-align: justify; font-size: 0.95rem; line-height: 1.7; border-left: 3px solid #c59b27; padding-left: 14px;">
                <?= esc($book['synopsis']); ?>
              </div>
            <?php else: ?>
              <div class="text-muted py-4 text-center fs-7" style="color: #8c7b6d !important;">
                <i class="ti ti-notebook-off text-muted mb-2 d-block" style="font-size: 2.2rem; opacity: 0.5;"></i>
                <span>Sinopsis / blurb ringkasan belum ditambahkan untuk karya buku ini.</span>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Related Books Section -->
  <?php if (!empty($relatedBooks)): ?>
    <div class="mt-5">
      <h4 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #4a3424; font-family: 'Georgia', serif;">
        <i class="ti ti-books text-warning fs-4" style="color: #c59b27 !important;"></i> Koleksi Buku Lainnya dalam Kategori Ini
      </h4>
      <!-- Horizontal Scrollable Container for Related Books -->
      <div class="d-flex overflow-x-auto gap-3 pb-3 pt-1 px-1 custom-horizontal-scroll" style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory; scrollbar-width: thin; scrollbar-color: #c59b27 #f8f2e6;">
        <?php foreach ($relatedBooks as $rel): ?>
          <?php
            $rawRCover = $rel['book_cover'] ?? '';
            $rCover = getBookCoverUrl($rawRCover);
            $hasRCover = !empty($rawRCover) && ($rCover !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
            $rStockCount = (int)($rel['quantity'] ?? 0);
            $rCallNo = $rel['call_number'] ?: ($rel['ddc'] ? 'DDC ' . $rel['ddc'] : null);
          ?>
          <div style="flex: 0 0 200px; min-width: 200px; max-width: 200px; scroll-snap-align: start;">
            <a href="<?= base_url('book/' . ($rel['slug'] ?: $rel['id'])); ?>" class="text-decoration-none text-dark d-block h-100">
              <div class="card h-100 border-0 rounded-4 overflow-hidden catalog-book-card position-relative transition-all" style="background: #ffffff; border: 1.5px solid #e2d5c3 !important; box-shadow: 0 4px 14px rgba(110, 71, 39, 0.05); cursor: pointer;">
                
                <div class="catalog-book-cover position-relative d-flex align-items-center justify-content-center overflow-hidden p-2" style="height: 200px; background: linear-gradient(135deg, #faf5ee 0%, #eee4d5 100%);">
                  <?php if ($hasRCover): ?>
                    <img src="<?= $rCover; ?>" alt="<?= esc($rel['title']); ?>" loading="lazy" class="h-100 w-auto shadow-sm" style="object-fit: contain; max-width: 100%; max-height: 100%; filter: drop-shadow(0 5px 10px rgba(110, 71, 39, 0.2)); transition: transform 0.35s ease;">
                  <?php else: ?>
                    <div class="d-flex flex-column align-items-center justify-content-center text-center p-2.5 h-100 w-100 rounded-3" style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%); color: #ffffff;">
                      <i class="ti ti-book fs-2 mb-1.5" style="color: #c59b27;"></i>
                      <span class="fw-bold fs-7 text-white text-truncate-2 px-1" style="line-height: 1.25; font-family: 'Georgia', serif;"><?= esc($rel['title']); ?></span>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="card-body p-3 d-flex flex-column">
                  <h6 class="fw-bold mb-1 text-truncate-2" title="<?= esc($rel['title']); ?>" style="color: #2d1e18 !important; font-size: 0.88rem; line-height: 1.3; min-height: 2.4em; font-weight: 700;">
                    <?= esc($rel['title']); ?>
                  </h6>

                  <div class="fw-semibold mb-2 text-truncate" style="color: #8b5e3c !important; font-size: 0.76rem;">
                    <i class="ti ti-user me-1" style="color: #c59b27;"></i><?= esc($rel['author'] ?: 'Penulis tak diketahui'); ?>
                  </div>

                  <div class="d-flex flex-column gap-1 mb-2" style="font-size: 0.72rem !important;">
                    <?php if (!empty($rCallNo)): ?>
                      <div class="text-truncate" style="color: #6e4727;" title="Nomor Panggil Buku">
                        <i class="ti ti-tag me-1" style="color: #c59b27;"></i><span class="fw-semibold">Panggil:</span> <code class="fw-bold text-dark px-1 bg-light rounded" style="font-size: 0.65rem;"><?= esc($rCallNo); ?></code>
                      </div>
                    <?php endif; ?>

                    <?php if (!empty($rel['isbn'])): ?>
                      <div class="text-truncate" style="color: #6e4727;" title="ISBN Buku">
                        <i class="ti ti-barcode me-1" style="color: #c59b27;"></i><span class="fw-semibold">ISBN:</span> <span class="fw-bold text-dark" style="font-family: 'Courier New', monospace; font-size: 0.65rem;"><?= esc($rel['isbn']); ?></span>
                      </div>
                    <?php endif; ?>
                  </div>

                  <div class="mb-2">
                    <?php if ($rStockCount > 0): ?>
                      <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #e6f4ea; color: #137333; border: 1px solid #ceead6; font-size: 0.65rem;">
                        <i class="ti ti-circle-check me-1" style="font-size: 0.7rem;"></i>Tersedia (<?= $rStockCount ?>)
                      </span>
                    <?php else: ?>
                      <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; font-size: 0.65rem;">
                        <i class="ti ti-circle-x me-1" style="font-size: 0.7rem;"></i>Dipinjam
                      </span>
                    <?php endif; ?>
                  </div>

                  <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between fs-8" style="border-color: #f0e6d6 !important;">
                    <span class="badge rounded-pill px-2 py-1 fw-bold text-truncate" style="background: #f4eae0; color: #6e4727; max-width: 85px; font-size: 0.62rem;">
                      <?= esc($rel['category'] ?: 'Umum'); ?>
                    </span>
                    <span class="badge rounded-pill px-2 py-1 fw-extrabold" style="background: #fff8eb; border: 1px solid #f3e5c8; color: #b48316; font-size: 0.62rem;">
                      <i class="ti ti-columns me-1" style="color: #c59b27;"></i>Rak <?= esc($rel['rack'] ?: '-'); ?>
                    </span>
                  </div>

                </div>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

</div>

<?= $this->endSection() ?>
