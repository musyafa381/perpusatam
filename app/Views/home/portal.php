<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Perpustakaan Pusat - Pondok Pesantren Assalafiyyah Mlangi</title>
<meta name="description" content="Portal Resmi Perpustakaan Pusat Pondok Pesantren Assalafiyyah Mlangi. Pencarian Katalog Buku, Repositori Pustaka, Buku Tamu, dan Keanggotaan.">
<style>
  /* UNIDA Gontor Hero Banner with Library Theme Colors */
  .unida-hero-banner {
    background: linear-gradient(135deg, #59391f 0%, #6e4727 35%, #8b5e3c 65%, #c59b27 100%);
    position: relative;
    padding: 60px 0 80px 0;
    color: #ffffff;
    overflow: hidden;
  }
  .unida-hero-banner::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
  }

  /* Mega Search Bar UNIDA Style */
  .unida-search-wrapper {
    background: #ffffff;
    border-radius: 50px;
    padding: 6px 8px 6px 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    display: flex;
    align-items: center;
    max-width: 720px;
    margin: 0 auto;
  }
  .unida-search-input {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    font-size: 1.05rem;
    color: #2d1e18;
    background: transparent;
    padding: 10px 12px;
    width: 100%;
  }
  .unida-search-btn {
    background: linear-gradient(135deg, #c59b27 0%, #d4af37 100%) !important;
    color: #2d1e18 !important;
    font-weight: 800 !important;
    border-radius: 50px !important;
    padding: 12px 28px !important;
    font-size: 1rem !important;
    border: none !important;
    box-shadow: 0 4px 12px rgba(197, 155, 39, 0.4);
    transition: all 0.25s ease;
    white-space: nowrap;
  }
  .unida-search-btn:hover {
    background: linear-gradient(135deg, #b8860b 0%, #c59b27 100%) !important;
    color: #ffffff !important;
    transform: scale(1.02);
  }

  /* UNIDA Gontor Category Pill Badges with Count */
  .unida-cat-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 50px;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 700;
    transition: all 0.25s ease;
    white-space: nowrap;
  }

  /* Active State (White Capsule Pill) */
  .unida-cat-pill.active {
    background: #ffffff;
    color: #59391f !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
  }
  .unida-cat-pill.active .unida-cat-count-badge {
    background: #f0e6d6;
    color: #6e4727;
  }

  /* Inactive Translucent State */
  .unida-cat-pill.inactive {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
  }
  .unida-cat-pill.inactive:hover {
    background: rgba(255, 255, 255, 0.28);
    border-color: rgba(255, 255, 255, 0.4);
  }
  .unida-cat-pill.inactive .unida-cat-count-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
  }

  .unida-cat-count-badge {
    padding: 2px 9px;
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 800;
  }

  /* Floating Stat Cards Overlapping Hero Bottom (UNIDA Style) */
  .unida-stats-container {
    margin-top: -45px;
    position: relative;
    z-index: 10;
  }
  .unida-stat-card {
    background: #ffffff;
    border: 1.5px solid #e2d5c3;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 8px 24px rgba(110, 71, 39, 0.08);
    transition: all 0.25s ease;
  }
  .unida-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(110, 71, 39, 0.15);
    border-color: #c59b27;
  }
  .unida-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
  }

  /* Modal Backdrop Dimmed & Blurred Overlay (Makes background behind modal dark & non-interactive) */
  .modal-backdrop.show {
    background-color: rgba(28, 16, 8, 0.68) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    opacity: 1 !important;
  }

  /* Smooth Modal Scale & Fade Animation */
  .modal.fade .modal-dialog {
    transform: scale(0.9) translateY(25px);
    transition: transform 0.38s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.35s ease-in-out;
    opacity: 0;
  }
  .modal.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
  }

  /* Ultra-Beautiful Themed Modal Card Styling */
  .unida-themed-modal {
    border-radius: 24px !important;
    border: 1.5px solid #e2d5c3 !important;
    box-shadow: 0 20px 50px rgba(45, 26, 12, 0.25) !important;
    overflow: hidden;
    background: #ffffff;
  }
  .unida-themed-modal-header {
    background: linear-gradient(135deg, #faf6f0 0%, #f4eae0 100%);
    border-bottom: 1.5px solid #e2d5c3 !important;
    padding: 16px 22px;
  }
  .unida-themed-modal-title {
    font-family: 'Georgia', serif;
    color: #3d230e;
    font-size: 1.2rem;
    font-weight: 800;
  }
  .unida-modal-close-btn {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(89, 57, 31, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: none;
    color: #59391f;
    transition: all 0.25s ease;
    cursor: pointer;
  }
  .unida-modal-close-btn:hover {
    background: #c59b27;
    color: #ffffff;
    transform: rotate(90deg) scale(1.08);
  }

  /* Quick Services Cards Stack (Right Column UNIDA Style) */
  .unida-service-btn-card {
    border-radius: 16px;
    padding: 18px 20px;
    color: #ffffff;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.25s ease;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
  }
  .unida-service-btn-card:hover {
    transform: translateX(4px);
    color: #ffffff;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.15);
  }

  .catalog-book-card {
    background: #ffffff;
    border: 1.5px solid #e2d5c3 !important;
    transition: all 0.25s ease;
  }
  /* Explore Collection Book Cover Cards (UNIDA Gontor Style) */
  .explore-cover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    border: 1.5px solid rgba(226, 213, 195, 0.6) !important;
  }
  .explore-cover-card:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 16px 32px rgba(89, 57, 31, 0.22) !important;
    border-color: #c59b27 !important;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper(['upload_helper']); ?>

<!-- 1. Hero Banner UNIDA Gontor Style (Warm Brown/Gold Theme) -->
<section class="unida-hero-banner text-center">
  <div class="container px-3">
    
    <h1 class="fw-extrabold text-white mb-2" style="font-family: 'Georgia', serif; font-size: 2.5rem; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);">
      Perpustakaan Assalafiyyah Mlangi
    </h1>
    <p class="text-white-50 mx-auto mb-4" style="max-width: 650px; font-size: 1rem; line-height: 1.5;">
      Pondok Pesantren Assalafiyyah Mlangi Sleman Yogyakarta
    </p>

    <!-- Mega Search Bar Form (Submits to Catalog Page) -->
    <form action="<?= base_url('book'); ?>" method="get" class="mb-3">
      <div class="unida-search-wrapper">
        <i class="ti ti-search fs-5 text-muted me-2 ms-1"></i>
        <input type="text" name="search" class="form-control unida-search-input" value="<?= esc($search ?? ''); ?>" placeholder="Cari buku, e-books, pengarang, penerbit, nomor ISBN..." aria-label="Cari Pustaka">
        <button class="btn unida-search-btn" type="submit">
          <i class="ti ti-search me-1"></i> Cari Pustaka
        </button>
      </div>
    </form>

    <!-- UNIDA Gontor Category Pills with Book Counts (Top 7) -->
    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 fs-7" style="color: rgba(255, 255, 255, 0.8);">
      
      <!-- 'Semua' Category Pill -->
      <a href="<?= base_url('book'); ?>" class="unida-cat-pill <?= empty($categoryFilter) ? 'active' : 'inactive'; ?>">
        <i class="ti ti-books"></i>
        <span>Semua</span>
        <span class="unida-cat-count-badge"><?= $totalBooksCount; ?></span>
      </a>

      <!-- Top 7 Category Pills with Count Badges -->
      <?php foreach ($categories as $cat) : ?>
        <a href="<?= base_url('book?category=' . $cat['id']); ?>" class="unida-cat-pill <?= (string)$categoryFilter === (string)$cat['id'] ? 'active' : 'inactive'; ?>">
          <span><?= esc($cat['name']); ?></span>
          <span class="unida-cat-count-badge"><?= (int)($cat['total_books'] ?? 0); ?></span>
        </a>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- 2. Floating Stat Cards Row (Overlapping Banner Bottom) -->
<div class="container px-3 unida-stats-container mb-5">
  <div class="row g-3">
    
    <div class="col-6 col-md-3 col-lg-2.4 col-xl" style="flex: 1;">
      <div class="unida-stat-card d-flex align-items-center gap-3">
        <div class="unida-stat-icon" style="background: #fdf6ea; color: #6e4727;">
          <i class="ti ti-books"></i>
        </div>
        <div class="text-truncate">
          <h4 class="fw-extrabold mb-0 text-dark"><?= number_format($totalBooksCount, 0, ',', '.'); ?></h4>
          <small class="text-muted fw-bold fs-1 text-uppercase">Buku Koleksi</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3 col-lg-2.4 col-xl" style="flex: 1;">
      <div class="unida-stat-card d-flex align-items-center gap-3">
        <div class="unida-stat-icon" style="background: #fff8eb; color: #b48316;">
          <i class="ti ti-copy"></i>
        </div>
        <div class="text-truncate">
          <h4 class="fw-extrabold mb-0 text-dark"><?= number_format($totalCopiesCount ?? $totalBooksCount, 0, ',', '.'); ?></h4>
          <small class="text-muted fw-bold fs-1 text-uppercase">Eksemplar Buku</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3 col-lg-2.4 col-xl" style="flex: 1;">
      <div class="unida-stat-card d-flex align-items-center gap-3">
        <div class="unida-stat-icon" style="background: #fff8eb; color: #b48316;">
          <i class="ti ti-users"></i>
        </div>
        <div class="text-truncate">
          <h4 class="fw-extrabold mb-0 text-dark"><?= number_format($totalMembersCount, 0, ',', '.'); ?></h4>
          <small class="text-muted fw-bold fs-1 text-uppercase">Anggota Aktif</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3 col-lg-2.4 col-xl" style="flex: 1;">
      <div class="unida-stat-card d-flex align-items-center gap-3">
        <div class="unida-stat-icon" style="background: #fff8eb; color: #b48316;">
          <i class="ti ti-id-badge-2"></i>
        </div>
        <div class="text-truncate">
          <h4 class="fw-extrabold mb-0 text-dark"><?= number_format($totalVisitorsCount, 0, ',', '.'); ?></h4>
          <small class="text-muted fw-bold fs-1 text-uppercase">Kunjungan</small>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3 col-lg-2.4 col-xl d-none d-md-block" style="flex: 1;">
      <div class="unida-stat-card d-flex align-items-center gap-3">
        <div class="unida-stat-icon" style="background: #fff8eb; color: #b48316;">
          <i class="ti ti-bookmarks"></i>
        </div>
        <div class="text-truncate">
          <h4 class="fw-extrabold mb-0 text-dark"><?= number_format($totalLoansCount, 0, ',', '.'); ?></h4>
          <small class="text-muted fw-bold fs-1 text-uppercase">Sirkulasi Pinjam</small>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- 3. Main Split Section Container: Welcome Card (Left) & Quick Services (Right) -->
<div class="container px-3 mb-5">
  <div class="row g-4 align-items-stretch">
    
    <!-- Left Column (8 Columns): Welcome Card UNIDA Style -->
    <div class="col-12 col-lg-8">
      <div class="card border-0 rounded-4 p-4 h-100 shadow-sm d-flex flex-column justify-content-between" style="background: #ffffff; border: 1.5px solid #e2d5c3 !important;">
        <div>
          <div class="d-flex align-items-start gap-3 mb-3">
            <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan" class="rounded-3 flex-shrink-0" style="width: 58px; height: 58px; object-fit: cover; border: 1.5px solid #c59b27;">
            <div>
              <h4 class="fw-extrabold mb-1" style="color: #2d1e18; font-family: 'Georgia', serif; font-size: 1.45rem;">
                Welcome to Assalafiyyah Library
              </h4>
              <small class="text-muted fw-semibold d-block">Pondok Pesantren Assalafiyyah Mlangi Sleman Yogyakarta</small>
            </div>
          </div>

          <p class="text-muted fs-7 mb-4" style="line-height: 1.65;">
            Perpustakaan Pusat Assalafiyyah Mlangi merupakan pusat keilmuan dan pembelajaran modern santri. Kami menyediakan akses ke ribuan koleksi buku, kitab kuning, novel, serta repositori digital untuk mendukung riset dan literasi keilmuan pesantren.
          </p>
        </div>

        <!-- 3 Action Pills (Themed Warm Brown & Gold) -->
        <div class="d-flex align-items-center flex-wrap gap-2 mt-auto pt-2">
          <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalVisiMisi" style="background: #fdf6ea; color: #6e4727; border: 1.5px solid #f3e5c8; font-size: 0.78rem;">
            <i class="ti ti-target-arrow" style="color: #c59b27 !important;"></i> Vision & Mission
          </button>
          <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalJamLayanan" style="background: #fff8eb; color: #b48316; border: 1.5px solid #f9e2b0; font-size: 0.78rem;">
            <i class="ti ti-clock" style="color: #b48316 !important;"></i> Service Hours
          </button>
          <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalFasilitas" style="background: #f4eae0; color: #59391f; border: 1.5px solid #e2d5c3; font-size: 0.78rem;">
            <i class="ti ti-building" style="color: #59391f !important;"></i> Facilities
          </button>
        </div>
      </div>
    </div>

    <!-- Right Column (4 Columns): Quick Services Stack -->
    <div class="col-12 col-lg-4">
      <div class="d-flex flex-column gap-3 h-100 justify-content-between">
        
        <!-- Card 1: Katalog Pustaka (Warm Brown Gradient) -->
        <a href="<?= base_url('book'); ?>" class="unida-service-btn-card flex-fill" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); border-radius: 18px; border: 1px solid rgba(255,255,255,0.15);">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.18) !important; color: #f0c968 !important;">
              <i class="ti ti-books fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Katalog Pustaka</h6>
              <small style="color: rgba(255, 255, 255, 0.8); font-size: 0.725rem;">Cari & Jelajahi Koleksi Pustaka</small>
            </div>
          </div>
          <i class="ti ti-chevron-right fs-5 text-white-50"></i>
        </a>

        <!-- Card 2: Buku Tamu Digital (Golden Amber Gradient) -->
        <a href="<?= base_url('buku-tamu'); ?>" class="unida-service-btn-card flex-fill" style="background: linear-gradient(135deg, #c59b27 0%, #d4af37 100%); border-radius: 18px; border: 1px solid rgba(255,255,255,0.2);">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: rgba(45, 30, 24, 0.25) !important; color: #ffffff !important;">
              <i class="ti ti-id-badge-2 fs-4"></i>
            </div>
            <div>
              <h6 class="fw-extrabold mb-0" style="color: #2d1e18; font-size: 0.95rem;">Buku Tamu Digital</h6>
              <small style="color: rgba(45, 30, 24, 0.85); font-size: 0.725rem; font-weight: 600;">Presensi Kunjungan Santri & Tamu</small>
            </div>
          </div>
          <i class="ti ti-chevron-right fs-5" style="color: rgba(45, 30, 24, 0.6);"></i>
        </a>

        <!-- Card 3: Display TV Monitoring (Espresso Dark Brown Gradient) -->
        <a href="<?= base_url('tv'); ?>" target="_blank" class="unida-service-btn-card flex-fill" style="background: linear-gradient(135deg, #3d230e 0%, #59391f 100%); border-radius: 18px; border: 1px solid rgba(255,255,255,0.15);">
          <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.18) !important; color: #f0c968 !important;">
              <i class="ti ti-device-tv fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-0 text-white" style="font-size: 0.95rem;">Display TV Monitoring</h6>
              <small style="color: rgba(255, 255, 255, 0.8); font-size: 0.725rem;">Tampilan Layar Monitor Realtime</small>
            </div>
          </div>
          <i class="ti ti-chevron-right fs-5 text-white-50"></i>
        </a>

      </div>
    </div>

  </div>
