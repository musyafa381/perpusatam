<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Tambah Pengarang Buku</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('admin/authors'); ?>" class="btn btn-outline-primary mb-3">
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

<div class="card shadow-sm border-0">
  <div class="card-body p-4">
    <h5 class="card-title fw-bold mb-4"><i class="ti ti-user-plus text-primary me-2"></i> Form Tambah Pengarang</h5>
    <form action="<?= base_url('admin/authors'); ?>" method="post">
      <?= csrf_field(); ?>
      <div class="mb-3" style="max-width: 500px;">
        <label for="name" class="form-label fw-semibold">Nama Pengarang / Penulis</label>
        <input type="text" class="form-control <?php if ($validation->hasError('name')) : ?>is-invalid<?php endif ?>" id="name" name="name" value="<?= old('name'); ?>" placeholder="Masukkan nama pengarang" required autofocus>
        <div class="invalid-feedback">
          <?= $validation->getError('name'); ?>
        </div>
      </div>
      <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan</button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
