<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Daftar Buku</title>
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
          <i class="ti ti-books me-1"></i> Katalog Perpustakaan
        </div>
        <h3 class="text-white fw-bold mb-1">Katalog & Stok Buku</h3>
        <p class="text-white-50 mb-0">Kelola koleksi pustaka, lokasi rak, kategori, dan ketersediaan stok fisik.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/books/new'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-plus me-1"></i> Tambah Buku Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <!-- Advanced Filter Form (Harmonized High-Contrast Modern Style) -->
    <form action="" method="get" class="mb-4 p-3 p-md-4 rounded-4 shadow-sm" style="background: #ffffff; border: 1.5px solid #e8decb !important;">
      <div class="row g-3">
        <div class="col-12 col-md-3">
          <label class="form-label fw-bold mb-1.5 d-flex align-items-center gap-1" style="color: #6e4727; font-size: 0.725rem; letter-spacing: 0.5px;">
            <i class="ti ti-book" style="color: #c59b27;"></i> JUDUL BUKU
          </label>
          <input type="text" class="form-control px-3 py-2 fw-medium shadow-none" name="title" value="<?= esc($title ?? ''); ?>" placeholder="Cari judul..." style="background-color: #faf6f0; border: 1px solid #d4c4b0; color: #2d241e; font-size: 0.875rem; border-radius: 8px;">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label fw-bold mb-1.5 d-flex align-items-center gap-1" style="color: #6e4727; font-size: 0.725rem; letter-spacing: 0.5px;">
            <i class="ti ti-user" style="color: #c59b27;"></i> PENULIS / PENGARANG
          </label>
          <input type="text" class="form-control px-3 py-2 fw-medium shadow-none" name="author" value="<?= esc($author ?? ''); ?>" placeholder="Cari penulis..." style="background-color: #faf6f0; border: 1px solid #d4c4b0; color: #2d241e; font-size: 0.875rem; border-radius: 8px;">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label fw-bold mb-1.5 d-flex align-items-center gap-1" style="color: #6e4727; font-size: 0.725rem; letter-spacing: 0.5px;">
            <i class="ti ti-category" style="color: #c59b27;"></i> KATEGORI
          </label>
          <select class="form-select px-3 py-2 fw-medium shadow-none" name="category_id" style="background-color: #faf6f0; border: 1px solid #d4c4b0; color: #2d241e; font-size: 0.875rem; border-radius: 8px;">
            <option value="">Semua Kategori</option>
            <?php foreach ($categories as $cat) : ?>
              <option value="<?= $cat['id']; ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : ''; ?>><?= esc($cat['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label fw-bold mb-1.5 d-flex align-items-center gap-1" style="color: #6e4727; font-size: 0.725rem; letter-spacing: 0.5px;">
            <i class="ti ti-columns" style="color: #c59b27;"></i> LOKASI RAK
          </label>
          <select class="form-select px-3 py-2 fw-medium shadow-none" name="rack_id" style="background-color: #faf6f0; border: 1px solid #d4c4b0; color: #2d241e; font-size: 0.875rem; border-radius: 8px;">
            <option value="">Semua Rak</option>
            <?php foreach ($racks as $r) : ?>
              <option value="<?= $r['id']; ?>" <?= ($rackId ?? '') == $r['id'] ? 'selected' : ''; ?>><?= esc($r['name']); ?> (Lantai <?= $r['floor']; ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12 d-flex justify-content-end align-items-center gap-2 pt-1">
          <?php if (!empty($title) || !empty($author) || !empty($categoryId) || !empty($rackId) || !empty($search)) : ?>
            <a href="<?= base_url('admin/books'); ?>" class="btn btn-outline-secondary btn-sm px-3 py-2 fw-bold rounded-3" style="border-color: #d4c4b0; color: #6e4727; background-color: #ffffff;"><i class="ti ti-rotate me-1"></i> Reset Filter</a>
          <?php endif; ?>
          <button class="btn btn-primary btn-sm px-4 py-2 fw-bold rounded-3 text-white shadow-sm" type="submit" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none;"><i class="ti ti-search me-1"></i> Terapkan Filter</button>
        </div>
      </div>
    </form>

    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col" class="text-center">Sampul</th>
            <th scope="col">Judul & Pengarang</th>
            <th scope="col">Kategori</th>
            <th scope="col">Rak</th>
            <th scope="col" class="text-center">Stok</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1 + ($itemPerPage * ($currentPage - 1)) ?>
          <?php if (empty($books)) : ?>
            <tr>
              <td class="text-center py-4" colspan="7">
                <i class="ti ti-info-circle fs-6 d-block mb-1 text-muted"></i>
                <b>Tidak ada data buku ditemukan</b>
              </td>
            </tr>
          <?php endif; ?>
          <?php foreach ($books as $book) : ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td class="text-center">
                <div class="d-flex justify-content-center align-items-center mx-auto rounded overflow-hidden shadow-sm" style="width: 50px; height: 65px; background: #f8fafc;">
                  <?php
                  $coverImageUrl = getBookCoverUrl($book['book_cover']);
                  ?>
                  <img class="mh-100 mw-100 object-fit-cover" src="<?= $coverImageUrl; ?>" alt="<?= esc($book['title']); ?>">
                </div>
              </td>
              <td>
                <div class="fw-bold text-dark fs-3"><?= esc("{$book['title']} ({$book['year']})"); ?></div>
                <small class="text-muted"><i class="ti ti-user me-1"></i><?= esc($book['author']); ?></small>
              </td>
              <td>
                <span class="badge badge-subtle-primary fs-2"><?= esc($book['category']); ?></span>
              </td>
              <td>
                <span class="badge badge-subtle-secondary fs-2"><i class="ti ti-box me-1"></i><?= esc($book['rack']); ?></span>
              </td>
              <td class="text-center">
                <span class="badge bg-primary-subtle text-primary fw-bold fs-3 px-3 py-2 rounded-circle">
                  <?= esc($book['quantity']); ?>
                </span>
              </td>
              <td class="text-center pe-3">
                <a href="<?= base_url("admin/books/{$book['slug']}"); ?>" class="btn btn-primary btn-sm px-3 rounded-3">
                  <i class="ti ti-eye me-1"></i> Detail
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager->links('books', 'my_pager'); ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>