</div>

<!-- 4. Explore Collection Section Container (With Warm Background Container Box) -->
<div class="container px-3 mb-5">
  <div class="p-4 p-md-5 rounded-5 shadow-sm" style="background: linear-gradient(135deg, #fcf8f2 0%, #f4eae0 100%); border: 1.5px solid #e2d5c3;">
    
    <!-- Explore Collection Header & 4 Category Capsules -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
      <div>
        <h3 class="fw-extrabold mb-1" style="color: #2d1e18; font-family: 'Georgia', serif; font-size: 1.65rem;">
          Explore Collection
        </h3>
        <p class="text-muted mb-0 fs-7">Koleksi pustaka terbaru yang baru saja terdaftar di perpustakaan</p>
      </div>

      <!-- 4 Category Filter Capsules (Semua Buku + 3 Teratas) -->
      <div class="d-flex align-items-center flex-wrap gap-2">
        <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-bold shadow-xs explore-cat-btn active" data-cat-id="all" style="background: #59391f; color: #ffffff; border: 1.5px solid #59391f; font-size: 0.8rem;">
          <i class="ti ti-books me-1"></i> Semua Buku
        </button>
        <?php foreach (array_slice($categories, 0, 3) as $cItem) : ?>
          <button type="button" class="btn btn-sm rounded-pill px-3 py-2 fw-bold shadow-xs explore-cat-btn" data-cat-id="<?= $cItem['id']; ?>" style="background: #ffffff; color: #6e4727; border: 1.5px solid #e8decb; font-size: 0.8rem;">
            <i class="ti ti-bookmark me-1" style="color: #c59b27;"></i> <?= esc($cItem['name']); ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Books Showcase Grid (Max 6 Items Per Active Category Tab) -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 mb-4" id="exploreBooksGrid">
      <?php foreach ($latestBooks as $index => $b) : ?>
        <?php
          $rawCover = $b['book_cover'] ?? '';
          $coverUrl = getBookCoverUrl($rawCover);
          $hasCover = !empty($rawCover) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
          $catId = (int)($b['category_id'] ?? 0);
        ?>
        <div class="col explore-book-item" data-cat-id="<?= $catId; ?>" style="<?= $index < 6 ? '' : 'display: none;'; ?>">
          <a href="<?= base_url('book/' . ($b['slug'] ?: $b['id'])); ?>" class="d-block text-decoration-none h-100">
            <div class="card h-100 border-0 rounded-4 overflow-hidden explore-cover-card position-relative shadow-sm" style="background: #ffffff;">
              
              <!-- Book Cover Image Box (Compact Height) -->
              <div class="position-relative overflow-hidden d-flex align-items-center justify-content-center p-2" style="height: 160px; background: linear-gradient(135deg, #faf5ee 0%, #eee4d5 100%);">
                <?php if ($hasCover) : ?>
                  <img src="<?= $coverUrl; ?>" alt="<?= esc($b['title']); ?>" loading="lazy" class="h-100 w-auto shadow-sm rounded-2" style="object-fit: contain; max-width: 100%; max-height: 100%; filter: drop-shadow(0 4px 10px rgba(89, 57, 31, 0.2));">
                <?php else : ?>
                  <div class="d-flex flex-column align-items-center justify-content-center text-center p-2 h-100 w-100 rounded-3" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff;">
                    <i class="ti ti-book fs-2 mb-1" style="color: #f0c968;"></i>
                    <span class="fw-bold fs-8 text-white text-truncate-2 px-1" style="line-height: 1.2; font-family: 'Georgia', serif;"><?= esc($b['title']); ?></span>
                  </div>
                <?php endif; ?>
                
                <!-- BARU Gold Pill Badge -->
                <span class="position-absolute top-0 start-0 m-1.5 badge rounded-pill fw-bold" style="background: #c59b27; color: #ffffff; font-size: 0.58rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                  BARU
                </span>
              </div>

              <!-- Title & Author Tight Footer ("Rengkat" & Neat) -->
              <div class="p-2 text-center" style="background: #ffffff;">
                <h6 class="fw-bold text-truncate mb-1" title="<?= esc($b['title']); ?>" style="color: #2d1e18 !important; font-size: 0.82rem; font-family: 'Georgia', serif;">
                  <?= esc($b['title']); ?>
                </h6>
                <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                  <small class="text-truncate fw-semibold" style="color: #8b5e3c !important; font-size: 0.72rem;">
                    <i class="ti ti-user me-0.5" style="color: #c59b27;"></i><?= esc($b['author'] ?: 'Penulis tak diketahui'); ?>
                  </small>
                </div>
                <span class="badge rounded-pill px-2 py-0.5 fw-bold text-truncate" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8; font-size: 0.6rem;">
                  <?= esc($b['category'] ?: 'Umum'); ?>
                </span>
              </div>

            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Empty Message if Selected Category Has No Books -->
    <div id="exploreEmptyMsg" class="text-center py-4 text-muted" style="display: none;">
      <i class="ti ti-folder-off fs-2 mb-2 d-block" style="color: #c59b27;"></i>
      <span class="fw-bold fs-7">Belum ada buku untuk kategori ini</span>
    </div>

    <!-- Center Action Button: View All Books -->
    <div class="text-center">
      <a href="<?= base_url('book'); ?>" class="btn rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff; border: none; font-size: 0.9rem;">
        View All Books <i class="ti ti-arrow-right ms-1"></i>
      </a>
    </div>

  </div>
