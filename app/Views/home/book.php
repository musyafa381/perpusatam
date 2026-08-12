<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Katalog Pustaka - Perpustakaan Assalafiyyah Mlangi</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper(['upload_helper']); ?>

<!-- 1. Hero Banner UNIDA Gontor Style (Identical to Portal Page) -->
<section class="unida-hero-banner text-center">
  <div class="container px-3">
    
    <h1 class="fw-extrabold text-white mb-2" style="font-family: 'Georgia', serif; font-size: 2.5rem; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);">
      Perpustakaan Assalafiyyah Mlangi
    </h1>
    <p class="text-white-50 mx-auto mb-4" style="max-width: 650px; font-size: 1rem; line-height: 1.5;">
      <?= !empty($search) ? 'Menampilkan hasil pencarian kata kunci: <strong class="text-warning">"' . esc($search) . '"</strong>' : 'Pondok Pesantren Assalafiyyah Mlangi Sleman Yogyakarta'; ?>
    </p>

    <!-- Mega Search Bar Form -->
    <form action="<?= base_url('book'); ?>" method="get" class="mb-3">
      <?php if (!empty($selectedCategory)): ?>
        <input type="hidden" name="category" value="<?= esc($selectedCategory); ?>">
      <?php endif; ?>
      <div class="unida-search-wrapper">
        <i class="ti ti-search fs-5 text-muted me-2 ms-1"></i>
        <input type="text" name="search" class="form-control unida-search-input" value="<?= esc($search ?? ''); ?>" placeholder="Cari buku, e-books, pengarang, penerbit, nomor ISBN..." aria-label="Cari Pustaka">
        <?php if (!empty($search)): ?>
          <a href="<?= base_url('book' . ($selectedCategory ? '?category=' . $selectedCategory : '')); ?>" class="text-muted text-decoration-none me-2" title="Hapus pencarian">
            <i class="ti ti-x fs-5"></i>
          </a>
        <?php endif; ?>
        <button class="btn unida-search-btn" type="submit">
          <i class="ti ti-search me-1"></i> Cari Pustaka
        </button>
      </div>
    </form>

    <!-- UNIDA Gontor Category Pills with Book Counts (Top 7) -->
    <?php if (!empty($categories)): ?>
      <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 fs-7" style="color: rgba(255, 255, 255, 0.8);">
        
        <!-- 'Semua' Category Pill -->
        <a href="<?= base_url('book' . (!empty($search) ? '?search=' . urlencode($search) : '')); ?>" class="unida-cat-pill <?= empty($selectedCategory) ? 'active' : 'inactive'; ?>">
          <i class="ti ti-books"></i>
          <span>Semua</span>
          <span class="unida-cat-count-badge"><?= $totalBooksCount ?? count($books); ?></span>
        </a>

        <!-- Top 7 Category Pills with Count Badges -->
        <?php foreach ($categories as $cat) : ?>
          <a href="<?= base_url('book?category=' . $cat['id'] . (!empty($search) ? '&search=' . urlencode($search) : '')); ?>" class="unida-cat-pill <?= (string)$selectedCategory === (string)$cat['id'] ? 'active' : 'inactive'; ?>">
            <span><?= esc($cat['name']); ?></span>
            <span class="unida-cat-count-badge"><?= (int)($cat['total_books'] ?? 0); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<div class="container py-4">

  <!-- Active Filter Bar (If any filter is applied) -->
  <?php if (!empty($selectedCategory) || !empty($selectedAuthor) || !empty($selectedPublisher) || !empty($search)): ?>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 rounded-4 mb-4 shadow-sm" style="background: rgba(255, 255, 255, 0.95); border: 1.5px solid #e2d5c3; backdrop-filter: blur(10px);">
      <div class="d-flex align-items-center flex-wrap gap-2">
        <span class="fw-extrabold fs-7 me-1 d-inline-flex align-items-center gap-1" style="color: #59391f;">
          <i class="ti ti-filter fs-5" style="color: #c59b27;"></i> Filter Aktif:
        </span>

        <?php if (!empty($search)): ?>
          <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8;">
            Pencarian: "<?= esc($search); ?>"
          </span>
        <?php endif; ?>

        <?php if (!empty($selectedCategory)): ?>
          <?php 
            $catName = 'Kategori';
            foreach ($allCategories as $ac) {
              if ((string)$ac['id'] === (string)$selectedCategory) { $catName = $ac['name']; break; }
            }
          ?>
          <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8;">
            Kategori: <?= esc($catName); ?>
          </span>
        <?php endif; ?>

        <?php if (!empty($selectedAuthor)): ?>
          <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8;">
            Penulis: <?= esc($selectedAuthor); ?>
          </span>
        <?php endif; ?>

        <?php if (!empty($selectedPublisher)): ?>
          <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fdf6ea; color: #6e4727; border: 1px solid #f3e5c8;">
            Penerbit: <?= esc($selectedPublisher); ?>
          </span>
        <?php endif; ?>
      </div>

      <a href="<?= base_url('book'); ?>" class="btn btn-sm rounded-pill px-3 py-1.5 fw-extrabold" style="background: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; font-size: 0.75rem;">
        <i class="ti ti-x me-1"></i> Reset Semua Filter
      </a>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    
    <!-- Left Column: UNIDA Sidebar Filter Card (3 Columns) -->
    <div class="col-12 col-lg-3">
      <div class="card border-0 shadow-sm rounded-4 p-3.5 sticky-top" style="top: 85px; background: #ffffff; border: 1.5px solid #e2d5c3 !important; box-shadow: 0 8px 24px rgba(89, 57, 31, 0.06) !important;">
        
        <!-- Header Title Bar -->
        <div class="d-flex align-items-center justify-content-between pb-2.5 mb-3 border-bottom" style="border-color: #e2d5c3 !important;">
          <h5 class="fw-extrabold mb-0 d-flex align-items-center gap-2" style="color: #2d1e18; font-family: 'Georgia', serif; font-size: 1.05rem;">
            <i class="ti ti-adjustments-horizontal fs-4" style="color: #c59b27 !important;"></i> Filter Pustaka
          </h5>
          <?php if (!empty($selectedCategory) || !empty($selectedAuthor) || !empty($selectedPublisher) || !empty($search)): ?>
            <a href="<?= base_url('book'); ?>" class="text-decoration-none fw-bold text-danger fs-8" title="Reset filter">
              <i class="ti ti-rotate-clockwise me-0.5"></i> Reset
            </a>
          <?php endif; ?>
        </div>

        <form action="<?= base_url('book'); ?>" method="get">
          
          <?php if (!empty($search)): ?>
            <input type="hidden" name="search" value="<?= esc($search); ?>">
          <?php endif; ?>

          <!-- Filter Item 1: Kategori Buku -->
          <div class="mb-3">
            <label class="form-label fw-bold fs-7 mb-1.5 d-flex align-items-center gap-1.5" style="color: #59391f;">
              <i class="ti ti-bookmark" style="color: #c59b27;"></i> Kategori Buku
            </label>
            <select name="category" class="form-select form-select-sm rounded-3 fw-semibold" style="border: 1.5px solid #e2d5c3; color: #2d1e18; background-color: #fdfbf7; padding: 7px 12px; font-size: 0.825rem;">
              <option value="">-- Semua Kategori --</option>
              <?php foreach ($allCategories as $cat): ?>
                <option value="<?= esc($cat['id']); ?>" <?= (string)$selectedCategory === (string)$cat['id'] ? 'selected' : ''; ?>>
                  <?= esc($cat['name']); ?> (<?= (int)($cat['total_books'] ?? 0); ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Filter Item 2: Penulis / Pengarang -->
          <div class="mb-3">
            <label class="form-label fw-bold fs-7 mb-1.5 d-flex align-items-center gap-1.5" style="color: #59391f;">
              <i class="ti ti-user" style="color: #c59b27;"></i> Penulis / Pengarang
            </label>
            <select name="author" class="form-select form-select-sm rounded-3 fw-semibold" style="border: 1.5px solid #e2d5c3; color: #2d1e18; background-color: #fdfbf7; padding: 7px 12px; font-size: 0.825rem;">
              <option value="">-- Semua Penulis --</option>
              <?php foreach ($authors as $aut): ?>
                <option value="<?= esc($aut['author']); ?>" <?= (string)$selectedAuthor === (string)$aut['author'] ? 'selected' : ''; ?>>
                  <?= esc($aut['author']); ?> (<?= (int)$aut['total_books']; ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Filter Item 3: Penerbit -->
          <div class="mb-4">
            <label class="form-label fw-bold fs-7 mb-1.5 d-flex align-items-center gap-1.5" style="color: #59391f;">
              <i class="ti ti-building" style="color: #c59b27;"></i> Penerbit
            </label>
            <select name="publisher" class="form-select form-select-sm rounded-3 fw-semibold" style="border: 1.5px solid #e2d5c3; color: #2d1e18; background-color: #fdfbf7; padding: 7px 12px; font-size: 0.825rem;">
              <option value="">-- Semua Penerbit --</option>
              <?php foreach ($publishers as $pub): ?>
                <option value="<?= esc($pub['publisher']); ?>" <?= (string)$selectedPublisher === (string)$pub['publisher'] ? 'selected' : ''; ?>>
                  <?= esc($pub['publisher']); ?> (<?= (int)$pub['total_books']; ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Submit Filter Button -->
          <button type="submit" class="btn w-100 rounded-pill py-2.5 fw-extrabold text-white shadow-sm d-flex align-items-center justify-content-center gap-1.5" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); border: none; font-size: 0.85rem;">
            <i class="ti ti-filter me-1" style="color: #f0c968;"></i> Terapkan Filter
          </button>
          
        </form>

      </div>
    </div>

    <!-- Right Column: Catalog Book Grid (9 Columns) -->
    <div class="col-12 col-lg-9">
      
      <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-5 g-3 mb-4">
        <?php if (empty($books)) : ?>
          <div class="col-12 text-center py-5 bg-white rounded-4 border shadow-sm" style="border: 1.5px solid #e2d5c3 !important;">
            <div class="py-4">
              <i class="ti ti-search-off text-muted mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
              <h4 class="fw-bold text-dark mb-2">Buku Tidak Ditemukan</h4>
              <p class="text-muted fs-7 mb-4">Maaf, koleksi buku yang Anda cari tidak tersedia dalam katalog saat ini.</p>
              <a href="<?= base_url('book'); ?>" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow-sm" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%);">
                <i class="ti ti-rotate-clockwise me-1"></i> Tampilkan Semua Koleksi
              </a>
            </div>
          </div>
        <?php else : ?>
          <?php foreach ($books as $index => $book) : ?>
            <?php
            $rawCover = $book['book_cover'] ?? '';
            $coverUrl = getBookCoverUrl($rawCover);
            $hasCover = !empty($rawCover) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
            $stockCount = (int)($book['quantity'] ?? 0);
            ?>
            <div class="col">
              <a href="<?= base_url('book/' . ($book['slug'] ?: $book['id'])); ?>" class="text-decoration-none d-block">
                <div class="unida-cover-hover-card">
                  
                  <!-- Clean Cover Image Display (Default State) -->
                  <?php if ($hasCover): ?>
                    <img src="<?= $coverUrl; ?>" alt="<?= esc($book['title']); ?>" loading="lazy" class="unida-cover-img">
                  <?php else: ?>
                    <div class="d-flex flex-column align-items-center justify-content-center text-center p-3 h-100 w-100" style="background: linear-gradient(135deg, #59391f 0%, #7c522f 100%); color: #ffffff;">
                      <i class="ti ti-book fs-1 mb-2" style="color: #f0c968;"></i>
                      <span class="fw-bold fs-7 text-white text-truncate-3 px-1" style="line-height: 1.25; font-family: 'Georgia', serif;"><?= esc($book['title']); ?></span>
                    </div>
                  <?php endif; ?>

                  <!-- Hover Title & Details Overlay (Clean UNIDA Layout) -->
                  <div class="unida-cover-overlay text-white">
                    <!-- Category Top Capsule Tag -->
                    <div class="mb-1">
                      <span class="badge rounded-pill text-truncate fw-bold shadow-sm" style="background: #c59b27; color: #2d1e18; font-size: 0.61rem; padding: 3px 8px; max-width: 95%;">
                        <i class="ti ti-bookmark me-0.5"></i><?= esc($book['category'] ?: 'Umum'); ?>
                      </span>
                    </div>

                    <!-- Judul Utama Buku -->
                    <h6 class="fw-bold text-white mb-1 text-truncate-2" title="<?= esc($book['title']); ?>" style="font-size: 0.88rem; line-height: 1.25; font-family: 'Georgia', serif; text-shadow: 0 1px 3px rgba(0,0,0,0.5);">
                      <?= esc($book['title']); ?>
                    </h6>

                    <!-- Penulis & Rak Bottom Row -->
                    <div class="d-flex align-items-center justify-content-between gap-1 mt-1 pt-1.5 border-top" style="border-color: rgba(255, 255, 255, 0.2) !important;">
                      <span class="fw-semibold text-truncate" style="color: #f3d382; font-size: 0.72rem;">
                        <i class="ti ti-user me-0.5" style="color: #c59b27;"></i><?= esc($book['author'] ?: 'Penulis tak diketahui'); ?>
                      </span>
                      <span class="badge rounded-pill px-2 py-0.5 fw-extrabold flex-shrink-0" style="background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.62rem;">
                        <i class="ti ti-columns me-0.5" style="color: #f3d382;"></i>Rak <?= esc($book['rack'] ?: '-'); ?>
                      </span>
                    </div>
                  </div>

                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Pagination Bar -->
      <?php if (!empty($books)): ?>
        <div class="d-flex justify-content-center mt-4">
          <?= $pager->links('books', 'my_pager'); ?>
        </div>
      <?php endif; ?>

    </div>

  </div>

</div>
<?= $this->endSection() ?>