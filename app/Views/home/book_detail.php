<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title><?= esc($book['title']); ?> - Perpustakaan Assalafiyyah Mlangi</title>
<meta name="description" content="<?= esc(mb_strimwidth(strip_tags($book['synopsis'] ?? $book['title']), 0, 160, '...')); ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper(['upload_helper']); ?>

<div class="container py-4">

  <!-- UNIDA Style Breadcrumb Navigation -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white px-3.5 py-2.5 rounded-pill shadow-xs border" style="border: 1.5px solid #e2d5c3 !important;">
      <li class="breadcrumb-item"><a href="<?= base_url(); ?>" class="text-decoration-none fw-bold" style="color: #6e4727;"><i class="ti ti-home me-1"></i>Beranda</a></li>
      <li class="breadcrumb-item"><a href="<?= base_url('book'); ?>" class="text-decoration-none fw-bold" style="color: #6e4727;"><i class="ti ti-books me-1"></i>Katalog Buku</a></li>
      <li class="breadcrumb-item active fw-semibold text-truncate" style="color: #8b5e3c; max-width: 280px;" aria-current="page"><?= esc($book['title']); ?></li>
    </ol>
  </nav>

  <?php
    $rawCover = $book['book_cover'] ?? '';
    $coverUrl = getBookCoverUrl($rawCover);
    $hasCover = !empty($rawCover) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
    $stockCount = (int)($book['quantity'] ?? 0);
  ?>

  <!-- Main Book Detail Card UNIDA Style -->
  <div class="card border-0 shadow-sm rounded-5 overflow-hidden mb-4" style="background: rgba(255, 255, 255, 0.95); border: 1.5px solid #e2d5c3 !important; backdrop-filter: blur(12px); box-shadow: 0 10px 30px rgba(89, 57, 31, 0.08) !important;">
    <div class="card-body p-4 p-md-5">
      <div class="row g-4 align-items-start">
        
        <!-- Book Cover Column -->
        <div class="col-12 col-md-4 col-lg-3 text-center">
          <div class="position-relative d-flex align-items-center justify-content-center overflow-hidden p-3 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #faf5ee 0%, #eee4d5 100%); min-height: 320px; border: 1.5px solid #e8decb;">
            <?php if ($hasCover): ?>
              <img src="<?= $coverUrl; ?>" alt="<?= esc($book['title']); ?>" class="img-fluid rounded-3 shadow-md" style="max-height: 360px; object-fit: contain; filter: drop-shadow(0 8px 16px rgba(89, 57, 31, 0.25));">
            <?php else: ?>
              <div class="d-flex flex-column align-items-center justify-content-center text-center p-4 w-100 h-100 rounded-3" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff; min-height: 280px;">
                <i class="ti ti-book fs-1 mb-3" style="color: #f0c968; font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-white mb-0" style="font-family: 'Georgia', serif;"><?= esc($book['title']); ?></h5>
              </div>
            <?php endif; ?>
          </div>

          <!-- Availability Status Badge -->
          <div class="mt-3">
            <?php if ($stockCount > 0): ?>
              <div class="alert border-0 rounded-4 py-2.5 px-3 mb-0 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #e6f4ea; color: #137333; border: 1px solid #ceead6 !important;">
                <i class="ti ti-circle-check fs-5"></i>
                <span class="fw-bold fs-7">Tersedia di Perpustakaan (<?= $stockCount; ?> Eksemplar)</span>
              </div>
            <?php else: ?>
              <div class="alert border-0 rounded-4 py-2.5 px-3 mb-0 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background-color: #fce8e6; color: #c5221f; border: 1px solid #fad2cf !important;">
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
            <span class="badge px-3 py-1.5 rounded-pill fw-extrabold text-dark shadow-sm mb-2" style="background: linear-gradient(135deg, #c59b27 0%, #d4af37 100%); font-size: 0.75rem; letter-spacing: 0.5px;">
              <i class="ti ti-bookmark me-1"></i> <?= esc($book['category'] ?: 'Umum'); ?>
            </span>
            <h1 class="fw-extrabold mb-2" style="color: #2d1e18 !important; font-family: 'Georgia', serif; font-size: 2.1rem; line-height: 1.3;">
              <?= esc($book['title']); ?>
            </h1>
            <div class="d-flex flex-wrap align-items-center gap-2 gap-sm-3 fs-7" style="color: #8b5e3c;">
              <span><i class="ti ti-user me-1" style="color: #c59b27;"></i> Pengarang: <strong style="color: #2d1e18;"><?= esc($book['author'] ?: 'Tak Diketahui'); ?></strong></span>
              <span class="d-none d-sm-inline">•</span>
              <span><i class="ti ti-building me-1" style="color: #c59b27;"></i> Penerbit: <strong style="color: #2d1e18;"><?= esc($book['publisher'] ?: 'Tak Diketahui'); ?></strong></span>
            </div>
          </div>

          <hr style="border-color: #e2d5c3; margin: 1.25rem 0;">

          <!-- Key Metadata Grid UNIDA Theme (4 Cards) -->
          <div class="row g-3 gy-3 mb-4">
            <!-- NOMOR PANGGIL / CALL NUMBER -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-4 h-100 border transition-all shadow-xs" style="background: linear-gradient(135deg, #fdfbf7 0%, #f4eae0 100%); border-color: #e2d5c3 !important;">
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #6e4727; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-tag fs-5" style="color: #c59b27;"></i> Nomor Panggil
                  </span>
                  <?php if (!empty($book['ddc'])): ?>
                    <span class="badge px-1.5 py-0.5 rounded-2" style="background: #e8decb; color: #6e4727; font-size: 0.62rem;">DDC: <?= esc($book['ddc']); ?></span>
                  <?php endif; ?>
                </div>
                <div class="fw-extrabold ps-1 text-truncate" style="color: #2d1e18; font-size: 0.9rem; font-family: 'Courier New', monospace; letter-spacing: 0.5px;">
                  <?= esc($book['call_number'] ?: ($book['ddc'] ?: 'Belum diatur')); ?>
                </div>
              </div>
            </div>

            <!-- LOKASI RAK & LANTAI -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-4 h-100 border transition-all shadow-xs" style="background: linear-gradient(135deg, #fdfbf7 0%, #f4eae0 100%); border-color: #e2d5c3 !important;">
                <div class="mb-2">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #6e4727; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-columns fs-5" style="color: #c59b27;"></i> Lokasi Rak & Lantai
                  </span>
                </div>
                <div class="fw-extrabold text-truncate ps-1" style="color: #2d1e18; font-size: 0.88rem;">
                  Rak <?= esc($book['rack'] ?: '-'); ?> <?= esc($book['floor'] ? '(Lantai ' . $book['floor'] . ')' : ''); ?>
                </div>
              </div>
            </div>

            <!-- ISBN -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-4 h-100 border transition-all shadow-xs" style="background: linear-gradient(135deg, #fdfbf7 0%, #f4eae0 100%); border-color: #e2d5c3 !important;">
                <div class="mb-2">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #6e4727; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-barcode fs-5" style="color: #c59b27;"></i> ISBN
                  </span>
                </div>
                <div class="fw-extrabold text-truncate ps-1" style="color: #2d1e18; font-size: 0.88rem; font-family: 'Courier New', monospace;"><?= esc($book['isbn'] ?: '-'); ?></div>
              </div>
            </div>

            <!-- TAHUN TERBIT -->
            <div class="col-6 col-md-6 col-lg-3">
              <div class="p-3 rounded-4 h-100 border transition-all shadow-xs" style="background: linear-gradient(135deg, #fdfbf7 0%, #f4eae0 100%); border-color: #e2d5c3 !important;">
                <div class="mb-2">
                  <span class="fw-bold d-inline-flex align-items-center gap-1.5" style="color: #6e4727; font-size: 0.72rem; letter-spacing: 0.4px; text-transform: uppercase;">
                    <i class="ti ti-calendar fs-5" style="color: #c59b27;"></i> Tahun Terbit
                  </span>
                </div>
                <div class="fw-extrabold text-truncate ps-1" style="color: #2d1e18; font-size: 0.88rem;"><?= esc($book['year'] ?: '-'); ?></div>
              </div>
            </div>
          </div>

          <!-- Synopsis / Blurb Section Card -->
          <div class="card border-0 rounded-4 shadow-none p-4" style="background: #ffffff; border: 1.5px solid #e2d5c3 !important;">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: #e2d5c3 !important;">
              <h5 class="fw-extrabold mb-0 d-flex align-items-center gap-2" style="color: #2d1e18; font-family: 'Georgia', serif;">
                <i class="ti ti-feather fs-4" style="color: #c59b27 !important;"></i> Sinopsis / Blurb Buku
              </h5>
              <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #fdf6ea; color: #6e4727 !important; border: 1px solid #f3e5c8; font-size: 0.675rem; letter-spacing: 0.4px;">Ringkasan Pustaka</span>
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

  <!-- Related Books Section (UNIDA Hover Cards Slider) -->
  <?php if (!empty($relatedBooks)): ?>
    <div class="mt-5">
      <h4 class="fw-extrabold mb-3 d-flex align-items-center gap-2" style="color: #2d1e18; font-family: 'Georgia', serif;">
        <i class="ti ti-books fs-4" style="color: #c59b27 !important;"></i> Koleksi Buku Lainnya dalam Kategori Ini
      </h4>
      
      <div class="d-flex overflow-x-auto gap-3 pb-3 pt-1 px-1 custom-horizontal-scroll" style="-webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory; scrollbar-width: thin; scrollbar-color: #c59b27 #f8f2e6;">
        <?php foreach ($relatedBooks as $rel): ?>
          <?php
            $rawRCover = $rel['book_cover'] ?? '';
            $rCover = getBookCoverUrl($rawRCover);
            $hasRCover = !empty($rawRCover) && ($rCover !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
          ?>
          <div style="flex: 0 0 175px; min-width: 175px; max-width: 175px; scroll-snap-align: start;">
            <a href="<?= base_url('book/' . ($rel['slug'] ?: $rel['id'])); ?>" class="text-decoration-none d-block">
              <div class="unida-cover-hover-card" style="height: 230px;">
                
                <!-- Clean Cover Image Display -->
                <?php if ($hasRCover): ?>
                  <img src="<?= $rCover; ?>" alt="<?= esc($rel['title']); ?>" loading="lazy" class="unida-cover-img">
                <?php else: ?>
                  <div class="d-flex flex-column align-items-center justify-content-center text-center p-3 h-100 w-100" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff;">
                    <i class="ti ti-book fs-1 mb-2" style="color: #f0c968;"></i>
                    <span class="fw-bold fs-7 text-white text-truncate-3 px-1" style="line-height: 1.25; font-family: 'Georgia', serif;"><?= esc($rel['title']); ?></span>
                  </div>
                <?php endif; ?>

                <!-- Hover Title & Details Overlay -->
                <div class="unida-cover-overlay text-white">
                  <div class="mb-1">
                    <span class="badge rounded-pill text-truncate fw-bold shadow-sm" style="background: #c59b27; color: #2d1e18; font-size: 0.61rem; padding: 3px 8px; max-width: 95%;">
                      <i class="ti ti-bookmark me-0.5"></i><?= esc($rel['category'] ?: 'Umum'); ?>
                    </span>
                  </div>
                  <h6 class="fw-bold text-white mb-1 text-truncate-2" title="<?= esc($rel['title']); ?>" style="font-size: 0.84rem; line-height: 1.25; font-family: 'Georgia', serif;">
                    <?= esc($rel['title']); ?>
                  </h6>
                  <div class="d-flex align-items-center justify-content-between gap-1 mt-1 pt-1.5 border-top" style="border-color: rgba(255, 255, 255, 0.2) !important;">
                    <span class="fw-semibold text-truncate" style="color: #f3d382; font-size: 0.71rem;">
                      <i class="ti ti-user me-0.5" style="color: #c59b27;"></i><?= esc($rel['author'] ?: 'Penulis tak diketahui'); ?>
                    </span>
                    <span class="badge rounded-pill px-2 py-0.5 fw-extrabold flex-shrink-0" style="background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.6rem;">
                      <i class="ti ti-columns me-0.5" style="color: #f3d382;"></i>Rak <?= esc($rel['rack'] ?: '-'); ?>
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
