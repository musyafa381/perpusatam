<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Katalog Pustaka - Perpustakaan Assalafiyyah Mlangi</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper(['upload_helper']); ?>

<div class="container py-4">

  <!-- UNIDA Hero Banner with Library Theme Colors -->
  <section class="unida-hero-banner text-center shadow-sm mb-4">
    <div class="container px-3 position-relative" style="z-index: 2;">
      
      <h1 class="fw-extrabold text-white mb-2" style="font-family: 'Georgia', serif; font-size: 2.2rem; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);">
        Perpustakaan Assalafiyyah Mlangi
      </h1>
      <p class="text-white-50 mx-auto mb-4" style="max-width: 650px; font-size: 0.95rem; line-height: 1.5;">
        <?= !empty($search) ? 'Menampilkan hasil pencarian kata kunci: <strong class="text-warning">"' . esc($search) . '"</strong>' : 'Pondok Pesantren Assalafiyyah Mlangi Sleman Yogyakarta'; ?>
      </p>

      <!-- Mega Search Bar Form -->
      <form action="<?= base_url('book'); ?>" method="get" class="mb-4">
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

  <!-- Book Grid Section (6 Cards per Row on Desktop) -->
  <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3 mb-4">
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

              <!-- Hover Title & Details Overlay (Revealed on Cursor Hover) -->
              <div class="unida-cover-overlay text-white">
                <h6 class="fw-bold text-white mb-1 text-truncate-2" title="<?= esc($book['title']); ?>" style="font-size: 0.84rem; line-height: 1.25; font-family: 'Georgia', serif;">
                  <?= esc($book['title']); ?>
                </h6>
                <div class="fw-semibold text-truncate mb-2" style="color: #f0c968; font-size: 0.72rem;">
                  <i class="ti ti-user me-0.5"></i><?= esc($book['author'] ?: 'Penulis tak diketahui'); ?>
                </div>

                <div class="d-flex align-items-center justify-content-between gap-1 pt-1.5 border-top" style="border-color: rgba(255, 255, 255, 0.2) !important;">
                  <span class="badge rounded-pill px-2 py-0.5 fw-bold text-truncate" style="background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); font-size: 0.6rem; max-width: 75px;">
                    <?= esc($book['category'] ?: 'Umum'); ?>
                  </span>
                  <span class="badge rounded-pill px-2 py-0.5 fw-extrabold text-truncate" style="background: rgba(197, 155, 39, 0.35); color: #f0c968; border: 1px solid rgba(197, 155, 39, 0.5); font-size: 0.6rem;">
                    <i class="ti ti-columns me-0.5"></i>Rak <?= esc($book['rack'] ?: '-'); ?>
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
<?= $this->endSection() ?>