</div>

<!-- 5. Informasi Perpustakaan Showcase Container (Warm Cream Theme, 2 Cards Side-by-Side) -->
<div class="container px-3 mb-5">
  <div class="p-4 p-md-5 rounded-5 shadow-sm overflow-hidden position-relative" style="background: linear-gradient(135deg, #fcf8f2 0%, #f4eae0 100%); border: 1.5px solid #e2d5c3;">
    
    <!-- Section Header (Without Buka Layar TV Button) -->
    <div class="mb-4 border-bottom pb-3" style="border-color: #e2d5c3 !important;">
      <h3 class="fw-extrabold mb-1" style="color: #2d1e18; font-family: 'Georgia', serif; font-size: 1.65rem;">
        Informasi Perpustakaan
      </h3>
      <p class="text-muted mb-0 fs-7">Konten pengumuman, banner literasi & sirkulasi digital</p>
    </div>

    <!-- Banners Showcase (2 Cards Side-by-Side Per Slide) -->
    <?php
      $displayBanners = !empty($tvBanners) ? $tvBanners : [];
      $chunks = !empty($displayBanners) ? array_chunk($displayBanners, 2) : [];
    ?>

    <?php if (!empty($chunks)) : ?>
      <?php if (count($displayBanners) <= 2) : ?>
        <!-- Static 2 Cards Side-by-Side Row (For 2 or fewer banners) -->
        <div class="row row-cols-1 row-cols-md-2 g-4">
          <?php foreach ($displayBanners as $tb) : ?>
            <div class="col">
              <div class="rounded-4 overflow-hidden shadow-sm position-relative border" style="border: 1.5px solid #e2d5c3 !important; background: #ffffff;">
                <img src="<?= esc($tb['url']); ?>" alt="<?= esc($tb['title'] ?? 'Banner'); ?>" class="w-100 h-auto d-block rounded-4" style="object-fit: cover;">
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <!-- Sliding Carousel for More Than 2 Banners (2 Cards Per Slide) -->
        <div id="tvMultiSlideCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
          
          <div class="carousel-inner pb-2">
            <?php foreach ($chunks as $cIdx => $chunk) : ?>
              <div class="carousel-item <?= $cIdx === 0 ? 'active' : ''; ?>">
                <div class="row row-cols-1 row-cols-md-2 g-4">
                  <?php foreach ($chunk as $tb) : ?>
                    <div class="col">
                      <div class="rounded-4 overflow-hidden shadow-sm position-relative border" style="border: 1.5px solid #e2d5c3 !important; background: #ffffff;">
                        <img src="<?= esc($tb['url']); ?>" alt="<?= esc($tb['title'] ?? 'Banner'); ?>" class="w-100 h-auto d-block rounded-4" style="object-fit: cover;">
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Carousel Controls & Indicators -->
          <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
            <button class="btn btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center shadow-xs" type="button" data-bs-target="#tvMultiSlideCarousel" data-bs-slide="prev" style="width: 38px; height: 38px; background: #ffffff; color: #6e4727; border: 1.5px solid #e8decb;">
              <i class="ti ti-chevron-left fs-5"></i>
            </button>

            <div class="carousel-indicators position-static m-0 d-flex align-items-center gap-1.5">
              <?php foreach ($chunks as $cIdx => $chunk) : ?>
                <button type="button" data-bs-target="#tvMultiSlideCarousel" data-bs-slide-to="<?= $cIdx; ?>" class="<?= $cIdx === 0 ? 'active' : ''; ?>" aria-current="<?= $cIdx === 0 ? 'true' : 'false'; ?>" aria-label="Slide <?= $cIdx + 1; ?>" style="width: 24px; height: 6px; border-radius: 4px; background-color: #59391f; border: none;"></button>
              <?php endforeach; ?>
            </div>

            <button class="btn btn-sm rounded-circle p-2 d-inline-flex align-items-center justify-content-center shadow-xs" type="button" data-bs-target="#tvMultiSlideCarousel" data-bs-slide="next" style="width: 38px; height: 38px; background: #ffffff; color: #6e4727; border: 1.5px solid #e8decb;">
              <i class="ti ti-chevron-right fs-5"></i>
            </button>
          </div>

        </div>
      <?php endif; ?>

    <?php else : ?>
      <!-- 2 Default Side-by-Side Showcase Cards (When no banners uploaded) -->
      <div class="row row-cols-1 row-cols-md-2 g-4">
        
        <!-- Card 1: Member Card & Tiering -->
        <div class="col">
          <div class="card h-100 border-0 rounded-4 overflow-hidden p-4 shadow-sm d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #ffffff 0%, #faf6f0 100%); border: 1.5px solid #e2d5c3 !important;">
            <div>
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #59391f; color: #ffffff; font-size: 0.72rem;">
                  MEMBER CARD GOLD
                </span>
                <i class="ti ti-id-badge-2 fs-2" style="color: #c59b27;"></i>
              </div>
              <h5 class="fw-bold mb-2" style="color: #2d1e18; font-family: 'Georgia', serif;">Fasilitas Member Premium</h5>
              <ul class="list-unstyled text-muted fs-7 mb-3" style="line-height: 1.7;">
                <li><i class="ti ti-circle-check-filled text-warning me-1.5" style="color: #c59b27 !important;"></i> Bawa novel & kitab ke asrama santri</li>
                <li><i class="ti ti-circle-check-filled text-warning me-1.5" style="color: #c59b27 !important;"></i> Waktu peminjaman hingga 10 hari</li>
                <li><i class="ti ti-circle-check-filled text-warning me-1.5" style="color: #c59b27 !important;"></i> Maksimal peminjaman 3 buku bersamaan</li>
              </ul>
            </div>
            <div class="pt-2 border-top text-center" style="border-color: #e8decb !important;">
              <span class="fw-bold fs-8" style="color: #6e4727;">Masa Berlaku Kartu 2 Tahun</span>
            </div>
          </div>
        </div>

        <!-- Card 2: Layanan & Sirkulasi Digital -->
        <div class="col">
          <div class="card h-100 border-0 rounded-4 overflow-hidden p-4 shadow-sm d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #ffffff 0%, #faf6f0 100%); border: 1.5px solid #e2d5c3 !important;">
            <div>
              <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #c59b27; color: #ffffff; font-size: 0.72rem;">
                  INFORMASI LITERASI
                </span>
                <i class="ti ti-books fs-2" style="color: #59391f;"></i>
              </div>
              <h5 class="fw-bold mb-2" style="color: #2d1e18; font-family: 'Georgia', serif;">Layanan & Sirkulasi Santri</h5>
              <p class="text-muted fs-7 mb-3" style="line-height: 1.65;">
                Fasilitas presensi digital santri, pencarian katalog online, serta layanan sirkulasi peminjaman buku dan kitab berbasis sistem terintegrasi.
              </p>
            </div>
            <div class="pt-2 border-top text-center" style="border-color: #e8decb !important;">
              <span class="fw-bold fs-8" style="color: #6e4727;">Sinkronisasi Data Realtime</span>
            </div>
          </div>
        </div>

      </div>
    <?php endif; ?>

  </div>
