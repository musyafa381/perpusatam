<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Edit Buku</title>
<style>
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
<?php $oldInput = $oldInput ?? (session('_ci_old_input')['post'] ?? []); ?>
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

<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-3">Form Edit Buku</h5>
    <form action="<?= base_url('admin/books/update/' . $book['slug']); ?>" method="post" enctype="multipart/form-data" data-no-pjax>
      <?= csrf_field(); ?>
      <input type="hidden" name="cover_url" id="cover_url" value="">
      <div class="row">
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3 p-3">
          <label for="cover" class="d-block" style="cursor: pointer;">
            <div class="d-flex justify-content-center bg-light overflow-hidden h-100 position-relative">
              <?php
              $coverImageUrl = getBookCover($book['book_cover'] ?? '');
              ?>
              <img id="bookCoverPreview" src="<?= $coverImageUrl; ?>" alt="" height="300" class="z-1">
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
            <input type="text" class="form-control <?php if ($validation->hasError('title')) : ?>is-invalid<?php endif ?>" id="title" name="title" value="<?= $oldInput['title'] ?? $book['title']; ?>" required>
            <div class="invalid-feedback">
              <?= $validation->getError('title'); ?>
            </div>
          </div>
          <div class="mb-3">
            <label for="author_id" class="form-label mb-1">Pengarang / Penulis <small class="text-muted fw-normal">(Bisa pilih lebih dari 1)</small></label>
            <?php 
              $currentAuthors = array_map('trim', explode(',', $book['author'] ?? ''));
              $oldAuthors = (array)($oldInput['author_id'] ?? []); 
            ?>
            <div class="input-group">
              <select class="form-select select2 <?php if ($validation->hasError('author_id')) : ?>is-invalid<?php endif ?>" id="author_id" name="author_id[]" multiple required>
                <?php foreach ($authors as $author) : ?>
                  <option value="<?= $author['id']; ?>" <?= (in_array($author['id'], $oldAuthors) || ($book['author_id'] == $author['id']) || in_array($author['name'], $currentAuthors)) ? 'selected' : ''; ?>><?= esc($author['name']); ?></option>
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
              $oldPublisher = $oldInput['publisher_id'] ?? $book['publisher_id']; 
            ?>
            <div class="input-group">
              <select class="form-select select2 <?php if ($validation->hasError('publisher_id')) : ?>is-invalid<?php endif ?>" id="publisher_id" name="publisher_id" required>
                <option value="" disabled>--Pilih penerbit--</option>
                <?php foreach ($publishers as $publisher) : ?>
                  <option value="<?= $publisher['id']; ?>" <?= ($oldPublisher == $publisher['id'] || $book['publisher_id'] == $publisher['id']) ? 'selected' : ''; ?>><?= esc($publisher['name']); ?></option>
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
          <input type="text" class="form-control <?php if ($validation->hasError('isbn')) : ?>is-invalid<?php endif ?>" id="isbn" name="isbn" minlength="10" maxlength="13" aria-describedby="isbnHelp" value="<?= $oldInput['isbn'] ?? $book['isbn']; ?>" required>
          <div id="isbnHelp" class="form-text">
            ISBN harus 10-13 karakter angka.
          </div>
          <div class="invalid-feedback">
            <?= $validation->getError('isbn'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="year" class="form-label">Tahun terbit</label>
          <input type="number" class="form-control <?php if ($validation->hasError('year')) : ?>is-invalid<?php endif ?>" id="year" name="year" minlength="4" maxlength="4" value="<?= $oldInput['year'] ?? $book['year']; ?>" required>
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
              <option value="" disabled>--Pilih rak--</option>
              <?php foreach ($racks as $rack) : ?>
                <option value="<?= $rack['id']; ?>" <?= ($oldInput['rack'] ?? $book['rack_id']) == $rack['id'] ? 'selected' : ''; ?>><?= $rack['name']; ?></option>
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
            $currentCategoryId = $book['category_id'] ?? null;
          ?>
          <div class="input-group">
            <select class="form-select select2 <?php if ($validation->hasError('category')) : ?>is-invalid<?php endif ?>" aria-label="Select category" id="category" name="category[]" multiple required>
              <?php foreach ($categories as $category) : ?>
                <option value="<?= $category['id']; ?>" <?= (in_array($category['id'], $oldCategories) || ($currentCategoryId == $category['id'])) ? 'selected' : ''; ?>><?= $category['name']; ?></option>
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
          <input type="text" class="form-control <?php if ($validation->hasError('ddc')) : ?>is-invalid<?php endif ?>" id="ddc" name="ddc" value="<?= esc($oldInput['ddc'] ?? $book['ddc'] ?? ''); ?>" placeholder="Ex. 005.75/85-22">
          <div class="invalid-feedback d-block">
            <?= $validation->getError('ddc'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="call_number" class="form-label mb-1">Nomor Panggil <small class="text-muted fw-normal">(Otomatis terbuat atau sesuaikan)</small></label>
          <input type="text" class="form-control <?php if ($validation->hasError('call_number')) : ?>is-invalid<?php endif ?>" id="call_number" name="call_number" value="<?= esc($oldInput['call_number'] ?? $book['call_number'] ?? ''); ?>" placeholder="Ex. 005.75 Kur p">
          <div class="invalid-feedback d-block">
            <?= $validation->getError('call_number'); ?>
          </div>
        </div>
        <div class="col-12 mb-3">
          <label for="synopsis" class="form-label mb-1">Sinopsis / Blurb Buku <small class="text-muted fw-normal">(Deskripsi / Ringkasan Singkat Isi Buku)</small></label>
          <textarea class="form-control <?php if ($validation->hasError('synopsis')) : ?>is-invalid<?php endif ?>" id="synopsis" name="synopsis" rows="4" placeholder="Tuliskan sinopsis, blurb, atau ringkasan singkat isi buku di sini..."><?= esc($oldInput['synopsis'] ?? $book['synopsis'] ?? ''); ?></textarea>
          <div class="invalid-feedback d-block">
            <?= $validation->getError('synopsis'); ?>
          </div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
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
  function previewImage(inputEl) {
    const fileInput = inputEl || document.querySelector('#cover');
    const imagePreview = document.querySelector('#bookCoverPreview');

    if (fileInput && fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        if (imagePreview) {
          imagePreview.src = e.target.result;
          imagePreview.style.display = 'block';
        }
      };
      reader.readAsDataURL(fileInput.files[0]);
    }
  }

  $(document).on('change', '#cover', function() {
    previewImage(this);
  });

  function getCsrfToken() {
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    if (metaToken) return { name: metaToken.getAttribute('name'), value: metaToken.getAttribute('content') };
    const inputToken = document.querySelector('input[name^="csrf_"]');
    if (inputToken) return { name: inputToken.getAttribute('name'), value: inputToken.value };
    return { name: '<?= csrf_token(); ?>', value: '<?= csrf_hash(); ?>' };
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
      const result = await res.json();
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