<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Peminjaman Baru - Pilih Buku</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use CodeIgniter\I18n\Time;

$tier = \App\Models\MemberModel::getTierDetails($member);
$loanModel = new \App\Models\LoanModel();
$activeLoansCount = $loanModel->where(['member_id' => $member['id'], 'return_date' => null])->countAllResults();
?>

<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div class="badge bg-white text-primary fw-bold px-3 py-1 mb-2 rounded-pill fs-2 shadow-sm">
          <i class="ti ti-arrow-right-circle me-1"></i> Langkah 2 dari 3
        </div>
        <h3 class="text-white fw-bold mb-1">Transaksi Peminjaman Baru</h3>
        <p class="text-white-50 mb-0">Cari dan pilih katalog buku yang akan dipinjam oleh anggota.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/loans/new/members/search'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-arrow-left me-1"></i> Ganti Anggota
        </a>
      </div>
    </div>
  </div>
</div>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="alert alert-<?= session()->getFlashdata('error') ? 'danger' : 'success'; ?> alert-dismissible fade show shadow-sm border border-2 border-<?= session()->getFlashdata('error') ? 'danger' : 'success'; ?> p-3 mb-4 rounded-3" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="ti <?= session()->getFlashdata('error') ? 'ti-alert-circle text-danger' : 'ti-circle-check text-success'; ?> fs-7 me-1"></i>
      <div>
        <h6 class="fw-bold mb-0 text-dark"><?= session()->getFlashdata('error') ? 'Gagal Melakukan Transaksi Peminjaman' : 'Berhasil'; ?></h6>
        <div class="fs-3"><?= session()->getFlashdata('msg'); ?></div>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="row g-4 mb-4">
  <!-- Left Side: Search & Selected Books -->
  <div class="col-12 col-lg-7">
    <div class="card info-card border-0 mb-4">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-scan text-primary me-2"></i> Scan Barcode Kartu Buku / Cari Manual</h5>
        <div class="input-group search-group mb-2">
          <span class="input-group-text bg-white text-primary"><i class="ti ti-scan fs-5"></i></span>
          <input type="text" class="form-control" id="search" name="search" autofocus placeholder="Tembak barcode kartu buku atau ketik judul/pengarang/ISBN..." onkeypress="if(event.key==='Enter') { event.preventDefault(); getBookData(this.value); }">
          <button class="btn btn-primary fw-bold px-4" onclick="getBookData(document.querySelector('#search').value)">
            <i class="ti ti-search me-1"></i> Cari
          </button>
        </div>
        <small class="text-muted"><i class="ti ti-info-circle me-1 text-primary"></i>Arahkan kursor ke kotak ini dan tembakkan scanner ke barcode kartu buku ATAU ketik judul/pengarang/ISBN secara manual.</small>
      </div>
    </div>

    <!-- Selected Books Container -->
    <div class="card info-card border-0">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3"><i class="ti ti-bookmark-check text-success me-2"></i> Daftar Buku yang Dipilih</h5>
        <ul id="bookList" class="list-unstyled d-flex flex-wrap gap-3 mb-3 p-0">
          <li id="none" class="p-3 bg-light rounded-3 text-muted text-center w-100 border">
            <i class="ti ti-book-off fs-6 d-block mb-1"></i>
            <b>Silakan cari dan pilih buku pada tabel di bawah terlebih dahulu.</b>
          </li>
        </ul>
        <form id="bookForm" action="<?= base_url('admin/loans/new'); ?>" method="post" class="m-0">
          <?= csrf_field(); ?>
          <input type="hidden" name="member_uid" value="<?= $member['uid']; ?>">
        </form>
      </div>
    </div>
  </div>

  <!-- Right Side: Member Summary Card -->
  <div class="col-12 col-lg-5">
    <div class="card info-card border-0 h-100">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-4"><i class="ti ti-user-check text-primary me-2"></i> Ringkasan Data Pemilik Pinjaman</h5>

        <div class="d-flex align-items-center mb-4 p-3 bg-light rounded-3 border">
          <div class="member-avatar me-3" style="width: 50px; height: 50px; font-size: 1.2rem;">
            <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1) . substr($member['last_name'] ?? '', 0, 1)); ?>
          </div>
          <div>
            <div class="fw-bold text-dark fs-4 mb-0"><?= esc("{$member['first_name']} {$member['last_name']}"); ?></div>
            <span class="badge <?= $tier['badge']; ?> px-3 py-1 fs-2 mt-1">
              <i class="ti <?= $tier['icon']; ?> me-1"></i> <?= esc($tier['name']); ?>
            </span>
          </div>
        </div>

        <?php if (!$tier['allow_novel']) : ?>
          <div class="alert alert-warning p-3 mb-3 fs-2 rounded-3 border border-warning">
            <i class="ti ti-alert-triangle text-warning me-1"></i>
            <strong>Perhatian:</strong> Anggota berstatus <strong><?= $tier['name']; ?></strong> tidak diizinkan meminjam buku kategori <strong>Novel</strong>.
          </div>
        <?php endif; ?>

        <?php if ($activeLoansCount >= $tier['max_loans']) : ?>
          <div class="alert alert-danger p-3 mb-3 fs-2 rounded-3 border border-danger">
            <i class="ti ti-alert-circle text-danger me-1"></i>
            <strong>Peringatan:</strong> Batas peminjaman aktif telah penuh (<?= $activeLoansCount; ?>/<?= $tier['max_loans']; ?> buku).
          </div>
        <?php endif; ?>

        <ul class="list-group list-group-flush border rounded-3 overflow-hidden">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-books me-2 text-primary"></i> Batas Maks. Pinjam</span>
            <strong class="text-dark">Maks <?= $tier['max_loans']; ?> Buku (Aktif: <?= $activeLoansCount; ?>)</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-clock me-2 text-primary"></i> Durasi Pinjam</span>
            <strong class="text-dark">Maks <?= $tier['max_days']; ?> Hari</strong>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <span><i class="ti ti-book-2 me-2 text-primary"></i> Peminjaman Novel</span>
            <div><?= $tier['allow_novel'] ? '<span class="badge badge-subtle-primary fw-bold">✅ Boleh Meminjam</span>' : '<span class="badge badge-subtle-danger fw-bold">❌ Perlu Silver Member+</span>'; ?></div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Search Results Container -->
