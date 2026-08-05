<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Tambah Penerbit Buku</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('admin/publishers'); ?>" class="btn btn-outline-primary mb-3">
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
    <h5 class="card-title fw-bold mb-4"><i class="ti ti-building-community text-primary me-2"></i> Form Tambah Penerbit</h5>
    <form action="<?= base_url('admin/publishers'); ?>" method="post">
      <?= csrf_field(); ?>
      <div class="mb-3" style="max-width: 500px;">
        <label for="name" class="form-label fw-semibold">Nama Penerbit</label>
        <input type="text" class="form-control <?php if ($validation->hasError('name')) : ?>is-invalid<?php endif ?>" id="name" name="name" value="<?= old('name'); ?>" placeholder="Masukkan nama penerbit" required autofocus>
        <div class="invalid-feedback">
          <?= $validation->getError('name'); ?>
        </div>
      </div>
      <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan</button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