</div>

<!-- 6. Popular / Most Borrowed Books Showcase Container ("Buku Paling Laris Dipinjam") -->
<div class="container px-3 mb-5">
  <div class="p-4 p-md-5 rounded-5 shadow-sm" style="background: linear-gradient(135deg, #fcf8f2 0%, #f4eae0 100%); border: 1.5px solid #e2d5c3;">
    
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4 border-bottom pb-3" style="border-color: #e2d5c3 !important;">
      <div>
        <h3 class="fw-extrabold mb-1 d-flex align-items-center gap-2" style="color: #2d1e18; font-family: 'Georgia', serif; font-size: 1.65rem;">
          <i class="ti ti-flame text-danger" style="color: #d97706 !important;"></i> Buku Paling Laris Dipinjam
        </h3>
        <p class="text-muted mb-0 fs-7">Koleksi pustaka terpopuler dengan frekuensi peminjaman tertinggi oleh santri</p>
      </div>
      <span class="badge rounded-pill px-3 py-2 fw-bold shadow-xs" style="background: #59391f; color: #ffffff; font-size: 0.8rem;">
        🔥 Most Popular
      </span>
    </div>

    <!-- Books Showcase Grid (6 Items) -->
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 mb-4">
      <?php if (!empty($popularBooks)) : ?>
        <?php foreach ($popularBooks as $pb) : ?>
          <?php
            $rawCover = $pb['book_cover'] ?? '';
            $coverUrl = getBookCoverUrl($rawCover);
            $hasCover = !empty($rawCover) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
            $borrowCount = intval($pb['total_borrowed'] ?? 0);
          ?>
          <div class="col">
            <a href="<?= base_url('book/' . ($pb['slug'] ?: $pb['id'])); ?>" class="d-block text-decoration-none h-100">
              <div class="card h-100 border-0 rounded-4 overflow-hidden explore-cover-card position-relative shadow-sm" style="background: #ffffff;">
                
                <!-- Book Cover Image Box -->
                <div class="position-relative overflow-hidden d-flex align-items-center justify-content-center p-2" style="height: 160px; background: linear-gradient(135deg, #faf5ee 0%, #eee4d5 100%);">
                  <?php if ($hasCover) : ?>
                    <img src="<?= $coverUrl; ?>" alt="<?= esc($pb['title']); ?>" loading="lazy" class="h-100 w-auto shadow-sm rounded-2" style="object-fit: contain; max-width: 100%; max-height: 100%; filter: drop-shadow(0 4px 10px rgba(89, 57, 31, 0.2));">
                  <?php else : ?>
                    <div class="d-flex flex-column align-items-center justify-content-center text-center p-2 h-100 w-100 rounded-3" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff;">
                      <i class="ti ti-flame fs-2 mb-1" style="color: #f0c968;"></i>
                      <span class="fw-bold fs-8 text-white text-truncate-2 px-1" style="line-height: 1.2; font-family: 'Georgia', serif;"><?= esc($pb['title']); ?></span>
                    </div>
                  <?php endif; ?>
                  
                  <!-- Popular Fire Badge -->
                  <span class="position-absolute top-0 start-0 m-1.5 badge rounded-pill fw-bold" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; font-size: 0.58rem; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                    🔥 <?= $borrowCount; ?>x Dipinjam
                  </span>
                </div>

                <!-- Title & Author Footer -->
                <div class="p-2 text-center" style="background: #ffffff;">
                  <h6 class="fw-bold text-truncate mb-1" title="<?= esc($pb['title']); ?>" style="color: #2d1e18 !important; font-size: 0.82rem; font-family: 'Georgia', serif;">
                    <?= esc($pb['title']); ?>
                  </h6>
                  <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
                    <small class="text-truncate fw-semibold" style="color: #8b5e3c !important; font-size: 0.72rem;">
                      <i class="ti ti-user me-0.5" style="color: #c59b27;"></i><?= esc($pb['author'] ?: 'Penulis tak diketahui'); ?>
                    </small>
                  </div>
                  <span class="badge rounded-pill px-2 py-0.5 fw-bold text-truncate" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8; font-size: 0.6rem;">
                    <?= esc($pb['category'] ?: 'Umum'); ?>
                  </span>
                </div>

              </div>
            </a>
          </div>
        <?php endforeach; ?>
      <?php else : ?>
        <div class="col-12 text-center py-4 text-muted">
          <i class="ti ti-books-off fs-2 mb-2 d-block" style="color: #c59b27;"></i>
          <span class="fw-bold fs-7">Belum ada statistik sirkulasi peminjaman</span>
        </div>
      <?php endif; ?>
    </div>

    <!-- Center Action Button -->
    <div class="text-center">
      <a href="<?= base_url('book'); ?>" class="btn rounded-pill px-4 py-2.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff; border: none; font-size: 0.9rem;">
        Lihat Semua Koleksi Pustaka <i class="ti ti-arrow-right ms-1"></i>
      </a>
    </div>

  </div>
