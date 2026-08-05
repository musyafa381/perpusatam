<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Katalog Pustaka - Perpustakaan Assalafiyyah Mlangi</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper(['upload_helper']); ?>

<div class="container py-4">

  <!-- Top Action Navigation -->
  <div class="d-flex align-items-center justify-content-between gap-2 mb-3" style="flex-wrap: nowrap;">
    <a href="<?= base_url(); ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold bg-white shadow-sm text-nowrap flex-shrink-0">
      <i class="ti ti-arrow-left me-1"></i> <span class="d-none d-sm-inline">Kembali ke Beranda TV</span><span class="d-inline d-sm-none">Kembali</span>
    </a>
    <div class="d-flex align-items-center gap-2">
      <a href="<?= base_url('buku-tamu'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold shadow-sm text-white text-nowrap" style="background-color: #c59b27; border: none;">
        <i class="ti ti-id-badge-2 me-1"></i> <span class="d-none d-sm-inline">Buku Tamu Digital</span><span class="d-inline d-sm-none">Buku Tamu</span>
      </a>
      <a href="<?= base_url('login'); ?>" class="btn btn-sm rounded-pill px-3 fw-bold shadow-sm text-white text-nowrap" style="background-color: #6e4727; border: none;">
        <i class="ti ti-login me-1"></i> Login
      </a>
    </div>
  </div>

  <!-- Header Banner & Search Status (Harmonized Cream, Brown & Gold Theme) -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #ffffff 0%, #faf5ee 50%, #f4eae0 100%); border: 1px solid #e2d5c3 !important; box-shadow: 0 4px 18px rgba(110, 71, 39, 0.06) !important;">
    <div class="card-body p-4 p-md-4">
      <div class="row align-items-center gy-3">
        <div class="col-md-7">
          <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
            <span class="badge px-3 py-2 rounded-pill fw-bold text-white shadow-sm flex-shrink-0" style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 50%, #c59b27 100%); font-size: 0.75rem; letter-spacing: 0.5px;">
              <i class="ti ti-books me-1"></i> KATALOG KEILMUAN
            </span>
            <span class="fw-semibold small" style="color: #8b5e3c;">• Perpustakaan Assalafiyyah Mlangi</span>
          </div>
          <h2 class="fw-extrabold mb-1" style="color: #4a3424 !important; font-family: 'Georgia', serif; font-size: 1.65rem;">Pameran Koleksi Buku</h2>
          <p class="fs-7 mb-0" style="color: #7c6857 !important; font-weight: 500;">
            <?= !empty($search) ? 'Menampilkan hasil pencarian kata kunci: <strong style="color: #4a3424;">"' . esc($search) . '"</strong>' : 'Menjelajahi seluruh perbendaharaan kitab turats, buku keilmuan, dan referensi pustaka.'; ?>
          </p>
        </div>

        <div class="col-md-5 text-md-end">
          <form action="<?= base_url('book'); ?>" method="get" class="d-flex gap-2 justify-content-md-end">
            <?php if (!empty($selectedCategory)): ?>
              <input type="hidden" name="category" value="<?= esc($selectedCategory); ?>">
            <?php endif; ?>
            <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white" style="border: 1.5px solid #d4c4b0 !important;">
              <span class="input-group-text bg-white border-0 ps-3" style="color: #8b5e3c;">
                <i class="ti ti-search fs-5"></i>
              </span>
              <input type="text" name="search" class="form-control border-0 shadow-none fs-7 py-2" value="<?= esc($search ?? ''); ?>" placeholder="Cari judul, penulis, ISBN..." style="color: #2d241e;" />
              <?php if (!empty($search)): ?>
                <a href="<?= base_url('book' . ($selectedCategory ? '?category=' . $selectedCategory : '')); ?>" class="input-group-text bg-white border-0 pe-2 text-decoration-none" style="color: #8b5e3c;">
                  <i class="ti ti-x fs-6"></i>
                </a>
              <?php endif; ?>
              <button type="submit" class="btn fw-bold px-4 text-white shadow-sm" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none;">Cari</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Category Filter Pills -->
      <?php if (!empty($categories)): ?>
        <div class="mt-4 pt-3 border-top" style="border-color: #e8decb !important;">
          <span class="fw-bold small d-block mb-2" style="color: #6e4727;"><i class="ti ti-filter me-1" style="color: #c59b27;"></i> Filter Kategori:</span>
          <div class="d-flex align-items-center gap-2 pb-1" style="overflow-x: auto; -webkit-overflow-scrolling: touch; flex-wrap: nowrap; scrollbar-width: none;">
            <a href="<?= base_url('book' . ($search ? '?search=' . urlencode($search) : '')); ?>" 
               class="btn btn-sm rounded-pill px-3 fw-bold transition-all shadow-sm text-nowrap flex-shrink-0" 
               style="<?= empty($selectedCategory) ? 'background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); color: #ffffff; border: none;' : 'background: #ffffff; border: 1px solid #d4c4b0; color: #6e4727;'; ?>">
              Semua Kategori
            </a>
            <?php foreach ($categories as $cat): ?>
              <a href="<?= base_url('book?category=' . $cat['id'] . ($search ? '&search=' . urlencode($search) : '')); ?>" 
                 class="btn btn-sm rounded-pill px-3 fw-semibold transition-all shadow-sm text-nowrap flex-shrink-0" 
                 style="<?= ($selectedCategory == $cat['id']) ? 'background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); color: #ffffff; border: none;' : 'background: #ffffff; border: 1px solid #d4c4b0; color: #6e4727;'; ?>">
                <?= esc($cat['name']); ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Book Grid Section (5 Cards per Row on Desktop) -->
  <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3 mb-4">
    <?php if (empty($books)) : ?>
      <div class="col-12 text-center py-5 bg-white rounded-4 border shadow-sm">
        <div class="py-4">
          <i class="ti ti-search-off text-muted mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
          <h4 class="fw-bold text-dark mb-2">Buku Tidak Ditemukan</h4>
          <p class="text-muted fs-7 mb-4">Maaf, koleksi buku yang Anda cari tidak tersedia dalam katalog saat ini.</p>
          <a href="<?= base_url('book'); ?>" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow-sm" style="background-color: #8b5e3c;">
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
          <a href="<?= base_url('book/' . ($book['slug'] ?: $book['id'])); ?>" class="text-decoration-none text-dark d-block h-100">
            <div class="card h-100 border-0 rounded-4 overflow-hidden catalog-book-card position-relative transition-all" style="background: #ffffff; border: 1.5px solid #e2d5c3 !important; box-shadow: 0 4px 14px rgba(110, 71, 39, 0.05); cursor: pointer;">
              
              <!-- Frame Cover Buku (Contain Full Image, Zero Cropping!) -->
              <div class="catalog-book-cover position-relative d-flex align-items-center justify-content-center overflow-hidden p-2" style="height: 210px; background: linear-gradient(135deg, #faf5ee 0%, #eee4d5 100%);">
                <?php if ($hasCover): ?>
                  <img src="<?= $coverUrl; ?>" alt="<?= esc($book['title']); ?>" loading="lazy" class="h-100 w-auto shadow-sm" style="object-fit: contain; max-width: 100%; max-height: 100%; filter: drop-shadow(0 5px 10px rgba(110, 71, 39, 0.2)); transition: transform 0.35s ease;">
                <?php else: ?>
                  <div class="d-flex flex-column align-items-center justify-content-center text-center p-2.5 h-100 w-100 rounded-3" style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%); color: #ffffff; box-shadow: inset 0 0 15px rgba(0,0,0,0.2);">
                    <i class="ti ti-book fs-2 mb-1.5" style="color: #c59b27;"></i>
                    <span class="fw-bold fs-7 text-white text-truncate-2 px-1" style="line-height: 1.25; font-family: 'Georgia', serif;"><?= esc($book['title']); ?></span>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Content Details Card -->
              <div class="card-body p-3 d-flex flex-column">
                <!-- Judul Utama Buku -->
                <h6 class="fw-bold mb-1 text-truncate-2" title="<?= esc($book['title']); ?>" style="color: #2d1e18 !important; font-size: 0.9rem; line-height: 1.3; min-height: 2.4em; font-weight: 700;">
                  <?= esc($book['title']); ?>
                </h6>

                <!-- Nama Penulis Subtext -->
                <div class="fw-semibold mb-2 text-truncate" style="color: #8b5e3c !important; font-size: 0.78rem;">
                  <i class="ti ti-user me-1" style="color: #c59b27;"></i><?= esc($book['author'] ?: 'Penulis tak diketahui'); ?>
                </div>

                <!-- Kode Panggil & ISBN Sub-info Ringkas -->
                <div class="d-flex flex-column gap-1 mb-2" style="font-size: 0.725rem !important;">
                  <?php 
                    $callNo = $book['call_number'] ?: ($book['ddc'] ? 'DDC ' . $book['ddc'] : null);
                  ?>
                  <?php if (!empty($callNo)): ?>
                    <div class="text-truncate" style="color: #6e4727;" title="Nomor Panggil Buku">
                      <i class="ti ti-tag me-1" style="color: #c59b27;"></i><span class="fw-semibold">Panggil:</span> <code class="fw-bold text-dark px-1 bg-light rounded" style="font-size: 0.68rem;"><?= esc($callNo); ?></code>
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($book['isbn'])): ?>
                    <div class="text-truncate" style="color: #6e4727;" title="ISBN Buku">
                      <i class="ti ti-barcode me-1" style="color: #c59b27;"></i><span class="fw-semibold">ISBN:</span> <span class="fw-bold text-dark" style="font-family: 'Courier New', monospace; font-size: 0.68rem;"><?= esc($book['isbn']); ?></span>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Badge Status Stok -->
                <div class="mb-2">
                  <?php if ($stockCount > 0): ?>
                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #e6f4ea; color: #137333; border: 1px solid #ceead6; font-size: 0.65rem;">
                      <i class="ti ti-circle-check me-1" style="font-size: 0.7rem;"></i>Tersedia (<?= $stockCount ?>)
                    </span>
                  <?php else: ?>
                    <span class="badge rounded-pill px-2 py-1 fw-bold" style="background: #fce8e6; color: #c5221f; border: 1px solid #fad2cf; font-size: 0.65rem;">
                      <i class="ti ti-circle-x me-1" style="font-size: 0.7rem;"></i>Dipinjam
                    </span>
                  <?php endif; ?>
                </div>

                <!-- Footer Metadata: Kategori & Lokasi Rak -->
                <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between fs-8" style="border-color: #f0e6d6 !important;">
                  <span class="badge rounded-pill px-2.5 py-1 fw-bold text-truncate" style="background: #f4eae0; color: #6e4727; max-width: 90px; font-size: 0.65rem;">
                    <?= esc($book['category'] ?: 'Umum'); ?>
                  </span>
                  <span class="badge rounded-pill px-2.5 py-1 fw-extrabold" style="background: #fff8eb; border: 1px solid #f3e5c8; color: #b48316; font-size: 0.65rem;">
                    <i class="ti ti-columns me-1" style="color: #c59b27;"></i>Rak <?= esc($book['rack'] ?: '-'); ?>
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