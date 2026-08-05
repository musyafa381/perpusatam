<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Pengembalian Baru</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= base_url('admin/returns'); ?>" class="btn btn-outline-primary mb-3">
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
          <i class="ti ti-rotate-clockwise-2 me-1"></i> Pengembalian Baru
        </div>
        <h3 class="text-white fw-bold mb-1">Pindai Barcode / Cari Peminjaman Buku</h3>
        <p class="text-white-50 mb-0">Tembakkan alat barcode scanner ke ID Card Anggota, Barcode Transaksi 8-Digit, atau Kode Buku, atau ketik pencarian manual.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/returns'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 shadow-sm mb-4" style="background-color: #fffdfa !important; border: 1.5px solid #e8decb !important; border-radius: 16px;">
  <div class="card-body p-4">
    <!-- Pilihan Mode / Opsi Identifikasi -->
    <div class="d-flex align-items-center justify-content-center gap-2 mb-4 flex-wrap text-center">
      <span class="badge px-3 py-2 fs-2 rounded-pill" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
        <i class="ti ti-id-badge-2 me-1" style="color: #8b5e3c !important;"></i> 1. Barcode Anggota (ID Card NIS/NIP)
      </span>
      <span class="badge px-3 py-2 fs-2 rounded-pill" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
        <i class="ti ti-barcode me-1" style="color: #8b5e3c !important;"></i> 2. Barcode Transaksi (8-Digit UID)
      </span>
      <span class="badge px-3 py-2 fs-2 rounded-pill" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
        <i class="ti ti-search me-1" style="color: #8b5e3c !important;"></i> 3. Input Manual (Nama / Email / Judul Buku)
      </span>
    </div>

    <!-- Main Barcode / Manual Input Search Box -->
    <div class="p-4 rounded-4 border mb-4" style="background-color: #fcf8f2 !important; border: 1.5px solid #e8decb !important;">
      <label for="search" class="form-label fw-bold fs-3 mb-2" style="color: #432818;">
        <i class="ti ti-scan me-1" style="color: #8b5e3c;"></i> Input / Tembak Scanner Fisik Barcode Transaksi / Anggota:
      </label>
      <div class="input-group input-group-lg search-group shadow-sm rounded-pill overflow-hidden border" style="border-color: #c59b27 !important;">
        <span class="input-group-text bg-white px-3 border-0" style="color: #8b5e3c;"><i class="ti ti-barcode fs-6"></i></span>
        <input type="text" class="form-control border-0 fs-4 py-3" id="search" name="search" placeholder="Tembak barcode ID Card / Transaksi 8-Digit / Kode Buku, atau ketik nama/judul..." autofocus onkeypress="if(event.key === 'Enter') getLoan(this.value)">
        <button class="btn btn-pill-gold fw-bold px-4 fs-3 border-0 d-flex align-items-center gap-2" type="button" onclick="getLoan(document.querySelector('#search').value)">
          <i class="ti ti-search fs-5"></i> Cari Data
        </button>
      </div>
      <small class="form-text mt-2 d-block fs-2" style="color: #6e4727;">
        <i class="ti ti-info-circle me-1" style="color: #c59b27;"></i> Arahkan kursor pada kolom input di atas lalu tembakkan barcode scanner fisik. Hasil pencarian akan muncul otomatis di bawah.
      </small>
    </div>

    <!-- Live Search Results Container -->
    <div class="row">
      <div class="col-12">
        <div id="loanResult" class="p-4 rounded-4 text-center border" style="background-color: #fcf8f2 !important; border: 1.5px dashed #e8decb !important; min-height: 100px;">
          <p class="text-muted mb-0 fs-3" style="color: #8b5e3c !important;"><i class="ti ti-barcode me-1"></i> Menunggu tembakan barcode scanner atau kata kunci pencarian...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  let searchTimeout = null;

  function getLoan(param) {
    param = param.trim();
    if (!param) return;

    $('#loanResult').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2 mb-0">Mencari data peminjaman...</p></div>');

    jQuery.ajax({
      url: "<?= base_url('admin/returns/new/search'); ?>",
      type: 'get',
      data: {
        'param': param
      },
      success: function(response, status, xhr) {
        $('#loanResult').html(response);
        $('#search').val(''); // Auto clear search input

        $('html, body').animate({
          scrollTop: $("#loanResult").offset().top - 80
        }, 300);
      },
      error: function(xhr, status, thrown) {
        console.log(thrown);
        $('#loanResult').html('<div class="alert alert-danger mb-0">Gagal mengambil data pencarian. Silakan coba lagi.</div>');
        $('#search').val(''); // Auto clear search input
      }
    });
  }


  // Instant live search as user types or physical scanner shoots
  $(document).on('input', '#search', function() {
    clearTimeout(searchTimeout);
    const val = this.value.trim();
    if (val.length >= 2) {
      searchTimeout = setTimeout(() => {
        getLoan(val);
      }, 400);
    }
  });
</script>
<?= $this->endSection() ?>