</div>

<!-- =========================================================
     MODALS UNTUK 3 ACTION PILLS (VISION, SERVICE HOURS, FACILITIES)
     ========================================================= -->
<!-- Modal 1: Vision & Mission -->
<div class="modal fade" id="modalVisiMisi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content unida-themed-modal border-0">
      
      <!-- Modal Header -->
      <div class="modal-header unida-themed-modal-header d-flex align-items-center justify-content-between px-4 py-3">
        <h5 class="modal-title unida-themed-modal-title mb-0 d-flex align-items-center">
          <i class="ti ti-target-arrow fs-3 me-2" style="color: #c59b27 !important;"></i>
          <span>Visi & Misi Perpustakaan</span>
        </h5>
        <button type="button" class="unida-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
          <i class="ti ti-x fs-5"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4">
        
        <!-- Visi Section -->
        <div class="mb-4">
          <div class="d-flex align-items-center gap-2 mb-2.5">
            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #59391f; color: #ffffff; font-size: 0.75rem;">VISI</span>
            <span class="fw-bold fs-6" style="color: #3d230e; font-family: 'Georgia', serif;">Visi Utama Perpustakaan</span>
          </div>
          <p class="mb-0 fs-7 ps-1" style="color: #4a3424 !important; line-height: 1.65; font-weight: 500;">
            Menjadi pusat keilmuan, riset, dan literasi Islam yang unggul berbasis nilai-nilai luhur pesantren serta terintegrasi dengan teknologi informasi modern.
          </p>
        </div>

        <hr style="border-color: #e2d5c3; opacity: 0.6;" class="my-3">

        <!-- Misi Section -->
        <div>
          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: #c59b27; color: #ffffff; font-size: 0.75rem;">MISI</span>
            <span class="fw-bold fs-6" style="color: #3d230e; font-family: 'Georgia', serif;">Misi Utama Perpustakaan</span>
          </div>

          <div class="d-flex flex-column gap-3 ps-1">
            <div class="d-flex align-items-start gap-2.5 fs-7">
              <span class="badge rounded-circle p-1 d-inline-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width: 22px; height: 22px; background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8; font-size: 0.7rem; font-weight: 800;">1</span>
              <span class="fw-medium" style="color: #4a3424 !important; line-height: 1.5;">Menyediakan koleksi pustaka kitab kuning, sains, dan literasi umum secara komprehensif.</span>
            </div>
            <div class="d-flex align-items-start gap-2.5 fs-7">
              <span class="badge rounded-circle p-1 d-inline-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width: 22px; height: 22px; background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8; font-size: 0.7rem; font-weight: 800;">2</span>
              <span class="fw-medium" style="color: #4a3424 !important; line-height: 1.5;">Mengembangkan sistem repositori keilmuan pesantren dan katalog digital berbasis web.</span>
            </div>
            <div class="d-flex align-items-start gap-2.5 fs-7">
              <span class="badge rounded-circle p-1 d-inline-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" style="width: 22px; height: 22px; background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8; font-size: 0.7rem; font-weight: 800;">3</span>
              <span class="fw-medium" style="color: #4a3424 !important; line-height: 1.5;">Memberikan pelayanan sirkulasi pustaka yang ramah, santun, cepat, dan terintegrasi.</span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Modal 2: Service Hours -->