<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <div id="bookResult">
      <div class="p-5 text-center text-muted">
        <i class="ti ti-search fs-8 d-block mb-2"></i>
        <b>Ketik kata kunci buku di atas untuk menampilkan hasil pencarian katalog.</b>
      </div>
    </div>
  </div>
</div>
<!-- Modal Warning Popup Syarat Member -->
<div class="modal fade" id="modalWarning" tabindex="-1" aria-labelledby="modalWarningLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-body p-4" id="modalWarningMessage">
        <!-- Dynamic Warning Content -->
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  function getBookData(param) {
    if (!param.trim()) return;
    
    $('#bookResult').html('<div class="p-5 text-center text-primary"><div class="spinner-border mb-2" role="status"></div><br><b>Mencari data buku...</b></div>');

    jQuery.ajax({
      url: "<?= base_url('admin/loans/new/books/search'); ?>",
      type: 'get',
      data: {
        'param': param,
        'memberUid': '<?= $member['uid']; ?>'
      },
      success: function(response, status, xhr) {
        $('#bookResult').html(response);
        $('#search').val(''); // Auto clear search input

        $('html, body').animate({
          scrollTop: $("#bookResult").offset().top - 20
        }, 400);
      },
      error: function(xhr, status, thrown) {
        console.log(thrown);
        $('#bookResult').html('<div class="alert alert-danger">Gagal mengambil data buku: ' + thrown + '</div>');
        $('#search').val(''); // Auto clear search input
      }
    });
  }


  let bookSelection = new Map();

  const bookListElement = document.getElementById('bookList');
  const bookFormElement = document.getElementById('bookForm');

  function selectBook(book) {
    const key = book.key || book.slug;
    if (!bookSelection.has(key)) {
      bookSelection.set(key, book);
      addBook(book);
    } else {
      Swal.fire({
        icon: 'info',
        title: 'Buku Sudah Dipilih',
        text: `Buku/Eksemplar "${book.title}" ${book.item_code ? '(Kode: ' + book.item_code + ')' : ''} sudah ada dalam daftar peminjaman.`,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
    }
  }

  function unselectBook(key) {
    bookSelection.delete(key);
    removeBook(key);
  }

  function addBook(book) {
    const key = book.key || book.slug;
    const coverUrl = book.coverUrl || (book.cover && (book.cover.startsWith('http://') || book.cover.startsWith('https://')) ? book.cover : (book.cover ? '<?= base_url(BOOK_COVER_URI); ?>' + book.cover : '<?= base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER); ?>'));
    const itemCodeBadge = book.item_code 
      ? `<span class="badge fw-semibold fs-1 px-2 py-1 d-inline-block" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;"><i class="ti ti-barcode me-1" style="color: #8b5e3c !important;"></i>Kode: ${book.item_code}</span>` 
      : '<span class="badge bg-light text-muted border fs-1 d-inline-block">Umum / Tanpa Barcode</span>';

    const bookCard = `<li id="${key}" class="flex-grow-1" style="max-width: 340px;">
          <div class="card shadow-sm rounded-4 overflow-hidden position-relative p-2" style="background-color: #fcf8f2 !important; border: 1.5px solid #e8decb !important;">
            <div class="d-flex align-items-center gap-3">
              <img src="${coverUrl}" class="rounded-3 shadow-sm border" style="width: 52px; height: 72px; object-fit: cover;">
              <div class="flex-grow-1 min-w-0">
                <h6 class="fw-bold mb-1 text-truncate" style="color: #432818 !important; max-width: 170px;" title="${book.title}">${book.title}</h6>
                <div>${itemCodeBadge}</div>
              </div>
              <button type="button" onclick="unselectBook('${key}')" class="btn btn-sm rounded-circle p-1 me-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;" title="Hapus dari pilihan">
                <i class="ti ti-x fs-5"></i>
              </button>
            </div>
          </div>
        </li>`;



    if (bookSelection.size === 1) {
      const noneEl = bookListElement.querySelector('#none');
      if (noneEl) noneEl.remove();
      if (!bookFormElement.querySelector('#confirmBook')) {
        bookFormElement.innerHTML += `<button id="confirmBook" class="btn btn-pill-gold fw-bold px-4 py-3 shadow mt-3 w-100 fs-4 d-flex align-items-center justify-content-center" type="submit"><i class="ti ti-check me-2"></i> Konfirmasi & Lanjutkan Peminjaman</button>`;
      }
    }

    bookListElement.innerHTML += bookCard;
    bookFormElement.innerHTML += `<input type="hidden" name="slugs[]" value="${book.slug}" id="input-slug-${key}">
      <input type="hidden" name="item_codes[]" value="${book.item_code || ''}" id="input-code-${key}">`;
  }

  function removeBook(key) {
    const bookElement = bookListElement.querySelector(`#${key}`);
    const inputSlugElement = bookFormElement.querySelector(`#input-slug-${key}`);
    const inputCodeElement = bookFormElement.querySelector(`#input-code-${key}`);

    if (bookElement) bookElement.remove();
    if (inputSlugElement) inputSlugElement.remove();
    if (inputCodeElement) inputCodeElement.remove();

    if (bookSelection.size === 0) {
      bookListElement.innerHTML = `<li id="none" class="p-3 bg-light rounded-3 text-muted text-center w-100 border">
            <i class="ti ti-book-off fs-6 d-block mb-1"></i>
            <b>Silakan cari dan pilih buku pada tabel di bawah terlebih dahulu.</b>
          </li>`;

      const confirmBtn = bookFormElement.querySelector('#confirmBook');
      if (confirmBtn) confirmBtn.remove();
    }
  }


  document.getElementById('bookForm').addEventListener('submit', function(e) {
    const allowNovel = <?= json_encode((bool) $tier['allow_novel']); ?>;
    const tierName = <?= json_encode($tier['name']); ?>;
    const maxLoans = <?= intval($tier['max_loans']); ?>;
    const activeLoans = <?= intval($activeLoansCount); ?>;

    const selectedCount = bookSelection.size;
    const totalCount = activeLoans + selectedCount;

    // Check 1: Novel restriction for non-members
    let hasNovel = false;
    let novelTitle = '';
    for (let [slug, book] of bookSelection) {
      if (book.category && book.category.toLowerCase().includes('novel')) {
        hasNovel = true;
        novelTitle = book.title;
        break;
      }
    }

    if (hasNovel && !allowNovel) {
      e.preventDefault();
      $('#modalWarningMessage').html(`
        <div class="text-center p-3">
          <div class="avatar-lg bg-warning-subtle text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; margin: 0 auto;">
            <i class="ti ti-lock fs-9 text-warning"></i>
          </div>
          <h4 class="fw-bold text-dark mb-2">Buku Novel Tidak Diizinkan</h4>
          <p class="text-muted fs-3 mb-4">
            Buku <strong>"${novelTitle}"</strong> berkategori <strong>Novel</strong>.<br>
            Anggota berstatus <strong>${tierName}</strong> tidak diizinkan meminjam buku Novel. Diperlukan minimal <strong>Silver Member</strong> (Donasi min. 3 buku).
          </p>
          <button type="button" class="btn btn-pill-gold fw-bold px-4 py-2.5 w-100" data-bs-dismiss="modal">
            <i class="ti ti-check me-1"></i> Saya Mengerti, Hapus Buku Novel
          </button>
        </div>
      `);
      const warningModal = new bootstrap.Modal(document.getElementById('modalWarning'));
      warningModal.show();
      return false;
    }

    // Check 2: Over loan limit
    if (totalCount > maxLoans) {
      e.preventDefault();
      $('#modalWarningMessage').html(`
        <div class="text-center p-3">
          <div class="avatar-lg bg-danger-subtle text-danger rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; margin: 0 auto;">
            <i class="ti ti-ban fs-9 text-danger"></i>
          </div>
          <h4 class="fw-bold text-dark mb-2">Batas Peminjaman Terlampaui</h4>
          <p class="text-muted fs-3 mb-4">
            Anggota berstatus <strong>${tierName}</strong> hanya diperbolehkan meminjam maksimal <strong>${maxLoans} buku aktif</strong>.<br>
            Saat ini anggota sudah memiliki <strong>${activeLoans} peminjaman aktif</strong> + <strong>${selectedCount} buku baru terpilih</strong> (Total: ${totalCount} buku).
          </p>
          <button type="button" class="btn btn-pill-gold fw-bold px-4 py-2.5 w-100" data-bs-dismiss="modal">
            <i class="ti ti-check me-1"></i> Saya Mengerti, Kelola Pilihan Buku
          </button>
        </div>
      `);
      const warningModal = new bootstrap.Modal(document.getElementById('modalWarning'));
      warningModal.show();
      return false;
    }
  });
</script>
<?= $this->endSection() ?>