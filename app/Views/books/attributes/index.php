<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Master Atribut Buku</title>
<style>
  .attr-nav .nav-link {
    color: #6e4727 !important;
    background-color: #f7f3ed !important;
    border: 1.5px solid #e2d5c3 !important;
    border-radius: 12px !important;
    font-weight: 600 !important;
    padding: 10px 20px !important;
    transition: all 0.25s ease;
  }
  .attr-nav .nav-link:hover {
    background-color: #eee4d5 !important;
  }
  .attr-nav .nav-link.active {
    background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%) !important;
    color: #ffffff !important;
    border-color: #6e4727 !important;
    box-shadow: 0 4px 12px rgba(110, 71, 39, 0.25) !important;
  }
  .table-custom-attr th {
    background-color: #faf6f0 !important;
    color: #5a4636 !important;
    font-weight: 700 !important;
    font-size: 0.85rem !important;
    border-bottom: 1.5px solid #e8decb !important;
  }
  .search-pill-input {
    border: 1.5px solid #dcd1be !important;
    background-color: #fcfaf7 !important;
    font-size: 0.85rem !important;
    transition: all 0.2s ease;
  }
  .search-pill-input:focus {
    border-color: #8b5e3c !important;
    background-color: #ffffff !important;
    box-shadow: 0 0 0 0.2rem rgba(139, 94, 60, 0.15) !important;
  }
  .attr-pagination .page-item .page-link {
    color: #6e4727 !important;
    border-color: #e8decb !important;
    background-color: #ffffff !important;
    border-radius: 8px !important;
    margin: 0 2px !important;
    font-size: 0.825rem !important;
    font-weight: 600 !important;
    padding: 6px 12px !important;
    transition: all 0.2s ease;
  }
  .attr-pagination .page-item .page-link:hover {
    background-color: #f7f3ed !important;
    color: #5a381e !important;
    border-color: #8b5e3c !important;
  }
  .attr-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%) !important;
    color: #ffffff !important;
    border-color: #6e4727 !important;
    box-shadow: 0 2px 6px rgba(110, 71, 39, 0.2) !important;
  }
  .attr-pagination .page-item.disabled .page-link {
    color: #a89f91 !important;
    background-color: #fcfaf7 !important;
    border-color: #ede5d8 !important;
    opacity: 0.7 !important;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
  <div class="col-12">
    <!-- Header Banner -->
    <div class="position-relative overflow-hidden rounded-4 mb-4 p-4 text-white shadow-sm" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%);">
      <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
          <div class="badge bg-white fw-bold px-3 py-1 mb-2 rounded-pill shadow-sm" style="color: #6e4727 !important; font-size: 0.75rem;">
            <i class="ti ti-tags me-1"></i> MASTER DATA METADATA
          </div>
          <h3 class="text-white fw-bold mb-1">Master Atribut Buku</h3>
          <p class="text-white-50 mb-0" style="font-size: 0.9rem;">Kelola Pengarang, Penerbit, Kategori, dan Rak Buku dalam satu tempat terpusat.</p>
        </div>
      </div>
    </div>

    <!-- Alert Flash Notifications -->
    <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success rounded-3 p-3 mb-4 shadow-sm border-0 d-flex align-items-center gap-2">
        <i class="ti ti-circle-check fs-5"></i>
        <div class="fw-semibold"><?= session()->getFlashdata('success'); ?></div>
      </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
      <div class="alert alert-danger rounded-3 p-3 mb-4 shadow-sm border-0 d-flex align-items-center gap-2">
        <i class="ti ti-alert-circle fs-5"></i>
        <div class="fw-semibold"><?= session()->getFlashdata('error'); ?></div>
      </div>
    <?php endif; ?>

    <!-- Main Card Container -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: #ffffff; border: 1.5px solid #e8decb !important;">
      <div class="card-header bg-transparent border-bottom p-3 p-md-4" style="border-color: #e8decb !important;">
        <!-- Navigation Tabs -->
        <ul class="nav nav-pills attr-nav gap-2" id="attributeTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($activeTab === 'authors') ? 'active' : ''; ?>" id="tab-authors-tab" data-bs-toggle="tab" data-bs-target="#tab-authors" type="button" role="tab">
              <i class="ti ti-user-edit me-1.5 fs-5"></i> Pengarang (<?= count($authors); ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($activeTab === 'publishers') ? 'active' : ''; ?>" id="tab-publishers-tab" data-bs-toggle="tab" data-bs-target="#tab-publishers" type="button" role="tab">
              <i class="ti ti-building me-1.5 fs-5"></i> Penerbit (<?= count($publishers); ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($activeTab === 'categories') ? 'active' : ''; ?>" id="tab-categories-tab" data-bs-toggle="tab" data-bs-target="#tab-categories" type="button" role="tab">
              <i class="ti ti-category-2 me-1.5 fs-5"></i> Kategori (<?= count($categories); ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= ($activeTab === 'racks') ? 'active' : ''; ?>" id="tab-racks-tab" data-bs-toggle="tab" data-bs-target="#tab-racks" type="button" role="tab">
              <i class="ti ti-columns me-1.5 fs-5"></i> Rak Buku (<?= count($racks); ?>)
            </button>
          </li>
        </ul>
      </div>

      <div class="card-body p-4">
        <div class="tab-content" id="attributeTabsContent">

          <!-- TAB 1: PENGARANG -->
          <div class="tab-pane fade <?= ($activeTab === 'authors') ? 'show active' : ''; ?>" id="tab-authors" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <h5 class="fw-bold mb-0 text-dark">Daftar Pengarang / Penulis Buku</h5>
              <div class="d-flex align-items-center gap-2">
                <div class="position-relative" style="width: 240px;">
                  <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="z-index: 5; pointer-events: none;"></i>
                  <input type="search" class="form-control form-control-sm rounded-pill ps-5 pe-3 search-pill-input filter-table-input" placeholder="Cari pengarang..." data-table="tableAuthors">
                </div>
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddAuthor" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none;">
                  <i class="ti ti-plus me-1"></i> Tambah Pengarang
                </button>
              </div>
            </div>

            <div class="table-responsive rounded-3 border">
              <table class="table table-hover align-middle table-custom-attr mb-0" id="tableAuthors">
                <thead>
                  <tr>
                    <th class="ps-3" style="width: 60px;">#</th>
                    <th>Nama Pengarang</th>
                    <th class="text-end pe-3" style="width: 160px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($authors)): ?>
                    <tr>
                      <td colspan="3" class="text-center py-4 text-muted fs-7">Belum ada data pengarang.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($authors as $idx => $a): ?>
                      <tr class="attr-row">
                        <td class="ps-3 fw-bold text-muted"><?= $idx + 1; ?></td>
                        <td class="fw-bold text-dark attr-name"><?= esc($a['name']); ?></td>
                        <td class="text-end pe-3">
                          <button type="button" class="btn btn-sm btn-outline-warning rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#modalEditAuthor_<?= $a['id']; ?>">
                            <i class="ti ti-edit"></i> Edit
                          </button>
                          <a href="<?= base_url('admin/book-attributes/authors/delete/' . $a['id']); ?>" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Apakah Anda yakin ingin menghapus pengarang ini?')">
                            <i class="ti ti-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="table-pagination-container d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3" data-table="tableAuthors"></div>
          </div>

          <!-- TAB 2: PENERBIT -->
          <div class="tab-pane fade <?= ($activeTab === 'publishers') ? 'show active' : ''; ?>" id="tab-publishers" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <h5 class="fw-bold mb-0 text-dark">Daftar Penerbit Buku</h5>
              <div class="d-flex align-items-center gap-2">
                <div class="position-relative" style="width: 240px;">
                  <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="z-index: 5; pointer-events: none;"></i>
                  <input type="search" class="form-control form-control-sm rounded-pill ps-5 pe-3 search-pill-input filter-table-input" placeholder="Cari penerbit..." data-table="tablePublishers">
                </div>
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddPublisher" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none;">
                  <i class="ti ti-plus me-1"></i> Tambah Penerbit
                </button>
              </div>
            </div>

            <div class="table-responsive rounded-3 border">
              <table class="table table-hover align-middle table-custom-attr mb-0" id="tablePublishers">
                <thead>
                  <tr>
                    <th class="ps-3" style="width: 60px;">#</th>
                    <th>Nama Penerbit</th>
                    <th class="text-end pe-3" style="width: 160px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($publishers)): ?>
                    <tr>
                      <td colspan="3" class="text-center py-4 text-muted fs-7">Belum ada data penerbit.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($publishers as $idx => $p): ?>
                      <tr class="attr-row">
                        <td class="ps-3 fw-bold text-muted"><?= $idx + 1; ?></td>
                        <td class="fw-bold text-dark attr-name"><?= esc($p['name']); ?></td>
                        <td class="text-end pe-3">
                          <button type="button" class="btn btn-sm btn-outline-warning rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#modalEditPublisher_<?= $p['id']; ?>">
                            <i class="ti ti-edit"></i> Edit
                          </button>
                          <a href="<?= base_url('admin/book-attributes/publishers/delete/' . $p['id']); ?>" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Apakah Anda yakin ingin menghapus penerbit ini?')">
                            <i class="ti ti-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="table-pagination-container d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3" data-table="tablePublishers"></div>
          </div>

          <!-- TAB 3: KATEGORI -->
          <div class="tab-pane fade <?= ($activeTab === 'categories') ? 'show active' : ''; ?>" id="tab-categories" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <h5 class="fw-bold mb-0 text-dark">Daftar Kategori Buku</h5>
              <div class="d-flex align-items-center gap-2">
                <div class="position-relative" style="width: 240px;">
                  <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="z-index: 5; pointer-events: none;"></i>
                  <input type="search" class="form-control form-control-sm rounded-pill ps-5 pe-3 search-pill-input filter-table-input" placeholder="Cari kategori..." data-table="tableCategories">
                </div>
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddCategory" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none;">
                  <i class="ti ti-plus me-1"></i> Tambah Kategori
                </button>
              </div>
            </div>

            <div class="table-responsive rounded-3 border">
              <table class="table table-hover align-middle table-custom-attr mb-0" id="tableCategories">
                <thead>
                  <tr>
                    <th class="ps-3" style="width: 60px;">#</th>
                    <th>Nama Kategori</th>
                    <th class="text-end pe-3" style="width: 160px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($categories)): ?>
                    <tr>
                      <td colspan="3" class="text-center py-4 text-muted fs-7">Belum ada data kategori.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($categories as $idx => $c): ?>
                      <tr class="attr-row">
                        <td class="ps-3 fw-bold text-muted"><?= $idx + 1; ?></td>
                        <td class="fw-bold text-dark attr-name"><?= esc($c['name']); ?></td>
                        <td class="text-end pe-3">
                          <button type="button" class="btn btn-sm btn-outline-warning rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#modalEditCategory_<?= $c['id']; ?>">
                            <i class="ti ti-edit"></i> Edit
                          </button>
                          <a href="<?= base_url('admin/book-attributes/categories/delete/' . $c['id']); ?>" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                            <i class="ti ti-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="table-pagination-container d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3" data-table="tableCategories"></div>
          </div>

          <!-- TAB 4: RAK BUKU -->
          <div class="tab-pane fade <?= ($activeTab === 'racks') ? 'show active' : ''; ?>" id="tab-racks" role="tabpanel">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
              <h5 class="fw-bold mb-0 text-dark">Daftar Rak & Lokasi Lantai</h5>
              <div class="d-flex align-items-center gap-2">
                <div class="position-relative" style="width: 240px;">
                  <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="z-index: 5; pointer-events: none;"></i>
                  <input type="search" class="form-control form-control-sm rounded-pill ps-5 pe-3 search-pill-input filter-table-input" placeholder="Cari rak..." data-table="tableRacks">
                </div>
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-bold rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddRack" style="background: linear-gradient(135deg, #8b5e3c 0%, #6e4727 100%); border: none;">
                  <i class="ti ti-plus me-1"></i> Tambah Rak
                </button>
              </div>
            </div>

            <div class="table-responsive rounded-3 border">
              <table class="table table-hover align-middle table-custom-attr mb-0" id="tableRacks">
                <thead>
                  <tr>
                    <th class="ps-3" style="width: 60px;">#</th>
                    <th>Nama Rak</th>
                    <th>Lokasi Lantai</th>
                    <th class="text-end pe-3" style="width: 160px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($racks)): ?>
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted fs-7">Belum ada data rak.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($racks as $idx => $r): ?>
                      <tr class="attr-row">
                        <td class="ps-3 fw-bold text-muted"><?= $idx + 1; ?></td>
                        <td class="fw-bold text-dark attr-name"><?= esc($r['name']); ?></td>
                        <td><span class="badge bg-light text-dark border px-2.5 py-1">Lantai <?= esc($r['floor'] ?: '1'); ?></span></td>
                        <td class="text-end pe-3">
                          <button type="button" class="btn btn-sm btn-outline-warning rounded-2 me-1" data-bs-toggle="modal" data-bs-target="#modalEditRack_<?= $r['id']; ?>">
                            <i class="ti ti-edit"></i> Edit
                          </button>
                          <a href="<?= base_url('admin/book-attributes/racks/delete/' . $r['id']); ?>" class="btn btn-sm btn-outline-danger rounded-2" onclick="return confirm('Apakah Anda yakin ingin menghapus rak ini?')">
                            <i class="ti ti-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <div class="table-pagination-container d-flex align-items-center justify-content-between flex-wrap gap-2 mt-3" data-table="tableRacks"></div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- === EDIT MODALS (PLACED OUTSIDE TABLES FOR CLEAN HTML) === -->
<?php foreach ($authors as $a): ?>
  <div class="modal fade" id="modalEditAuthor_<?= $a['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0">
        <form action="<?= base_url('admin/book-attributes/authors/update/' . $a['id']); ?>" method="post">
          <?= csrf_field(); ?>
          <div class="modal-header border-bottom p-3">
            <h5 class="modal-title fw-bold">Edit Pengarang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Pengarang</label>
              <input type="text" class="form-control" name="name" value="<?= esc($a['name']); ?>" required>
            </div>
          </div>
          <div class="modal-footer border-top p-3">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php foreach ($publishers as $p): ?>
  <div class="modal fade" id="modalEditPublisher_<?= $p['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0">
        <form action="<?= base_url('admin/book-attributes/publishers/update/' . $p['id']); ?>" method="post">
          <?= csrf_field(); ?>
          <div class="modal-header border-bottom p-3">
            <h5 class="modal-title fw-bold">Edit Penerbit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Penerbit</label>
              <input type="text" class="form-control" name="name" value="<?= esc($p['name']); ?>" required>
            </div>
          </div>
          <div class="modal-footer border-top p-3">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php foreach ($categories as $c): ?>
  <div class="modal fade" id="modalEditCategory_<?= $c['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0">
        <form action="<?= base_url('admin/book-attributes/categories/update/' . $c['id']); ?>" method="post">
          <?= csrf_field(); ?>
          <div class="modal-header border-bottom p-3">
            <h5 class="modal-title fw-bold">Edit Kategori</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Kategori</label>
              <input type="text" class="form-control" name="name" value="<?= esc($c['name']); ?>" required>
            </div>
          </div>
          <div class="modal-footer border-top p-3">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php foreach ($racks as $r): ?>
  <div class="modal fade" id="modalEditRack_<?= $r['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-4 border-0">
        <form action="<?= base_url('admin/book-attributes/racks/update/' . $r['id']); ?>" method="post">
          <?= csrf_field(); ?>
          <div class="modal-header border-bottom p-3">
            <h5 class="modal-title fw-bold">Edit Rak Buku</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Rak</label>
              <input type="text" class="form-control" name="name" value="<?= esc($r['name']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Posisi Lantai</label>
              <input type="text" class="form-control" name="floor" value="<?= esc($r['floor'] ?: '1'); ?>">
            </div>
          </div>
          <div class="modal-footer border-top p-3">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- === ADD MODALS === -->
<!-- Modal Add Author -->
<div class="modal fade" id="modalAddAuthor" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= base_url('admin/book-attributes/authors/store'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-header border-bottom p-3">
          <h5 class="modal-title fw-bold">Tambah Pengarang Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Pengarang</label>
            <input type="text" class="form-control" name="name" placeholder="Contoh: Tere Liye" required>
          </div>
        </div>
        <div class="modal-footer border-top p-3">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Pengarang</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Add Publisher -->
<div class="modal fade" id="modalAddPublisher" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= base_url('admin/book-attributes/publishers/store'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-header border-bottom p-3">
          <h5 class="modal-title fw-bold">Tambah Penerbit Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Penerbit</label>
            <input type="text" class="form-control" name="name" placeholder="Contoh: Gramedia Pustaka Utama" required>
          </div>
        </div>
        <div class="modal-footer border-top p-3">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Penerbit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Add Category -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= base_url('admin/book-attributes/categories/store'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-header border-bottom p-3">
          <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Kategori</label>
            <input type="text" class="form-control" name="name" placeholder="Contoh: Novel / Fiksi" required>
          </div>
        </div>
        <div class="modal-footer border-top p-3">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Kategori</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Add Rack -->
<div class="modal fade" id="modalAddRack" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0">
      <form action="<?= base_url('admin/book-attributes/racks/store'); ?>" method="post">
        <?= csrf_field(); ?>
        <div class="modal-header border-bottom p-3">
          <h5 class="modal-title fw-bold">Tambah Rak Buku Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Rak</label>
            <input type="text" class="form-control" name="name" placeholder="Contoh: Rak A1" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Posisi Lantai</label>
            <input type="text" class="form-control" name="floor" placeholder="Contoh: 1" value="1">
          </div>
        </div>
        <div class="modal-footer border-top p-3">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary fw-bold" style="background-color: #6e4727; border: none;">Simpan Rak</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function() {
  const PAGE_SIZE = 10;
  const DEBOUNCE_DELAY = 300;

  function debounce(func, wait) {
    let timeout;
    return function(...args) {
      const context = this;
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(context, args), wait);
    };
  }

  const tableStates = {};

  function initTablePagination(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const paginationContainer = document.querySelector(`.table-pagination-container[data-table="${tableId}"]`);
    if (!paginationContainer) return;

    const allRows = Array.from(table.querySelectorAll('tbody tr.attr-row'));
    
    tableStates[tableId] = {
      table: table,
      allRows: allRows,
      filteredRows: allRows,
      currentPage: 1,
      container: paginationContainer
    };

    renderTablePage(tableId);
  }

  function renderTablePage(tableId) {
    const state = tableStates[tableId];
    if (!state) return;

    const { table, allRows, filteredRows, currentPage, container } = state;
    const totalItems = filteredRows.length;
    const totalPages = Math.ceil(totalItems / PAGE_SIZE) || 1;

    if (state.currentPage > totalPages) state.currentPage = totalPages;
    if (state.currentPage < 1) state.currentPage = 1;

    const activePage = state.currentPage;
    const startIndex = (activePage - 1) * PAGE_SIZE;
    const endIndex = Math.min(startIndex + PAGE_SIZE, totalItems);

    allRows.forEach(row => row.style.display = 'none');

    for (let i = startIndex; i < endIndex; i++) {
      if (filteredRows[i]) {
        filteredRows[i].style.display = '';
      }
    }

    if (totalItems === 0) {
      container.innerHTML = `
        <div class="text-muted fs-7">Menampilkan 0 data</div>
        <div></div>
      `;
      return;
    }

    const startDisplay = startIndex + 1;
    const endDisplay = endIndex;
    
    let paginationHtml = `
      <div class="text-muted fs-7">
        Menampilkan <strong>${startDisplay}</strong> - <strong>${endDisplay}</strong> dari <strong>${totalItems}</strong> data
      </div>
      <nav aria-label="Table pagination">
        <ul class="pagination pagination-sm attr-pagination mb-0">
          <li class="page-item ${activePage === 1 ? 'disabled' : ''}">
            <button class="page-link prev-btn" data-table="${tableId}" data-page="${activePage - 1}" type="button">
              <i class="ti ti-chevron-left me-1"></i> Prev
            </button>
          </li>
    `;

    let startPage = Math.max(1, activePage - 2);
    let endPage = Math.min(totalPages, activePage + 2);

    if (activePage <= 3) {
      endPage = Math.min(5, totalPages);
    }
    if (activePage >= totalPages - 2) {
      startPage = Math.max(1, totalPages - 4);
    }

    if (startPage > 1) {
      paginationHtml += `
        <li class="page-item">
          <button class="page-link page-num-btn" data-table="${tableId}" data-page="1" type="button">1</button>
        </li>
      `;
      if (startPage > 2) {
        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
      }
    }

    for (let p = startPage; p <= endPage; p++) {
      paginationHtml += `
        <li class="page-item ${p === activePage ? 'active' : ''}">
          <button class="page-link page-num-btn" data-table="${tableId}" data-page="${p}" type="button">${p}</button>
        </li>
      `;
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
      }
      paginationHtml += `
        <li class="page-item">
          <button class="page-link page-num-btn" data-table="${tableId}" data-page="${totalPages}" type="button">${totalPages}</button>
        </li>
      `;
    }

    paginationHtml += `
          <li class="page-item ${activePage === totalPages ? 'disabled' : ''}">
            <button class="page-link next-btn" data-table="${tableId}" data-page="${activePage + 1}" type="button">
              Next <i class="ti ti-chevron-right ms-1"></i>
            </button>
          </li>
        </ul>
      </nav>
    `;

    container.innerHTML = paginationHtml;
  }

  function handleSearch(input) {
    const tableId = input.getAttribute('data-table');
    const state = tableStates[tableId];
    if (!state) return;

    const term = input.value.toLowerCase().trim();
    if (!term) {
      state.filteredRows = state.allRows;
    } else {
      state.filteredRows = state.allRows.filter(row => {
        const nameCell = row.querySelector('.attr-name');
        const text = nameCell ? nameCell.textContent.toLowerCase() : row.textContent.toLowerCase();
        return text.includes(term);
      });
    }

    state.currentPage = 1;
    renderTablePage(tableId);
  }

  const debouncedSearch = debounce(function(input) {
    handleSearch(input);
  }, DEBOUNCE_DELAY);

  function init() {
    ['tableAuthors', 'tablePublishers', 'tableCategories', 'tableRacks'].forEach(initTablePagination);

    document.querySelectorAll('.filter-table-input').forEach(input => {
      input.addEventListener('input', function() {
        debouncedSearch(this);
      });
    });

    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.page-num-btn, .prev-btn, .next-btn');
      if (!btn) return;
      e.preventDefault();
      
      const tableId = btn.getAttribute('data-table');
      const targetPage = parseInt(btn.getAttribute('data-page'), 10);

      if (tableStates[tableId] && targetPage) {
        tableStates[tableId].currentPage = targetPage;
        renderTablePage(tableId);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>

<?= $this->endSection(); ?>
