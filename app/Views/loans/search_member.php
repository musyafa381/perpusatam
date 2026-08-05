<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Peminjaman Baru</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('admin/loans'); ?>" class="btn btn-outline-primary mb-3">
  <i class="ti ti-arrow-left"></i>
  Kembali
</a>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show" role="alert">
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
          <i class="ti ti-scan me-1"></i> Peminjaman Baru (Scan / Cari Member)
        </div>
        <h3 class="text-white fw-bold mb-1">Scan Barcode / Cari Anggota Peminjam</h3>
        <p class="text-white-50 mb-0">Langkah 1: Tembak barcode ID Card anggota dengan scanner ATAU ketik nama / UID / email secara manual.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/loans'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 shadow-sm mb-4" style="background-color: #fffdfa !important; border: 1.5px solid #e8decb !important; border-radius: 16px;">
  <div class="card-body p-4">
    
    <div class="mb-4">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-2" style="background-color: #f8f2e6; color: #8b5e3c;">
          <i class="ti ti-scan fs-6"></i>
        </div>
        <h5 class="fw-bold mb-0" style="color: #432818;">Input Barcode Scanner / Cari Manual Anggota</h5>
      </div>
      <p class="text-muted fs-2 mb-3">Tembak Barcode ID Card (NIS/NIP) ATAU Ketik Nama / UID / Email Anggota</p>

      <div class="input-group search-group input-group-lg shadow-sm rounded-pill overflow-hidden border" style="border-color: #c59b27 !important;">
        <span class="input-group-text bg-white px-3 border-0" style="color: #8b5e3c;"><i class="ti ti-scan fs-6"></i></span>
        <input type="text" class="form-control form-control-lg fs-4 border-0 py-3" id="search" name="search" autofocus placeholder="Arahkan scanner ke ID Card atau ketik nama/UID..." onkeypress="if(event.key === 'Enter') { event.preventDefault(); getMemberData(this.value); }">
        <button class="btn btn-pill-gold fw-bold px-4 border-0 d-flex align-items-center gap-2" onclick="getMemberData(document.querySelector('#search').value)">
          <i class="ti ti-search fs-5"></i> Cari Member
        </button>
      </div>
      <div class="form-text fs-2 mt-2" style="color: #6e4727;">
        <i class="ti ti-info-circle me-1" style="color: #c59b27;"></i>Tembakkan alat <strong>Barcode Scanner USB/Wireless</strong> ke ID Card Anggota, atau ketik nama/UID lalu tekan <strong>Enter</strong>.
      </div>
    </div>

    <div class="mt-4">
      <div id="memberResult" class="p-4 rounded-4 text-center border" style="background-color: #fcf8f2 !important; border: 1.5px dashed #e8decb !important; min-height: 100px;">
        <p class="text-muted mb-0 fs-3" style="color: #8b5e3c !important;"><i class="ti ti-info-circle me-1"></i> Data anggota peminjam akan muncul di sini</p>
      </div>
    </div>

  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function getMemberData(param) {
    if (!param) return;

    jQuery.ajax({
      url: "<?= base_url('admin/loans/new/members/search'); ?>",
      type: 'get',
      data: {
        'param': param
      },
      success: function(response, status, xhr) {
        $('#memberResult').html(response);
        $('#search').val(''); // Auto clear search input

        $('html, body').animate({
          scrollTop: $("#memberResult").offset().top
        }, 300);
      },
      error: function(xhr, status, thrown) {
        console.log(thrown);
        $('#memberResult').html(thrown);
        $('#search').val(''); // Auto clear search input
      }
    });
  }

</script>
<?= $this->endSection() ?>