<div class="modal fade" id="modalJamLayanan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content unida-themed-modal border-0">
      
      <!-- Modal Header -->
      <div class="modal-header unida-themed-modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title unida-themed-modal-title mb-0 d-flex align-items-center">
          <i class="ti ti-clock fs-3 me-2" style="color: #c59b27 !important;"></i>
          <span>Jam Operasional Layanan</span>
        </h5>
        <button type="button" class="unida-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
          <i class="ti ti-x fs-5"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4">
        
        <div class="list-group list-group-flush border-0">
          <div class="list-group-item bg-transparent d-flex align-items-center justify-content-between py-3 px-1 border-bottom">
            <span class="fw-bold text-dark d-inline-flex align-items-center gap-2" style="font-size: 0.9rem;">
              <i class="ti ti-woman fs-5" style="color: #c59b27;"></i> Santri Putri
            </span>
            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8; font-size: 0.8rem;">Sabtu – Senin</span>
          </div>

          <div class="list-group-item bg-transparent d-flex align-items-center justify-content-between py-3 px-1 border-bottom">
            <span class="fw-bold text-dark d-inline-flex align-items-center gap-2" style="font-size: 0.9rem;">
              <i class="ti ti-man fs-5" style="color: #b48316;"></i> Santri Putra
            </span>
            <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: #fff8eb; color: #b48316; border: 1px solid #f9e2b0; font-size: 0.8rem;">Selasa – Kamis</span>
          </div>

          <div class="list-group-item bg-transparent d-flex align-items-center justify-content-between py-3 px-1">
            <span class="fw-bold text-dark d-inline-flex align-items-center gap-2" style="font-size: 0.9rem;">
              <i class="ti ti-alarm fs-5" style="color: #59391f;"></i> Jam Buka Layanan
            </span>
            <span class="badge rounded-pill px-3 py-2 fw-extrabold" style="background: #59391f; color: #ffffff; font-size: 0.8rem;">07.30 – 11.30 WIB</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Modal 3: Facilities -->
