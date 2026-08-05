<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Data Rak Buku</title>
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
          <i class="ti ti-box me-1"></i> Lokasi Penyimpanan
        </div>
        <h3 class="text-white fw-bold mb-1">Data Rak Buku</h3>
        <p class="text-white-50 mb-0">Kelola lokasi fisik penyimpanan buku dan pembagian lantai perpustakaan.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/racks/new'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-plus me-1"></i> Tambah Rak Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom mb-0">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Nama Rak</th>
            <th scope="col" class="text-center">Lantai</th>
            <th scope="col" class="text-center">Jumlah Buku</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1 + ($itemPerPage * ($currentPage - 1)) ?>
          <?php foreach ($racks as $key => $rack) : ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="fw-bold text-dark fs-3"><?= esc($rack['name']); ?></div>
              </td>
              <td class="text-center">
                <span class="badge badge-subtle-secondary fs-2 px-3 py-2">
                  <i class="ti ti-building me-1"></i>Lantai <?= esc($rack['floor']); ?>
                </span>
              </td>
              <td class="text-center">
                <span class="badge badge-subtle-primary fs-2 px-3 py-2">
                  <i class="ti ti-books me-1"></i><?= $bookCountInRacks[$key] ?? 0; ?> Buku
                </span>
              </td>
              <td class="text-center pe-3">
                <div class="d-flex justify-content-center gap-2">
                  <a href="<?= base_url("admin/racks/{$rack['id']}/edit"); ?>" class="btn btn-primary btn-sm px-3 rounded-3">
                    <i class="ti ti-edit me-1"></i> Edit
                  </a>
                  <form action="<?= base_url("admin/racks/{$rack['id']}"); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-3" data-confirm="Apakah Anda yakin ingin menghapus rak ini?">
                      <i class="ti ti-trash me-1"></i> Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager->links('racks', 'my_pager'); ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>