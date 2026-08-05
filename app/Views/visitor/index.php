<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>Buku Tamu Digital & Presensi Kunjungan - Perpustakaan Assalafiyyah Mlangi</title>
<style>
  .visitor-kiosk-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid #e8decb;
    box-shadow: 0 15px 35px rgba(139, 94, 60, 0.12);
  }
  .visitor-clock {
    font-size: 2.5rem;
    font-weight: 800;
    color: #8b5e3c;
    letter-spacing: 1px;
  }
  .search-autocomplete-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 1050;
    max-height: 250px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #c59b27;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  }
  .search-item-option {
    padding: 10px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f8f2e6;
    transition: background 0.15s ease;
  }
  .search-item-option:hover, .search-item-option.active {
    background-color: #f8f2e6;
    color: #6e4727;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">

  <!-- Header Section Buku Tamu -->
  <div class="row align-items-center mb-4">
    <div class="col-md-7">
      <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url(); ?>" class="btn btn-outline-secondary rounded-circle d-flex align-items-center justify-content-center p-0 shadow-sm" style="width: 44px; height: 44px;" title="Kembali ke Beranda">
          <i class="ti ti-arrow-left fs-4"></i>
        </a>
        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 50px; height: 50px; background: linear-gradient(135deg, #8b5e3c, #c59b27);">
          <i class="ti ti-id-badge-2 fs-2"></i>
        </div>
        <div>
          <h2 class="fw-extrabold text-dark mb-0">Buku Tamu Digital</h2>
          <p class="text-muted mb-0 fs-3">Presensi Kunjungan Perpustakaan Assalafiyyah Mlangi</p>
        </div>
      </div>
    </div>
    <div class="col-md-5 text-md-end mt-3 mt-md-0">
      <div class="d-inline-block px-4 py-2 bg-white rounded-4 border shadow-sm text-center">
        <div class="text-uppercase text-muted fw-bold fs-1" id="liveDateStr">--</div>
        <div class="visitor-clock" id="liveClock">00:00:00</div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Form Input / Scan Kiosk -->
    <div class="col-lg-6">
      <div class="visitor-kiosk-card p-4 p-md-5 h-100">
        <div class="text-center mb-4">
          <?php if (!empty($activeSession)) : ?>
            <span class="badge px-3 py-2 rounded-pill fw-bold text-white mb-2" style="background-color: #10b981;">
              <i class="ti ti-clock-play me-1"></i> Sesi Aktif: <?= esc($activeSession['session_name']); ?>
            </span>
          <?php else : ?>
            <span class="badge px-3 py-2 rounded-pill fw-bold text-white mb-2" style="background-color: #ef4444;">
              <i class="ti ti-circle-x me-1"></i> Sesi Kunjungan Ditutup
            </span>
          <?php endif; ?>
          <h4 class="fw-bold text-dark mb-1">Presensi Masuk Perpustakaan</h4>
          <p class="text-muted fs-3">Tembakkan Barcode ID Card (NIS/UID) atau ketik nama Anda di bawah ini:</p>
        </div>

        <?php if (empty($activeSession)) : ?>
          <div class="alert alert-warning rounded-4 p-4 text-center border-0 shadow-sm mb-4" style="background-color: #fef3c7; color: #92400e;">
            <i class="ti ti-lock fs-1 d-block mb-2 text-warning"></i>
            <h5 class="fw-bold mb-1">Sesi Presensi Belum Dibuka</h5>
            <p class="mb-0 fs-2">Petugas Perpustakaan belum membuka sesi kunjungan untuk saat ini. Silakan hubungi Petugas Perpustakaan.</p>
          </div>
        <?php endif; ?>

        <form id="visitorCheckinForm" autocomplete="off" onsubmit="event.preventDefault(); submitVisitorCheckin();">
          <div class="position-relative mb-3">
            <div class="input-group input-group-lg shadow-sm rounded-4 overflow-hidden" style="border: 2px solid #c59b27;">
              <span class="input-group-text border-0 text-white" style="background-color: #8b5e3c;">
                <i class="ti ti-barcode fs-3"></i>
              </span>
              <input type="text" id="visitorSearchInput" class="form-control border-0 py-3 fs-3 fw-semibold" placeholder="Scan Barcode ID Card atau ketik nama..." <?= empty($activeSession) ? 'disabled' : 'autofocus required'; ?>>
              <button class="btn text-white fw-bold px-4" type="submit" style="background-color: #8b5e3c;" id="btnSubmitVisitor" <?= empty($activeSession) ? 'disabled' : ''; ?>>
                <i class="ti ti-check me-1"></i> Masuk
              </button>
            </div>
            <!-- Auto Complete Dropdown -->
            <div id="autocompleteList" class="search-autocomplete-list d-none"></div>
          </div>
        </form>

        <!-- Alert Notification -->
        <div id="visitorAlert" class="alert d-none mt-3 rounded-3 p-3 fs-3 shadow-sm"></div>

        <!-- Panduan Penggunaan -->
        <div class="mt-4 p-3 rounded-3 border" style="background-color: #fcf8f2; border-color: #e8decb !important;">
          <h6 class="fw-bold mb-2" style="color: #6e4727;"><i class="ti ti-help-circle me-1"></i> Petunjuk Presensi:</h6>
          <ul class="mb-0 fs-2 text-muted ps-3">
            <li>Santri/Siswa berpengunjung cukup **menembakkan alat scan** ke barcode ID Card.</li>
            <li>Jika tidak membawa kartu, ketik nama di kolom dan klik pilihan nama Anda.</li>
            <li>Data kunjungan Anda otomatis tercatat dalam sistem statistik perpustakaan.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Tabel Rekap Hari Ini -->
    <div class="col-lg-6">
      <div class="visitor-kiosk-card p-4 p-md-5 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 class="fw-bold text-dark mb-0"><i class="ti ti-users text-primary me-1"></i> Pengunjung Hari Ini</h5>
            <small class="text-muted">Daftar kehadiran santri & pengunjung</small>
          </div>
          <span class="badge px-3 py-2 rounded-pill fs-3 fw-bold text-white" style="background-color: #6e4727;" id="todayVisitorCountBadge">
            Total: <?= $todayCount; ?> Orang
          </span>
        </div>

        <div class="table-responsive rounded-4 border overflow-hidden shadow-sm" style="max-height: 400px; overflow-y: auto; border-color: #e8decb !important;">
          <table class="table table-hover align-middle mb-0 fs-2" id="visitorTodayTable">
            <thead style="background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%); color: #ffffff;">
              <tr>
                <th class="py-3 px-3 text-white fw-bold">Jam</th>
                <th class="py-3 text-white fw-bold">Nama Pengunjung</th>
                <th class="py-3 px-3 text-white fw-bold">Instansi / Kelas</th>
              </tr>
            </thead>
            <tbody id="visitorTodayTbody" style="background-color: #ffffff;">
              <?php if (empty($todayLogs)) : ?>
                <tr id="emptyRow">
                  <td colspan="3" class="text-center py-4 text-muted">
                    <i class="ti ti-id-badge-off fs-6 d-block mb-1 opacity-50"></i>
                    Belum ada kunjungan yang tercatat hari ini.
                  </td>
                </tr>
              <?php else : ?>
                <?php foreach ($todayLogs as $log) : ?>
                  <tr style="border-bottom: 1px solid #f8f2e6;">
                    <td class="fw-bold text-nowrap px-3" style="color: #8b5e3c;">
                      <i class="ti ti-clock me-1"></i><?= date('H:i', strtotime($log['created_at'])); ?>
                    </td>
                    <td class="fw-bold" style="color: #2d241e;">
                      <?= esc($log['visitor_name']); ?>
                      <?php if (!empty($log['uid'])) : ?>
                        <small class="d-block font-monospace text-muted fs-1">[<?= esc($log['uid']); ?>]</small>
                      <?php endif; ?>
                    </td>
                    <td class="px-3" style="color: #6e4727;">
                      <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #f8f2e6; color: #6e4727; border: 1px solid #e8decb;">
                        <?= esc($log['institution'] ?: 'Umum'); ?> <?= !empty($log['class_level']) ? '- ' . esc($log['class_level']) : ''; ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Modal Welcome Popup -->
<div class="modal fade" id="welcomeVisitorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg text-center p-4" style="background-color: #fcf8f2;">
      <div class="modal-body">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3 shadow" style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #059669);">
          <i class="ti ti-circle-check fs-1"></i>
        </div>
        <h3 class="fw-bold mb-1" style="color: #6e4727;" id="modalVisitorName">Selamat Datang!</h3>
        <p class="fs-4 text-muted mb-3" id="modalVisitorMeta">Instansi - Kelas</p>
        <div class="badge px-4 py-2 rounded-pill fs-3 text-white mb-3" style="background-color: #8b5e3c;">
          <i class="ti ti-clock me-1"></i> Ter-presensi pada jam <span id="modalVisitorTime">00:00</span> WIB
        </div>
        <p class="fs-2 text-dark mb-0 fw-semibold">Selamat membaca & mengeksplorasi pustaka Mlangi! ✨</p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  let searchTimeout = null;

  // Real-time Clock
  function updateLiveClock() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    document.getElementById('liveClock').textContent = timeStr;
    document.getElementById('liveDateStr').textContent = dateStr;
  }
  setInterval(updateLiveClock, 1000);
  updateLiveClock();

  const searchInput = document.getElementById('visitorSearchInput');
  const autocompleteList = document.getElementById('autocompleteList');

  // Event listener autocomplete pencarian nama
  searchInput.addEventListener('input', function() {
    const val = this.value.trim();
    clearTimeout(searchTimeout);

    if (val.length < 2) {
      autocompleteList.classList.add('d-none');
      return;
    }

    searchTimeout = setTimeout(async function() {
      try {
        const res = await fetch(`<?= base_url('buku-tamu/search-member'); ?>?q=${encodeURIComponent(val)}`);
        const items = await res.json();

        if (items.length > 0) {
          let html = '';
          items.forEach(item => {
            html += `<div class="search-item-option" onclick="selectMemberOption('${item.name.replace(/'/g, "\\'")}', '${item.uid}')">
              <strong style="color: #6e4727;">${item.name}</strong> 
              <small class="text-muted ms-1">${item.institution ? '- ' + item.institution : ''} ${item.class_level ? '(' + item.class_level + ')' : ''}</small>
              <div class="font-monospace fs-1 text-primary">[${item.uid}]</div>
            </div>`;
          });
          autocompleteList.innerHTML = html;
          autocompleteList.classList.remove('d-none');
        } else {
          autocompleteList.classList.add('d-none');
        }
      } catch (err) {
        console.error(err);
      }
    }, 250);
  });

  function selectMemberOption(name, uid) {
    searchInput.value = uid || name;
    autocompleteList.classList.add('d-none');
    submitVisitorCheckin();
  }

  // Hide autocomplete when clicking outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !autocompleteList.contains(e.target)) {
      autocompleteList.classList.add('d-none');
    }
  });

  // Submit checkin presensi
  async function submitVisitorCheckin() {
    const val = searchInput.value.trim();
    const btn = document.getElementById('btnSubmitVisitor');
    const alertDiv = document.getElementById('visitorAlert');

    if (!val) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    try {
      const formData = new FormData();
      formData.append('search_input', val);

      const res = await fetch('<?= base_url('buku-tamu/checkin'); ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });

      const result = await res.json();

      if (result.status) {
        // Reset Input
        searchInput.value = '';
        autocompleteList.classList.add('d-none');

        // Tampilkan Modal Ucapan Selamat Datang
        document.getElementById('modalVisitorName').textContent = `Selamat Datang, ${result.visitor_name}!`;
        document.getElementById('modalVisitorMeta').textContent = `${result.institution} ${result.class_level && result.class_level !== '-' ? '- ' + result.class_level : ''}`;
        document.getElementById('modalVisitorTime').textContent = result.time;

        const welcomeModal = new bootstrap.Modal(document.getElementById('welcomeVisitorModal'));
        welcomeModal.show();

        setTimeout(() => {
          welcomeModal.hide();
          searchInput.focus();
        }, 3500);

        // Update Tabel Hari Ini
        document.getElementById('todayVisitorCountBadge').textContent = `Total: ${result.todayCount} Orang`;
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

        const tbody = document.getElementById('visitorTodayTbody');
        const newRowHtml = `
          <tr style="background-color: #f8f2e6; transition: background 1.5s ease;">
            <td class="fw-bold text-nowrap px-3" style="color: #8b5e3c;"><i class="ti ti-clock me-1"></i>${result.time}</td>
            <td class="fw-bold" style="color: #2d241e;">${result.visitor_name}</td>
            <td class="px-3" style="color: #6e4727;"><span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #e8decb; color: #6e4727;">${result.institution} ${result.class_level !== '-' ? '- ' + result.class_level : ''}</span></td>
          </tr>
        `;
        tbody.insertAdjacentHTML('afterbegin', newRowHtml);

      } else {
        alertDiv.className = 'alert alert-danger mt-3 rounded-3 p-3 fs-3 shadow-sm';
        alertDiv.innerHTML = result.message;
        alertDiv.classList.remove('d-none');
      }
    } catch (err) {
      console.error(err);
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-check me-1"></i> Masuk';
      setTimeout(() => searchInput.focus(), 100);
    }
  }
</script>
<?= $this->endSection() ?>
