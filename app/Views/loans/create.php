<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Form Peminjaman Baru</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <a href="<?= base_url('admin/loans/new/books/search?member-uid=' . $member['uid']); ?>" class="btn btn-outline-primary">
    <i class="ti ti-arrow-left me-1"></i> Kembali ke Pencarian Buku
  </a>
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

<form action="<?= base_url('admin/loans'); ?>" method="post">
  <?= csrf_field(); ?>
  <input type="hidden" name="member_uid" value="<?= $member['uid']; ?>">

  <!-- Section 1: Information Anggota & Member Tier -->
  <div class="card mb-4 border border-2 border-primary-subtle shadow-sm">
    <div class="card-body p-4">
      <h5 class="card-title fw-bold text-dark mb-3"><i class="ti ti-user-check text-primary me-1"></i> Data Peminjam & Status Membership</h5>
      
      <?php
      $donatedCount = \App\Models\MemberModel::getDonatedBooksCount($member['id']);
      $donatedCount = max($donatedCount, intval($member['donated_books_count'] ?? 0));
      $tier = \App\Models\MemberModel::getTierDetails($member);
      $loanModel = new \App\Models\LoanModel();
      $activeLoansCount = $loanModel->where(['member_id' => $member['id'], 'return_date' => null])->countAllResults();

      $hasNovelSelected = false;
      $novelBookTitles = [];
      $outOfStockBooks = [];
      foreach ($books as $b) {
          if (stripos($b['category_name'] ?? '', 'novel') !== false || stripos($b['category'] ?? '', 'novel') !== false) {
              $hasNovelSelected = true;
              $novelBookTitles[] = $b['title'];
          }
          $hasAvailableItem = false;
          if (!empty($b['available_items'])) {
              foreach ($b['available_items'] as $it) {
                  $st = strtolower($it['status'] ?? 'tersedia');
                  if (($st === 'tersedia' || $st === 'available') && ($it['condition'] ?? 'baik') !== 'hilang') {
                      $hasAvailableItem = true;
                      break;
                  }
              }
          }
          if (!$hasAvailableItem) {
              $outOfStockBooks[] = $b['title'];
          }
      }
      $isOverLoanLimit = ($activeLoansCount + count($books)) > $tier['max_loans'];
      $isNovelBlocked = $hasNovelSelected && !$tier['allow_novel'];
      $isStockBlocked = !empty($outOfStockBooks);
      ?>

      <?php if ($isStockBlocked) : ?>
        <div class="alert alert-danger border border-2 border-danger shadow-sm p-3 mb-3 d-flex align-items-center rounded">
          <i class="ti ti-alert-circle fs-8 text-danger me-3"></i>
          <div>
            <h6 class="fw-bold text-dark mb-1">🚫 PERINGATAN STOK FISIK BUKU KOSONG / HABIS</h6>
            <p class="mb-0 fs-3">
              Buku <strong>"<?= implode(', ', $outOfStockBooks); ?>"</strong> saat ini tidak memiliki eksemplar fisik yang tersedia (Stok 0 / Sedang Dipinjam). 
              Silakan hapus buku tersebut dari daftar pinjam untuk melanjutkan transaksi.
            </p>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($isNovelBlocked) : ?>
        <div class="alert alert-warning border border-2 border-warning shadow-sm p-3 mb-3 d-flex align-items-center rounded">
          <i class="ti ti-alert-triangle fs-8 text-warning me-3"></i>
          <div>
            <h6 class="fw-bold text-dark mb-1">⚠️ PERINGATAN KELAYAKAN MEMBER NOVEL</h6>
            <p class="mb-0 fs-3">
              Buku <strong>"<?= implode(', ', $novelBookTitles); ?>"</strong> berkategori <strong>Novel</strong>. 
              Anggota berstatus <strong><?= $tier['name']; ?></strong> tidak diizinkan meminjam Novel. 
              Diperlukan minimal <strong>Silver Member</strong> (Mendonasikan min. 3 buku atau diangkat manual oleh Superadmin).
            </p>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($isOverLoanLimit) : ?>
        <div class="alert alert-danger border border-2 border-danger shadow-sm p-3 mb-3 d-flex align-items-center rounded">
          <i class="ti ti-alert-circle fs-8 text-danger me-3"></i>
          <div>
            <h6 class="fw-bold text-dark mb-1">🚫 PERINGATAN BATAS PINJAMAN TERLAMPAUI</h6>
            <p class="mb-0 fs-3">
              Status <strong><?= $tier['name']; ?></strong> hanya diperbolehkan meminjam maksimal <strong><?= $tier['max_loans']; ?> Buku Aktif</strong>. 
              Saat ini anggota sudah memiliki <?= $activeLoansCount; ?> peminjaman aktif + <?= count($books); ?> buku baru terpilih.
            </p>
          </div>
        </div>
      <?php endif; ?>

      <div class="p-3 rounded border border-light-subtle bg-light mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
          <span class="badge <?= $tier['badge']; ?> fs-3 px-3 py-2"><i class="ti <?= $tier['icon']; ?> me-1"></i> <?= $tier['name']; ?></span>
          <span class="text-muted fs-2">(Donasi: <strong><?= $donatedCount; ?></strong> buku)</span>
        </div>
        <div class="fs-2 fw-semibold" style="color: #6e4727;">
          <span class="me-3"><i class="ti ti-book me-1 text-primary"></i> Maks: <strong class="text-primary"><?= $tier['max_loans']; ?> Buku</strong> (Aktif: <strong class="text-primary"><?= $activeLoansCount; ?></strong>)</span>
          <span class="me-3"><i class="ti ti-clock me-1 text-primary"></i> Maks Durasi: <strong class="text-primary"><?= $tier['max_days']; ?> Hari</strong></span>
          <span><i class="ti ti-file-text me-1 text-primary"></i> Novel: <strong class="badge badge-subtle-primary"><?= $tier['allow_novel'] ? '✅ Boleh' : '❌ Tidak Boleh'; ?></strong></span>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold text-muted">Nama Anggota</label>
          <input type="text" class="form-control bg-light fw-bold text-dark" value="<?= "{$member['first_name']} {$member['last_name']}"; ?>" disabled>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold text-muted">Tipe & Instansi Anggota</label>
          <input type="text" class="form-control bg-light text-dark" value="<?= ($member['member_type'] ?? 'siswa') === 'siswa' ? 'Siswa / Santri (' . ($member['institution'] ?? 'Umum') . (!empty($member['class_level']) ? ' - ' . $member['class_level'] : '') . ')' : 'Petugas / Staf'; ?>" disabled>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold text-muted">Email / UID Barcode</label>
          <input type="text" class="form-control bg-light text-dark" value="<?= !empty($member['email']) && strpos($member['email'], 'student_') === false ? $member['email'] : $member['uid']; ?>" disabled>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label fw-semibold text-muted">Nomor Telepon</label>
          <input type="text" class="form-control bg-light text-dark" value="<?= $member['phone'] ?: '-'; ?>" disabled>
        </div>
      </div>
    </div>
  </div>

  <!-- Section 2: Form Peminjaman Buku Terpilih -->
  <div class="card shadow-sm">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="card-title fw-bold text-dark mb-0"><i class="ti ti-books text-primary me-1"></i> Daftar Buku Dipinjam</h5>
        <span class="badge badge-subtle-primary fs-3 px-3 py-2" id="selectedBookBadgeCount"><?= count($books); ?> Judul Buku Terpilih</span>
      </div>


      <div class="row g-2 mb-3" id="confirmationBookList">
        <?php 
        $usedItemIds = [];
        foreach ($books as $index => $book) : 
        ?>
          <?php
          $coverImageUrl = getBookCover($book['book_cover'] ?? '');
          $preselectedCode = $book['selected_item_code'] ?? ($selectedItemCodesMap[$book['slug']] ?? null);

          // Find unique item id matching preselectedCode if available
          $selectedItemId = 0;
          if (!empty($book['available_items'])) {
              foreach ($book['available_items'] as $item) {
                  if (!in_array($item['id'], $usedItemIds)) {
                      if ($preselectedCode && $preselectedCode === $item['item_code']) {
                          $selectedItemId = $item['id'];
                          break;
                      }
                  }
              }
              if (!$selectedItemId) {
                  foreach ($book['available_items'] as $item) {
                      if (!in_array($item['id'], $usedItemIds)) {
                          $selectedItemId = $item['id'];
                          $preselectedCode = $item['item_code'];
                          break;
                      }
                  }
              }
          }
          if ($selectedItemId > 0) {
              $usedItemIds[] = $selectedItemId;
          }
          ?>
          
          <div class="col-12 book-confirm-item" id="confirm-book-row-<?= $index; ?>">
            <input type="hidden" name="slugs[]" value="<?= $book['slug']; ?>">
            <input type="hidden" name="selected_item_ids[]" value="<?= $selectedItemId; ?>">
            <input type="hidden" name="items-<?= $book['slug']; ?>[]" value="<?= $selectedItemId; ?>">

            <div class="card border border-light-subtle shadow-sm rounded-3 overflow-hidden mb-1">
              <div class="card-body p-2 px-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  
                  <div class="d-flex align-items-center gap-3">
                    <!-- Sampul Buku -->
                    <img src="<?= $coverImageUrl; ?>" alt="<?= $book['title']; ?>" class="rounded border shadow-sm" style="width: 44px; height: 60px; object-fit: cover;">

                    <!-- Info Rincian Buku -->
                    <div>
                      <h6 class="fw-bold text-dark mb-0 fs-3"><?= "{$book['title']} ({$book['year']})"; ?></h6>
                      <div class="text-muted fs-2">
                        <span class="me-3"><i class="ti ti-user me-1"></i>Pengarang: <strong><?= $book['author']; ?></strong></span>
                        <span><i class="ti ti-building me-1"></i>Penerbit: <?= $book['publisher']; ?></span>
                      </div>
                    </div>
                  </div>

                  <div class="d-flex align-items-center gap-3">
                    <!-- Item Code Badge Only -->
                    <?php if ($selectedItemId > 0 && !empty($preselectedCode)) : ?>
                      <span class="badge fw-semibold fs-2 px-3 py-2" style="background-color: #f8f2e6 !important; color: #6e4727 !important; border: 1px solid #e8decb !important;">
                        <i class="ti ti-barcode me-1" style="color: #8b5e3c !important;"></i>Kode Eksemplar: <strong><?= esc($preselectedCode); ?></strong>
                      </span>
                    <?php else : ?>
                      <span class="badge bg-danger text-white fs-2 px-3 py-2">
                        <i class="ti ti-alert-circle me-1"></i>Stok Habis (0 Eksemplar Tersedia)
                      </span>
                    <?php endif; ?>

                    <!-- Tombol Hapus Buku -->
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="removeBookRow(<?= $index; ?>, '<?= esc($book['title']); ?>')" title="Hapus dari daftar pinjam">
                      <i class="ti ti-trash fs-4"></i>
                    </button>

                  </div>

                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>


      <!-- Durasi Peminjaman Global (Tombol Radio Kesamping) -->
      <div class="p-4 rounded-4 border mb-4" style="border: 1.5px solid #e8decb !important; background-color: #fcf8f2 !important;">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
          <div>
            <h6 class="fw-bold mb-1" style="color: #432818;"><i class="ti ti-clock me-2 text-primary fs-5"></i>Durasi Peminjaman</h6>
            <small class="text-muted">Pilih berapa lama batas peminjaman untuk transaksi ini:</small>
          </div>
          
          <div class="btn-group duration-btn-group" role="group" aria-label="Durasi Peminjaman">
            <input type="radio" class="btn-check" name="global_duration" id="dur_7" value="7" checked autocomplete="off">
            <label class="btn btn-outline-primary px-3 py-2 fw-bold d-flex align-items-center gap-1" for="dur_7">
              <i class="ti ti-calendar-event fs-4"></i> 7 Hari <small class="fw-normal">(Standar)</small>
            </label>

            <?php if ($tier['max_days'] >= 10) : ?>
              <input type="radio" class="btn-check" name="global_duration" id="dur_10" value="10" autocomplete="off">
              <label class="btn btn-outline-primary px-3 py-2 fw-bold d-flex align-items-center gap-1" for="dur_10">
                <i class="ti ti-calendar-plus fs-4"></i> 10 Hari
              </label>
            <?php endif; ?>

            <?php if ($tier['max_days'] >= 14 && $tier['max_days'] < 90) : ?>
              <input type="radio" class="btn-check" name="global_duration" id="dur_14" value="14" autocomplete="off">
              <label class="btn btn-outline-primary px-3 py-2 fw-bold d-flex align-items-center gap-1" for="dur_14">
                <i class="ti ti-calendar-stats fs-4"></i> 14 Hari
              </label>
            <?php endif; ?>

            <?php if ($tier['max_days'] >= 30) : ?>
              <input type="radio" class="btn-check" name="global_duration" id="dur_30" value="30" autocomplete="off">
              <label class="btn btn-outline-primary px-3 py-2 fw-bold d-flex align-items-center gap-1" for="dur_30">
                <i class="ti ti-calendar-event fs-4"></i> 30 Hari <small class="fw-normal">(1 Bulan)</small>
              </label>
            <?php endif; ?>

            <?php if ($tier['max_days'] >= 60) : ?>
              <input type="radio" class="btn-check" name="global_duration" id="dur_60" value="60" autocomplete="off">
              <label class="btn btn-outline-primary px-3 py-2 fw-bold d-flex align-items-center gap-1" for="dur_60">
                <i class="ti ti-calendar-plus fs-4"></i> 60 Hari <small class="fw-normal">(2 Bulan)</small>
              </label>
            <?php endif; ?>

            <?php if ($tier['max_days'] >= 90) : ?>
              <input type="radio" class="btn-check" name="global_duration" id="dur_90" value="90" autocomplete="off">
              <label class="btn btn-outline-primary px-3 py-2 fw-bold d-flex align-items-center gap-1" for="dur_90">
                <i class="ti ti-building-community fs-4"></i> 90 Hari <small class="fw-normal">(1 Semester)</small>
              </label>
            <?php endif; ?>
          </div>
        </div>
      </div>



      <div class="d-flex justify-content-end align-items-center gap-3 pt-2">
        <a href="<?= base_url('admin/loans/new/books/search?member-uid=' . $member['uid']); ?>" class="btn btn-outline-secondary px-4">
          Batal
        </a>
        <?php if ($isNovelBlocked || $isOverLoanLimit || $isStockBlocked) : ?>
          <button type="button" class="btn btn-danger fw-bold px-4 py-2" onclick="alert('<?= $isNovelBlocked ? 'Anggota berstatus Non-Member / Regular tidak diizinkan meminjam buku Novel. Silakan ganti buku atau tingkatkan status member.' : ($isOverLoanLimit ? 'Batas peminjaman aktif anggota telah penuh!' : 'Terdapat buku terpilih yang stok fisiknya habis/kosong (0 Eksemplar Tersedia). Hapus buku tersebut untuk melanjutkan.'); ?>')">
            <i class="ti ti-ban me-1"></i> Transaksi Ditolak (<?= $isStockBlocked ? 'Stok Buku Kosong' : 'Periksa Syarat Member'; ?>)
          </button>
        <?php else : ?>
          <button type="submit" id="saveLoanBtn" class="btn btn-pill-gold fw-bold px-4 py-2">
            <i class="ti ti-device-floppy me-1"></i> Simpan Transaksi Peminjaman
          </button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</form>