<div class="modal fade" id="modalFasilitas" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content unida-themed-modal border-0">
      
      <!-- Modal Header -->
      <div class="modal-header unida-themed-modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title unida-themed-modal-title mb-0 d-flex align-items-center">
          <i class="ti ti-building fs-3 me-2" style="color: #c59b27 !important;"></i>
          <span>Fasilitas Utama Perpustakaan</span>
        </h5>
        <button type="button" class="unida-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
          <i class="ti ti-x fs-5"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4">
        <div class="row g-3">
          
          <div class="col-6 col-md-6">
            <div class="card border-0 rounded-4 p-4 text-center h-100 shadow-xs" style="background: #faf7f2; border: 1.5px solid #e8decb !important;">
              <i class="ti ti-wifi text-warning fs-1 mb-2" style="color: #c59b27 !important;"></i>
              <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Area Wi-Fi Gratis</h6>
              <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">Koneksi internet cepat untuk riset santri.</p>
            </div>
          </div>

          <div class="col-6 col-md-6">
            <div class="card border-0 rounded-4 p-4 text-center h-100 shadow-xs" style="background: #faf7f2; border: 1.5px solid #e8decb !important;">
              <i class="ti ti-device-tv text-warning fs-1 mb-2" style="color: #c59b27 !important;"></i>
              <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Display TV Realtime</h6>
              <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">Monitoring sirkulasi & info realtime.</p>
            </div>
          </div>

          <div class="col-6 col-md-6">
            <div class="card border-0 rounded-4 p-4 text-center h-100 shadow-xs" style="background: #faf7f2; border: 1.5px solid #e8decb !important;">
              <i class="ti ti-id-badge-2 text-warning fs-1 mb-2" style="color: #c59b27 !important;"></i>
              <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Presensi Buku Tamu</h6>
              <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">Sistem scan & pencatatan digital.</p>
            </div>
          </div>

          <div class="col-6 col-md-6">
            <div class="card border-0 rounded-4 p-4 text-center h-100 shadow-xs" style="background: #faf7f2; border: 1.5px solid #e8decb !important;">
              <i class="ti ti-armchair text-warning fs-1 mb-2" style="color: #c59b27 !important;"></i>
              <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Ruang Baca Santri</h6>
              <p class="text-muted mb-0" style="font-size: 0.8rem; line-height: 1.4;">Area baca nyaman & kondusif.</p>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const catBtns = document.querySelectorAll('.explore-cat-btn');
  const bookItems = document.querySelectorAll('.explore-book-item');
  const emptyMsg = document.getElementById('exploreEmptyMsg');

  catBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Update active state style on capsule buttons
      catBtns.forEach(b => {
        b.classList.remove('active');
        b.style.background = '#ffffff';
        b.style.color = '#6e4727';
        b.style.border = '1.5px solid #e8decb';
      });

      this.classList.add('active');
      this.style.background = '#59391f';
      this.style.color = '#ffffff';
      this.style.border = '1.5px solid #59391f';

      const selectedCat = this.getAttribute('data-cat-id');
      let visibleCount = 0;

      bookItems.forEach(item => {
        const itemCat = item.getAttribute('data-cat-id');
        
        if (selectedCat === 'all') {
          if (visibleCount < 6) {
            item.style.display = 'block';
            visibleCount++;
          } else {
            item.style.display = 'none';
          }
        } else {
          if (itemCat === selectedCat && visibleCount < 6) {
            item.style.display = 'block';
            visibleCount++;
          } else {
            item.style.display = 'none';
          }
        }
      });

      if (emptyMsg) {
        emptyMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
      }
    });
  });
});
</script>

<?= $this->endSection() ?>
