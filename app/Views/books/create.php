<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Tambah Buku</title>
<style>
@keyframes aiPulseScan {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
.ai-searching-banner {
  background: linear-gradient(270deg, #fffae6, #fff3cd, #ffe8a1, #fffae6);
  background-size: 400% 400%;
  animation: aiPulseScan 2s ease infinite;
  border: 1.5px dashed #d4af37 !important;
}
.rotate-spin {
  animation: spin 0.8s linear infinite;
  display: inline-block;
}
@keyframes spin { 100% { transform: rotate(360deg); } }

/* Styling Input Group + Select2 agar tombol (+) menempel sempurna */
.input-group > .select2-container {
  flex: 1 1 auto;
  width: 1% !important;
}
.input-group > .select2-container .select2-selection {
  border-top-right-radius: 0 !important;
  border-bottom-right-radius: 0 !important;
}
.input-group > .btn-add-inline {
  border-top-left-radius: 0 !important;
  border-bottom-left-radius: 0 !important;
  z-index: 4;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
/* Styling Select2 Multiple Tag Badges + Small x remove icon */
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 6px !important;
  padding: 4px 8px !important;
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
  display: inline-flex !important;
  align-items: center !important;
  flex-direction: row-reverse !important;
  gap: 6px !important;
  background-color: #f7f3ed !important;
  color: #6e4727 !important;
  border: 1.5px solid #e2d5c3 !important;
  border-radius: 20px !important;
  padding: 3px 10px !important;
  font-size: 0.82rem !important;
  font-weight: 600 !important;
  margin: 2px 0 !important;
  transition: all 0.2s ease !important;
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice:hover {
  background-color: #ffebee !important;
  border-color: #ffcdd2 !important;
  color: #c62828 !important;
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
  background: none !important;
  border: none !important;
  color: #c62828 !important;
  font-weight: bold !important;
  font-size: 0.95rem !important;
  line-height: 1 !important;
  padding: 0 !important;
  margin-left: 2px !important;
  cursor: pointer !important;
  opacity: 0.8 !important;
  transition: opacity 0.2s !important;
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
  opacity: 1 !important;
  color: #b71c1c !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('admin/books'); ?>" class="btn btn-outline-primary mb-3">
  <i class="ti ti-arrow-left"></i>
  Kembali
</a>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Card Cari via AI & Multi-Source -->
<div class="card mb-4 border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #fcf8f2 0%, #f7ebd9 100%); border: 1px solid #e8decb !important;">
  <div class="card-body p-4">
    <div class="d-flex align-items-center gap-2 mb-2">
      <span class="badge bg-warning text-dark px-3 py-1 fs-3 fw-bold rounded-pill"><i class="ti ti-sparkles me-1"></i> AI & Multi-Source Engine</span>
      <h5 class="fw-bold text-dark mb-0">Cari Data Buku & Kover Otomatis</h5>
    </div>
    <p class="text-muted fs-2 mb-3">Scan <strong>Barcode ISBN</strong> atau ketik <strong>Judul Buku / Penulis</strong> di bawah ini. AI & sistem akan otomatis melengkapi Judul, Penulis, Penerbit, Tahun, Kategori, dan mengunggah kover ke Cloudinary!</p>
    <div class="input-group input-group-lg shadow-sm rounded-3">
      <input type="text" id="aiQueryInput" autofocus class="form-control border-warning bg-white fw-semibold" placeholder="Scan Barcode ISBN atau ketik Judul / Penulis di sini (langsung siap scan)...">
      <button class="btn btn-warning text-dark fw-bold px-4 shadow-sm d-inline-flex align-items-center gap-1" type="button" id="btnAiSearch">
        <i class="ti ti-sparkles" id="aiSearchIcon"></i>
        <span id="aiBtnText">Cari via AI & Kover</span>
      </button>
    </div>

    <!-- Animasi AI Memindai -->
    <div id="aiSearchLoadingBox" class="mt-3 p-4 rounded-4 text-center shadow-lg" style="display: none; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; border: 2px solid #fbbf24; box-shadow: 0 10px 30px rgba(251, 191, 36, 0.3) !important;">
      <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
        <div class="spinner-grow text-warning" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
        <h5 class="fw-bold text-warning mb-0 fs-5"><i class="ti ti-sparkles me-2"></i> AI Sedang Memindai & Mencari Metadata Buku...</h5>
      </div>
      <div class="progress mt-3 mb-2" style="height: 10px; background: rgba(255,255,255,0.15); border-radius: 10px; overflow: hidden;">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%;"></div>
      </div>
      <div class="text-white-50 fs-2 mt-2">
        <span>🤖 Menanyakan ke AI: <em>"Carikan kover buku, judul buku, pengarang, penerbit, tahun terbit, dan genre dari: <strong><span id="aiQueryPromptDisplay" class="text-warning fw-bold"></span></strong>"</em></span>
      </div>
    </div>

    <div id="aiSearchAlert" class="mt-3" style="display: none;"></div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-3">Form Tambah Buku</h5>
    <form action="<?= base_url('admin/books'); ?>" method="post" enctype="multipart/form-data" data-no-pjax>
      <?= csrf_field(); ?>
      <input type="hidden" name="cover_url" id="cover_url" value="">
      <div class="row">
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3 p-3">
          <label for="cover" class="d-block" style="cursor: pointer;">
            <div class="d-flex justify-content-center bg-light overflow-hidden h-100 position-relative">
              <img id="bookCoverPreview" src="<?= base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER); ?>" alt="" height="300" class="z-1">
              <p class="position-absolute top-50 start-50 translate-middle z-0">Pilih sampul</p>
            </div>
          </label>
        </div>
        <div class="col-12 col-md-6 col-lg-8 col-xl-9">
          <div class="mb-3">
            <label for="cover" class="form-label">Gambar sampul buku</label>
            <input class="form-control <?php if ($validation->hasError('cover')) : ?>is-invalid<?php endif ?>" type="file" id="cover" name="cover" onchange="previewImage()">
            <div class="invalid-feedback">
              <?= $validation->getError('cover'); ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="title" class="form-label">Judul buku</label>
            <input type="text" class="form-control <?php if ($validation->hasError('title')) : ?>is-invalid<?php endif ?>" id="title" name="title" value="<?= $oldInput['title'] ?? ''; ?>" required>
            <div class="invalid-feedback">
              <?= $validation->getError('title'); ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="author_id" class="form-label mb-1">Pengarang / Penulis <small class="text-muted fw-normal">(Bisa pilih lebih dari 1)</small></label>
            <?php 
              $oldAuthors = (array)($oldInput['author_id'] ?? []); 
            ?>
            <div class="input-group">
              <select class="form-select select2 <?php if ($validation->hasError('author_id')) : ?>is-invalid<?php endif ?>" id="author_id" name="author_id[]" multiple required>
                <?php foreach ($authors as $author) : ?>
                  <option value="<?= $author['id']; ?>" <?= in_array($author['id'], $oldAuthors) ? 'selected' : ''; ?>><?= esc($author['name']); ?></option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="btn btn-outline-primary btn-add-inline" data-bs-toggle="modal" data-bs-target="#addAuthorModal" title="Tambah Pengarang Baru">
                <i class="ti ti-plus fs-5"></i>
              </button>
            </div>
            <div class="invalid-feedback d-block">
              <?= $validation->getError('author_id'); ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="publisher_id" class="form-label mb-1">Penerbit</label>
            <?php 
              $oldPublisher = $oldInput['publisher_id'] ?? ''; 
            ?>
            <div class="input-group">
              <select class="form-select select2 <?php if ($validation->hasError('publisher_id')) : ?>is-invalid<?php endif ?>" id="publisher_id" name="publisher_id" required>
                <option value="" disabled selected>--Pilih penerbit--</option>
                <?php foreach ($publishers as $publisher) : ?>
                  <option value="<?= $publisher['id']; ?>" <?= ($oldPublisher == $publisher['id']) ? 'selected' : ''; ?>><?= esc($publisher['name']); ?></option>
                <?php endforeach; ?>
              </select>
              <button type="button" class="btn btn-outline-primary btn-add-inline" data-bs-toggle="modal" data-bs-target="#addPublisherModal" title="Tambah Penerbit Baru">
                <i class="ti ti-plus fs-5"></i>
              </button>
            </div>
            <div class="invalid-feedback d-block">
              <?= $validation->getError('publisher_id'); ?>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-6 mb-3">
          <label for="isbn" class="form-label">ISBN</label>
          <input type="text" class="form-control <?php if ($validation->hasError('isbn')) : ?>is-invalid<?php endif ?>" id="isbn" name="isbn" minlength="10" maxlength="13" value="<?= $oldInput['isbn'] ?? ''; ?>" required placeholder="Nomor 10-13 digit ISBN (terisi otomatis)">
          <div class="invalid-feedback">
            <?= $validation->getError('isbn'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="year" class="form-label">Tahun terbit</label>
          <input type="number" class="form-control <?php if ($validation->hasError('year')) : ?>is-invalid<?php endif ?>" id="year" name="year" minlength="4" maxlength="4" value="<?= $oldInput['year'] ?? ''; ?>" required>
          <div class="invalid-feedback">
            <?= $validation->getError('year'); ?>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-md-6 mb-3">
          <label for="rack" class="form-label mb-1">Rak</label>
          <div class="input-group">
            <select class="form-select select2 <?php if ($validation->hasError('rack')) : ?>is-invalid<?php endif ?>" aria-label="Select rack" id="rack" name="rack" required>
              <option value="" disabled selected>--Pilih rak--</option>
              <?php foreach ($racks as $rack) : ?>
                <option value="<?= $rack['id']; ?>" <?= ($oldInput['rack'] ?? '') == $rack['id'] ? 'selected' : ''; ?>><?= $rack['name']; ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-primary btn-add-inline" data-bs-toggle="modal" data-bs-target="#addRackModal" title="Tambah Rak Baru">
              <i class="ti ti-plus fs-5"></i>
            </button>
          </div>
          <div class="invalid-feedback d-block">
            <?= $validation->getError('rack'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="category" class="form-label mb-1">Kategori <small class="text-muted fw-normal">(Bisa pilih lebih dari 1)</small></label>
          <?php 
            $oldCategories = (array)($oldInput['category'] ?? []); 
          ?>
          <div class="input-group">
            <select class="form-select select2 <?php if ($validation->hasError('category')) : ?>is-invalid<?php endif ?>" aria-label="Select category" id="category" name="category[]" multiple required>
              <?php foreach ($categories as $category) : ?>
                <option value="<?= $category['id']; ?>" <?= in_array($category['id'], $oldCategories) ? 'selected' : ''; ?>><?= $category['name']; ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-primary btn-add-inline" data-bs-toggle="modal" data-bs-target="#addCategoryModal" title="Tambah Kategori Baru">
              <i class="ti ti-plus fs-5"></i>
            </button>
          </div>
          <div class="invalid-feedback d-block">
            <?= $validation->getError('category'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="ddc" class="form-label mb-1">Kode DDC <small class="text-muted fw-normal">(Klasifikasi Subjek, misal: 005.75 atau 297)</small></label>
          <input type="text" class="form-control <?php if ($validation->hasError('ddc')) : ?>is-invalid<?php endif ?>" id="ddc" name="ddc" value="<?= esc($oldInput['ddc'] ?? ''); ?>" placeholder="Ex. 005.75/85-22">
          <div class="invalid-feedback d-block">
            <?= $validation->getError('ddc'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="call_number" class="form-label mb-1">Nomor Panggil <small class="text-muted fw-normal">(Otomatis terbuat atau sesuaikan)</small></label>
          <input type="text" class="form-control <?php if ($validation->hasError('call_number')) : ?>is-invalid<?php endif ?>" id="call_number" name="call_number" value="<?= esc($oldInput['call_number'] ?? ''); ?>" placeholder="Ex. 005.75 Kur p">
          <div class="invalid-feedback d-block">
            <?= $validation->getError('call_number'); ?>
          </div>
        </div>
        <div class="col-12 mb-3">
          <label for="synopsis" class="form-label mb-1">Sinopsis / Blurb Buku <small class="text-muted fw-normal">(Deskripsi / Ringkasan Singkat Isi Buku)</small></label>
          <textarea class="form-control <?php if ($validation->hasError('synopsis')) : ?>is-invalid<?php endif ?>" id="synopsis" name="synopsis" rows="4" placeholder="Tuliskan sinopsis, blurb, atau ringkasan singkat isi buku di sini..."><?= esc($oldInput['synopsis'] ?? ''); ?></textarea>
          <div class="invalid-feedback d-block">
            <?= $validation->getError('synopsis'); ?>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary px-4 fw-bold mt-2">Simpan Buku</button>
    </form>
  </div>
</div>

<!-- Modal Tambah Pengarang -->
<div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="addAuthorModalLabel"><i class="ti ti-user-plus text-primary me-2"></i>Tambah Pengarang Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <div id="authorModalAlert" style="display:none;"></div>
        <div class="mb-3">
          <label for="new_author_name" class="form-label fw-semibold">Nama Pengarang / Penulis</label>
          <input type="text" class="form-control" id="new_author_name" placeholder="Contoh: Tere Liye">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btnSaveNewAuthor">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Penerbit -->
<div class="modal fade" id="addPublisherModal" tabindex="-1" aria-labelledby="addPublisherModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="addPublisherModalLabel"><i class="ti ti-building text-primary me-2"></i>Tambah Penerbit Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <div id="publisherModalAlert" style="display:none;"></div>
        <div class="mb-3">
          <label for="new_publisher_name" class="form-label fw-semibold">Nama Penerbit</label>
          <input type="text" class="form-control" id="new_publisher_name" placeholder="Contoh: Gramedia">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btnSaveNewPublisher">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="addCategoryModalLabel"><i class="ti ti-category text-primary me-2"></i>Tambah Kategori Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <div id="categoryModalAlert" style="display:none;"></div>
        <div class="mb-3">
          <label for="new_category_name" class="form-label fw-semibold">Nama Kategori</label>
          <input type="text" class="form-control" id="new_category_name" placeholder="Contoh: Novel, Sains, Agama">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btnSaveNewCategory">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah Rak -->
<div class="modal fade" id="addRackModal" tabindex="-1" aria-labelledby="addRackModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="addRackModalLabel"><i class="ti ti-layout-grid text-primary me-2"></i>Tambah Rak Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-3">
        <div id="rackModalAlert" style="display:none;"></div>
        <div class="mb-3">
          <label for="new_rack_name" class="form-label fw-semibold">Nama / Kode Rak</label>
          <input type="text" class="form-control" id="new_rack_name" placeholder="Contoh: A-1, B-2">
        </div>
        <div class="mb-3">
          <label for="new_rack_floor" class="form-label fw-semibold">Lantai</label>
          <input type="text" class="form-control" id="new_rack_floor" placeholder="Contoh: 1, 2" value="1">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary fw-bold px-4" id="btnSaveNewRack">Simpan</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function getCsrfToken() {
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) return { name: metaToken.getAttribute('name'), value: metaToken.getAttribute('content') };
    const inputToken = document.querySelector('input[name^="csrf_"]');
    if (inputToken) return { name: inputToken.getAttribute('name'), value: inputToken.value };
    return { name: '<?= csrf_token(); ?>', value: '<?= csrf_hash(); ?>' };
  }
  function previewImage() {
    const fileInput = document.querySelector('#cover');
    const imagePreview = document.querySelector('#bookCoverPreview');

    if (fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      reader.readAsDataURL(fileInput.files[0]);

      reader.onload = function(e) {
        imagePreview.src = e.target.result;
      };
    }
  }

  function initIsbnFocusAndEvents() {
    const isbnInput = document.querySelector('#isbn');
    const btnFetch = document.querySelector('#btnFetchIsbn');

    if (isbnInput) {
      setTimeout(function() {
        isbnInput.focus();
        if (isbnInput.value) {
          isbnInput.select();
        }
      }, 100);

      isbnInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          fetchBookByIsbn();
        }
      });
    }

    if (btnFetch) {
      btnFetch.onclick = function(e) {
        e.preventDefault();
        fetchBookByIsbn();
      };
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initIsbnFocusAndEvents);
  } else {
    initIsbnFocusAndEvents();
  }

  async function fetchBookByIsbn() {
    const isbnInput = document.querySelector('#isbn');
    const rawVal = isbnInput ? isbnInput.value : '';
    const isbnVal = rawVal.replace(/[^0-9X]/gi, '');
    const btn = document.querySelector('#btnFetchIsbn');
    const btnText = document.querySelector('#isbnBtnText');
    const icon = document.querySelector('#isbnSearchIcon');

    if (!isbnVal || (isbnVal.length !== 10 && isbnVal.length !== 13)) {
      showIsbnAlert('danger', 'Silakan masukkan nomor ISBN 10 atau 13 digit terlebih dahulu.');
      return;
    }

    // Set loading state
    if (btn) btn.disabled = true;
    if (btnText) btnText.textContent = 'Mencari...';
    if (icon) icon.className = 'spinner-border spinner-border-sm me-1';

    showIsbnAlert('info', 'Sedang mencari data buku dari internet (ISBNSearch / Google Books)...');

    try {
      const endpoint = `<?= base_url('admin/books/lookup-isbn'); ?>?isbn=${isbnVal}`;
      const res = await fetch(endpoint);
      const result = await res.json();

      if (result.status && result.data) {
        const d = result.data;
        populateBookForm(d.title, d.author, d.publisher, d.year, d.cover_url ? { thumbnail: d.cover_url } : null, d.author_id, d.publisher_id);
        showIsbnAlert('success', `✨ Data buku berhasil ditemukan via <strong>${result.source || 'Database ISBN'}</strong> & diisi otomatis!`);
        return;
      }

      // Client-side direct OpenLibrary / Google Books fallback if backend returns false
      showIsbnAlert('info', 'Mencoba pencarian langsung dari API Publik...');
      const olRes = await fetch(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbnVal}&format=json&jscmd=data`);
      const olData = await olRes.json();
      const olKey = `ISBN:${isbnVal}`;
      if (olData && olData[olKey]) {
        const item = olData[olKey];
        const title = item.title || '';
        const author = item.authors ? item.authors.map(a => a.name).join(', ') : '';
        const publisher = item.publishers ? item.publishers.map(p => p.name).join(', ') : '';
        const year = item.publish_date ? (item.publish_date.match(/\b\d{4}\b/) || [])[0] : '';
        const coverUrl = item.cover ? (item.cover.large || item.cover.medium || item.cover.small) : null;
        populateBookForm(title, author, publisher, year, coverUrl ? { thumbnail: coverUrl } : null);
        showIsbnAlert('success', `✨ Data buku berhasil ditemukan via <strong>OpenLibrary</strong> & diisi otomatis!`);
        return;
      }

      showIsbnAlert('warning', result.message || 'Buku tidak ditemukan di database publik online. Silakan isi form secara manual.');
    } catch (err) {
      console.error(err);
      showIsbnAlert('danger', 'Gagal terhubung ke server pencarian ISBN. Silakan periksa koneksi internet Anda atau isi form secara manual.');
    } finally {
      resetIsbnBtn();
    }
  }

  function populateBookForm(title, author, publisher, publishedDate, imageLinks, authorId, publisherId) {
    if (title) {
      const titleEl = document.querySelector('#title');
      if (titleEl) titleEl.value = title;
    }

    if (author && typeof $ !== 'undefined') {
      let authorEl = $('#author_id');
      const authorList = author.split(',').map(s => s.trim()).filter(Boolean);
      let selectedValues = [];

      authorList.forEach(authName => {
        let foundVal = null;
        const normAuthor = authName.replace(/[\s\-]+/g, ' ').toLowerCase();
        authorEl.find('option').each(function() {
          const optText = $(this).text().replace(/[\s\-]+/g, ' ').trim().toLowerCase();
          if (optText === normAuthor && $(this).val()) {
            foundVal = $(this).val();
          }
        });

        if (foundVal) {
          selectedValues.push(foundVal);
        } else {
          let newOption = new Option(authName, authName, true, true);
          authorEl.append(newOption);
          selectedValues.push(authName);
        }
      });
      authorEl.val(selectedValues).trigger('change');
    }

    if (publisher && typeof $ !== 'undefined') {
      let publisherEl = $('#publisher_id');
      const firstPubName = publisher.split(',')[0].trim();
      let foundVal = null;

      if (publisherId) {
        foundVal = publisherId;
      } else if (firstPubName) {
        const normPublisher = firstPubName.replace(/[\s\-]+/g, ' ').toLowerCase();
        publisherEl.find('option').each(function() {
          const optText = $(this).text().replace(/[\s\-]+/g, ' ').trim().toLowerCase();
          if (optText === normPublisher && $(this).val()) {
            foundVal = $(this).val();
          }
        });
      }

      if (foundVal) {
        publisherEl.val(foundVal).trigger('change');
      } else if (firstPubName) {
        // Hapus option dummy jika ada, lalu tambahkan option baru dengan value unik
        publisherEl.find('option[data-ai-temp]').remove();
        let newOption = new Option(firstPubName, firstPubName, false, false);
        $(newOption).attr('data-ai-temp', '1');
        publisherEl.append(newOption);
        publisherEl.val(firstPubName).trigger('change');
      }
    }

    if (publishedDate) {
      const yearMatch = publishedDate.toString().match(/\b\d{4}\b/);
      if (yearMatch) {
        const yearEl = document.querySelector('#year');
        if (yearEl) yearEl.value = yearMatch[0];
      }
    }

    if (imageLinks && imageLinks.thumbnail) {
      const coverUrl = imageLinks.thumbnail.replace(/^http:/, 'https:');
      const imagePreview = document.querySelector('#bookCoverPreview');
      const coverUrlInput = document.querySelector('#cover_url');
      if (imagePreview) {
        imagePreview.src = coverUrl;
      }
      if (coverUrlInput) {
        coverUrlInput.value = coverUrl;
      }
    }
  }

  function showIsbnAlert(type, msg) {
    const alertDiv = document.querySelector('#isbnAlert');
    if (!alertDiv) return;
    alertDiv.style.display = 'block';
    alertDiv.className = `alert alert-${type} alert-dismissible fade show p-2 small mt-2`;
    alertDiv.innerHTML = `${msg} <button type="button" class="btn-close p-1" data-bs-dismiss="alert" aria-label="Close"></button>`;
  }

  function resetIsbnBtn() {
    const btn = document.querySelector('#btnFetchIsbn');
    const btnText = document.querySelector('#isbnBtnText');
    const icon = document.querySelector('#isbnSearchIcon');
    if (btn) btn.disabled = false;
    if (btnText) btnText.textContent = 'Cari via ISBN';
    if (icon) icon.className = 'ti ti-search me-1';
  }

  $(document).on('click', '#btnSaveNewAuthor', async function() {
    const input = document.querySelector('#new_author_name');
    const val = input ? input.value.trim() : '';
    const alertDiv = document.querySelector('#authorModalAlert');
    if (!val) {
      if (alertDiv) {
        alertDiv.style.display = 'block';
        alertDiv.className = 'alert alert-danger p-2 small mb-3';
        alertDiv.textContent = 'Nama pengarang tidak boleh kosong.';
      }
      return;
    }
    try {
      const csrfToken = getCsrfToken();
      const formData = new FormData();
      formData.append('name', val);
      formData.append(csrfToken.name, csrfToken.value);
      const res = await fetch('<?= base_url('admin/authors'); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });
      let result;
      const textResponse = await res.text();
      try {
        result = JSON.parse(textResponse);
      } catch(e) {
        console.error("Response not JSON:", textResponse);
        if (alertDiv) {
          alertDiv.style.display = 'block';
          alertDiv.className = 'alert alert-danger p-2 small mb-3';
          alertDiv.textContent = 'Terjadi kesalahan sistem saat menyimpan pengarang.';
        }
        return;
      }

      if (result.status && result.data) {
        const d = result.data;
        let authorEl = $('#author_id');
        if (!authorEl.find("option[value='" + d.id + "']").length) {
          let newOption = new Option(d.name, d.id, true, true);
          authorEl.append(newOption).trigger('change');
        } else {
          authorEl.val(d.id).trigger('change');
        }
        input.value = '';
        if (alertDiv) alertDiv.style.display = 'none';
        const modalEl = document.querySelector('#addAuthorModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
      } else {
        if (alertDiv) {
          alertDiv.style.display = 'block';
          alertDiv.className = 'alert alert-danger p-2 small mb-3';
          alertDiv.textContent = result.message || 'Gagal menyimpan pengarang.';
        }
      }
    } catch(err) {
      console.error(err);
    }
  });

  $(document).on('click', '#btnSaveNewPublisher', async function() {
    const input = document.querySelector('#new_publisher_name');
    const val = input ? input.value.trim() : '';
    const alertDiv = document.querySelector('#publisherModalAlert');
    if (!val) {
      if (alertDiv) {
        alertDiv.style.display = 'block';
        alertDiv.className = 'alert alert-danger p-2 small mb-3';
        alertDiv.textContent = 'Nama penerbit tidak boleh kosong.';
      }
      return;
    }
    try {
      const csrfToken = getCsrfToken();
      const formData = new FormData();
      formData.append('name', val);
      formData.append(csrfToken.name, csrfToken.value);
      const res = await fetch('<?= base_url('admin/publishers'); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });
      const result = await res.json();
      if (result.status && result.data) {
        const d = result.data;
        let publisherEl = $('#publisher_id');
        if (!publisherEl.find("option[value='" + d.id + "']").length) {
          let newOption = new Option(d.name, d.id, true, true);
          publisherEl.append(newOption).trigger('change');
        } else {
          publisherEl.val(d.id).trigger('change');
        }
        input.value = '';
        if (alertDiv) alertDiv.style.display = 'none';
        const modalEl = document.querySelector('#addPublisherModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
      } else {
        if (alertDiv) {
          alertDiv.style.display = 'block';
          alertDiv.className = 'alert alert-danger p-2 small mb-3';
          alertDiv.textContent = result.message || 'Gagal menyimpan penerbit.';
        }
      }
    } catch(err) {
      console.error(err);
    }
  });

  $(document).on('click', '#btnSaveNewCategory', async function() {
    const input = document.querySelector('#new_category_name');
    const val = input ? input.value.trim() : '';
    const alertDiv = document.querySelector('#categoryModalAlert');
    if (!val) {
      if (alertDiv) {
        alertDiv.style.display = 'block';
        alertDiv.className = 'alert alert-danger p-2 small mb-3';
        alertDiv.textContent = 'Nama kategori tidak boleh kosong.';
      }
      return;
    }
    try {
      const csrfToken = getCsrfToken();
      const formData = new FormData();
      formData.append('category', val);
      formData.append(csrfToken.name, csrfToken.value);
      const res = await fetch('<?= base_url('admin/categories'); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });
      const result = await res.json();
      if (result.status && result.data) {
        const d = result.data;
        let catEl = $('#category');
        if (!catEl.find("option[value='" + d.id + "']").length) {
          let newOption = new Option(d.name, d.id, true, true);
          catEl.append(newOption).trigger('change');
        } else {
          catEl.val(d.id).trigger('change');
        }
        input.value = '';
        if (alertDiv) alertDiv.style.display = 'none';
        const modalEl = document.querySelector('#addCategoryModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
      } else {
        if (alertDiv) {
          alertDiv.style.display = 'block';
          alertDiv.className = 'alert alert-danger p-2 small mb-3';
          alertDiv.textContent = result.message || 'Gagal menyimpan kategori.';
        }
      }
    } catch(err) {
      console.error(err);
    }
  });

  $(document).on('click', '#btnSaveNewRack', async function() {
    const inputName = document.querySelector('#new_rack_name');
    const inputFloor = document.querySelector('#new_rack_floor');
    const nameVal = inputName ? inputName.value.trim() : '';
    const floorVal = inputFloor ? inputFloor.value.trim() : '1';
    const alertDiv = document.querySelector('#rackModalAlert');
    if (!nameVal) {
      if (alertDiv) {
        alertDiv.style.display = 'block';
        alertDiv.className = 'alert alert-danger p-2 small mb-3';
        alertDiv.textContent = 'Nama/Kode rak tidak boleh kosong.';
      }
      return;
    }
    try {
      const csrfToken = getCsrfToken();
      const formData = new FormData();
      formData.append('rack', nameVal);
      formData.append('floor', floorVal);
      formData.append(csrfToken.name, csrfToken.value);
      const res = await fetch('<?= base_url('admin/racks'); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });
      const result = await res.json();
      if (result.status && result.data) {
        const d = result.data;
        let rackEl = $('#rack');
        if (!rackEl.find("option[value='" + d.id + "']").length) {
          let newOption = new Option(d.name, d.id, true, true);
          rackEl.append(newOption).trigger('change');
        } else {
          rackEl.val(d.id).trigger('change');
        }
        inputName.value = '';
        if (alertDiv) alertDiv.style.display = 'none';
        const modalEl = document.querySelector('#addRackModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('overflow', '');
      } else {
        if (alertDiv) {
          alertDiv.style.display = 'block';
          alertDiv.className = 'alert alert-danger p-2 small mb-3';
          alertDiv.textContent = result.message || 'Gagal menyimpan rak.';
        }
      }
    } catch(err) {
      console.error(err);
    }
  });

  // Handler Pencarian via AI & Multi-Source Engine
  function triggerAiSearch() {
    const queryInput = document.querySelector('#aiQueryInput');
    const query = queryInput ? queryInput.value.trim() : '';
    if (!query) {
      alert('Silakan ketik atau scan Barcode ISBN / Judul Buku terlebih dahulu!');
      return;
    }

    const btn = $('#btnAiSearch');
    const icon = $('#aiSearchIcon');
    const text = $('#aiBtnText');
    const alertBox = $('#aiSearchAlert');
    const loadingBox = $('#aiSearchLoadingBox');

    btn.prop('disabled', true);
    icon.removeClass('ti-sparkles').addClass('ti-loader rotate-spin');
    text.text('AI Menjawab...');
    alertBox.hide();
    
    $('#aiQueryPromptDisplay').text(query);
    loadingBox.slideDown(200);

    fetch('<?= base_url('admin/books/lookup-ai'); ?>?query=' + encodeURIComponent(query))
      .then(res => res.json())
      .then(res => {
        btn.prop('disabled', false);
        icon.removeClass('ti-loader rotate-spin').addClass('ti-sparkles');
        text.text('Cari via AI & Kover');
        loadingBox.slideUp(150);

        if (res.status && res.data) {
          const d = res.data;
          const isbnOnly = res.isbn_only === true;

          // Gunakan fungsi populateBookForm yang sudah lengkap dan terbukti benar
          populateBookForm(
            d.title || '',
            d.author || '',
            d.publisher || '',
            d.year ? String(d.year) : '',
            d.cover_url ? { thumbnail: d.cover_url } : null,
            d.author_id || null,
            d.publisher_id || null
          );

          // Isi ISBN secara langsung
          if (d.isbn) $('#isbn').val(d.isbn);

          // Isi Kategori
          if (d.category_id) {
            $('#category').val(d.category_id).trigger('change');
          }

          // Isi Kode DDC
          if (d.ddc) {
            $('#ddc').val(d.ddc);
            // Trigger call_number auto-fill if exists
            if (typeof updateCallNumber === 'function') updateCallNumber();
          }

          // Isi Sinopsis
          if (d.synopsis) {
            $('#synopsis').val(d.synopsis);
          }

          if (isbnOnly) {
            alertBox.removeClass('alert-success alert-danger')
              .addClass('alert alert-warning rounded-3 p-3 shadow-sm')
              .html('<div class="d-flex align-items-center gap-2"><i class="ti ti-info-circle fs-6 text-warning"></i><div><strong>ISBN <code>' + query + '</code> ditemukan dari barcode scan.</strong><br>Data buku tidak ditemukan di database publik online. Silakan isi <strong>Judul, Pengarang, Penerbit &amp; Tahun</strong> secara manual.</div></div>')
              .slideDown(200);
          } else {
            alertBox.removeClass('alert-danger alert-warning')
              .addClass('alert alert-success rounded-3 p-3 shadow-sm')
              .html('<div class="d-flex align-items-center gap-2"><i class="ti ti-circle-check text-success fs-6"></i> <div><strong>✨ AI Berhasil Menjawab!</strong> Seluruh data <em>(Judul, Pengarang, Penerbit, Tahun, Genre, &amp; Kover)</em> dari "<strong>' + query + '</strong>" telah otomatis dimasukkan ke form di bawah.</div></div>')
              .slideDown(200);
          }
        } else {
          alertBox.removeClass('alert-success alert-warning')
            .addClass('alert alert-danger rounded-3 p-3')
            .html('<i class="ti ti-alert-triangle me-1 fs-4"></i> ' + (res.message || 'Data tidak ditemukan oleh AI.'))
            .slideDown(200);
        }
      })
      .catch(err => {
        console.error(err);
        loadingBox.slideUp(150);
        btn.prop('disabled', false);
        icon.removeClass('ti-loader rotate-spin').addClass('ti-sparkles');
        text.text('Cari via AI & Kover');
        alertBox.removeClass('alert-success alert-warning')
          .addClass('alert alert-danger rounded-3 p-3')
          .html('<i class="ti ti-alert-circle me-1 fs-4"></i> Terjadi kesalahan saat menghubungi server AI.')
          .slideDown(200);
      });
  }

  $(document).on('click', '#btnAiSearch', function(e) {
    e.preventDefault();
    triggerAiSearch();
  });

  $(document).on('keydown keypress', '#aiQueryInput', function(e) {
    if (e.which === 13 || e.keyCode === 13 || e.key === 'Enter') {
      e.preventDefault();
      triggerAiSearch();
    }
  });

  // Auto focus ke kolom AI search saat halaman dibuka (siap scan barcode)
  setTimeout(function() {
    $('#aiQueryInput').focus().select();
  }, 250);

  // Auto Generate Nomor Panggil (Call Number)
  function generateCallNumber() {
    if ($('#call_number').data('user-edited') === true) return;

    let ddc = $('#ddc').val() ? $('#ddc').val().trim() : '';
    let authorVal = '';
    let selectedAuthorOpt = $('#author_id option:selected').first();
    if (selectedAuthorOpt.length && selectedAuthorOpt.val()) {
      authorVal = selectedAuthorOpt.text().trim();
    }

    let titleVal = $('#title').val() ? $('#title').val().trim() : '';

    if (!ddc && !authorVal && !titleVal) return;

    let authorCode = '';
    if (authorVal) {
      let cleanAuthor = authorVal.replace(/^[^a-zA-Z0-9]+/, '');
      if (cleanAuthor.length >= 3) {
        authorCode = cleanAuthor.substring(0, 3);
        authorCode = authorCode.charAt(0).toUpperCase() + authorCode.slice(1).toLowerCase();
      } else if (cleanAuthor.length > 0) {
        authorCode = cleanAuthor.charAt(0).toUpperCase() + cleanAuthor.slice(1).toLowerCase();
      }
    }

    let titleCode = '';
    if (titleVal) {
      let words = titleVal.trim().split(/\s+/);
      if (words.length > 0 && words[0].length > 0) {
        let firstChar = words[0].charAt(0).toLowerCase();
        if (/[a-z0-9]/i.test(firstChar)) {
          titleCode = firstChar;
        }
      }
    }

    let result = [];
    if (ddc) result.push(ddc);
    if (authorCode) result.push(authorCode);
    if (titleCode) result.push(titleCode);

    if (result.length > 0) {
      $('#call_number').val(result.join(' '));
    }
  }

  $(document).on('input change', '#ddc, #title', generateCallNumber);
  $(document).on('change', '#author_id', generateCallNumber);
  $(document).on('input', '#call_number', function() {
    if ($(this).val().trim() !== '') {
      $(this).data('user-edited', true);
    } else {
      $(this).data('user-edited', false);
    }
  });

  // Auto Map Category to DDC Code
  const ddcCategoryMap = {
    'agama': '297',
    'islam': '297',
    'fiqih': '297',
    'hadits': '297',
    'quran': '297',
    'novel': '813',
    'fiksi': '813',
    'sastra': '813',
    'komputer': '005.75',
    'pemrograman': '005.75',
    'teknologi': '600',
    'matematika': '500',
    'sains': '500',
    'sejarah': '900',
    'bahasa': '400',
    'hukum': '300',
    'ekonomi': '300',
    'filsafat': '100',
    'seni': '700'
  };

  $(document).on('change', '#category', function() {
    let currentDdc = $('#ddc').val() ? $('#ddc').val().trim() : '';
    if (!currentDdc) {
      let selectedTexts = $(this).find('option:selected').map(function() { return $(this).text().toLowerCase(); }).get();
      for (let text of selectedTexts) {
        for (let key in ddcCategoryMap) {
          if (text.includes(key)) {
            $('#ddc').val(ddcCategoryMap[key]).trigger('change');
            return;
          }
        }
      }
    }
  });
</script>
<?= $this->endSection() ?>