<script>
  function removeBookRow(index, title) {
    const bookTitle = title || 'Buku ini';
    
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Hapus Buku?',
        text: `Apakah Anda yakin ingin menghapus "${bookTitle}" dari daftar peminjaman?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#8b5e3c',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="ti ti-trash me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          executeRemoveRow(index);
        }
      });
    } else {
      if (confirm(`Apakah Anda yakin ingin menghapus "${bookTitle}" dari daftar peminjaman?`)) {
        executeRemoveRow(index);
      }
    }
  }

  function executeRemoveRow(index) {
    const row = document.getElementById('confirm-book-row-' + index);
    if (row) {
      row.remove();
      const remainingItems = document.querySelectorAll('.book-confirm-item');
      const badgeCount = document.getElementById('selectedBookBadgeCount');
      if (badgeCount) {
        badgeCount.innerText = remainingItems.length + ' Judul Buku Terpilih';
      }
      if (remainingItems.length === 0) {
        const listContainer = document.getElementById('confirmationBookList');
        if (listContainer) {
          listContainer.innerHTML = `<div class="col-12"><div class="alert alert-warning text-center p-4 rounded-3 border"><i class="ti ti-book-off fs-7 d-block mb-1"></i><b>Tidak ada buku dalam daftar peminjaman.</b><p class="mb-0 fs-2 text-muted">Silakan kembali ke pencarian buku untuk memilih eksemplar yang ingin dipinjam.</p></div></div>`;
        }
        const saveBtn = document.getElementById('saveLoanBtn');
        if (saveBtn) saveBtn.disabled = true;
      }
    }
  }
</script>

<?= $this->endSection() ?>