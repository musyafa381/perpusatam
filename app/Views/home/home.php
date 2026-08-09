<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title>PERPUSTAKAAN YAYASAN ASSALAFIYYAH</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="public-dashboard-container">
  
  <!-- Header Bar (Theme Harmonized with Admin Panel) -->
  <header class="public-header-bar">
    <div class="header-left">
      <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan Assalafiyyah" class="header-admin-logo">
      <div class="header-title-box">
        <h1 class="header-main-title">PERPUSTAKAAN PUSAT</h1>
        <p class="header-sub-title">YAYASAN ASSALAFIYYAH MLANGI</p>
      </div>
    </div>

    <div class="header-center">
      <div class="clock-icon-wrapper">
        <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"></circle>
          <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
      </div>
      <div class="time-display-box">
        <div class="live-time" id="dashboardLiveClock">10:24</div>
        <div class="live-date" id="dashboardLiveDate">Selasa, 21 Juli 2026</div>
      </div>
    </div>

    <div class="header-right">
      <div class="quote-text">"Bacalah, maka kamu akan tahu"</div>
      <div class="quote-source">- QS. Al-'Alaq : 1</div>
    </div>
  </header>

  <!-- Main Grid Content (Single Screen Height Fit) -->
  <div class="public-main-grid">
    
    <!-- Left Column (Peminjam Aktif & Daftar Pengunjung - Maksimal 5 Data) -->
    <div class="public-left-col">
      
      <?php
      helper(['upload_helper', 'tv_helper']);
      $tvBanners = function_exists('getTvBanners') ? getTvBanners() : [];
      $loansPayload = [];
      if (!empty($activeLoans)) {
          foreach ($activeLoans as $ln) {
              $mName = trim(($ln['first_name'] ?? '') . ' ' . ($ln['last_name'] ?? ''));
              $loansPayload[] = [
                  'name'       => $mName ?: 'Anggota Perpustakaan',
                  'book'       => $ln['book_title'] ?? 'Buku Perpustakaan',
                  'loan_date'  => !empty($ln['loan_date']) ? date('d M Y', strtotime($ln['loan_date'])) : '-',
                  'due_date'   => !empty($ln['due_date']) ? date('d M Y', strtotime($ln['due_date'])) : '-',
              ];
          }
      }

      $visitorsPayload = [];
      if (!empty($todayVisitorLogs)) {
          foreach ($todayVisitorLogs as $vl) {
              $visitorsPayload[] = [
                  'name'  => $vl['visitor_name'] ?? 'Pengunjung',
                  'time'  => !empty($vl['created_at']) ? date('H:i', strtotime($vl['created_at'])) . ' WIB' : '-',
              ];
          }
      }
      ?>

      <!-- Section 1: Peminjam Aktif (Rotasi 1 Baris Ticker) -->
      <div class="pub-card peminjam-card">
        <div class="pub-card-header blue-header header-between">
          <div class="header-icon-title">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <span>PEMINJAM AKTIF</span>
          </div>
          <span class="pub-header-badge" id="loanCounterBadge">Sedang Meminjam</span>
        </div>
        
        <div class="single-row-ticker-container">
          <div class="single-row-ticker-box" id="loanTickerBox">
            <!-- Baris 1: Nama Peminjam -->
            <div class="ticker-row-1">
              <div class="user-avatar-circle">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              </div>
              <span class="ticker-user-name" id="loanMemberName">-</span>
            </div>

            <!-- Baris 2: Tanggal Peminjaman & Tenggat -->
            <div class="ticker-row-2">
              <span class="ticker-date-pill pill-borrow"><i class="ti ti-calendar me-1"></i>Pinjam: <strong id="loanDateVal" class="ms-1">-</strong></span>
              <span class="ticker-date-pill pill-due"><i class="ti ti-clock me-1"></i>Tempo: <strong id="loanDueVal" class="ms-1">-</strong></span>
            </div>

            <!-- Baris 3: Judul Buku -->
            <div class="ticker-row-3">
              <span class="ticker-book-badge"><i class="ti ti-book me-1"></i>BUKU:</span>
              <span class="ticker-book-title" id="loanBookTitle">-</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 2: Daftar Pengunjung (Rotasi 1 Baris Ticker) -->
      <div class="pub-card pengunjung-card">
        <div class="pub-card-header green-header header-between">
          <div class="header-icon-title">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
            </svg>
            <span>DAFTAR PENGUNJUNG</span>
          </div>
          <span class="pub-header-badge gold-badge" id="visitorCounterBadge">
            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" style="display:inline; margin-right:3px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Hari Ini
          </span>
        </div>

        <div class="single-row-ticker-container">
          <div class="single-row-ticker-box" id="visitorTickerBox">
            <div class="ticker-top-row">
              <div class="ticker-user-info">
                <div class="user-avatar-circle gold-avatar">
                  <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <span class="ticker-user-name" id="visitorNameVal">-</span>
              </div>
              <div class="ticker-dates-wrap">
                <span class="ticker-date-pill pill-time"><i class="ti ti-clock me-1"></i>Masuk: <strong id="visitorTimeVal" class="ms-1">-</strong></span>
              </div>
            </div>
            <div class="ticker-bottom-row">
              <span class="visitor-cat-badge"><i class="ti ti-id-badge-2 me-1"></i>PENGUNJUNG PERPUSTAKAAN</span>
            </div>
          </div>
        </div>

        <div class="pub-card-footer">
          <div class="visitor-total-pill">
            <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
            </svg>
            <span>Total Pengunjung Hari Ini: <strong><?= !empty($todayVisitorCount) ? $todayVisitorCount : 0 ?> orang</strong></span>
          </div>
        </div>
      </div>


    </div>

    <!-- Right Column (Koleksi Buku dengan Rotasi Otomatis Setiap 1 Menit) -->
    <div class="public-right-col">
      <div class="pub-card koleksi-card">
        <div class="pub-card-header blue-header header-between">
          <div class="header-icon-title">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <span>KOLEKSI BUKU</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="slide-indicator-text" id="slideIndicator">Halaman 1</span>
            <a href="<?= base_url('book') ?>" class="pub-btn-see-all">
              Lihat Semua <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
          </div>
        </div>

        <!-- Container Hero Spotlight + Queue Showcase -->
        <?php
        $dummyBooks = [
          ['title' => 'Sejarah Peradaban Islam', 'cat' => 'Sejarah', 'cat_cls' => 'tag-sejarah', 'stock' => 8, 'img' => base_url('assets/images/book_sejarah.png'), 'style' => 'cover-green'],
          ['title' => 'Pemrograman Web untuk Pemula', 'cat' => 'Teknologi', 'cat_cls' => 'tag-teknologi', 'stock' => 5, 'img' => base_url('assets/images/book_web.png'), 'style' => 'cover-darkblue'],
          ['title' => 'Fiqh Lengkap', 'cat' => 'Agama', 'cat_cls' => 'tag-agama', 'stock' => 3, 'img' => base_url('assets/images/book_fiqh.png'), 'style' => 'cover-gold'],
          ['title' => 'Bahasa Arab Dasar', 'cat' => 'Bahasa', 'cat_cls' => 'tag-bahasa', 'stock' => 7, 'img' => base_url('assets/images/book_arab.png'), 'style' => 'cover-sand'],
          ['title' => 'Sains untuk Semua', 'cat' => 'Sains', 'cat_cls' => 'tag-sains', 'stock' => 6, 'img' => base_url('assets/images/book_sains.png'), 'style' => 'cover-space'],
          ['title' => 'Kisah Para Nabi', 'cat' => 'Agama', 'cat_cls' => 'tag-agama', 'stock' => 10, 'img' => base_url('assets/images/book_nabi.png'), 'style' => 'cover-cream'],
          ['title' => 'Psikologi Remaja', 'cat' => 'Umum', 'cat_cls' => 'tag-umum', 'stock' => 4, 'img' => base_url('assets/images/book_psikologi.png'), 'style' => 'cover-teal'],
          ['title' => 'Matematika Dasar', 'cat' => 'Pendidikan', 'cat_cls' => 'tag-pendidikan', 'stock' => 6, 'img' => base_url('assets/images/book_math.png'), 'style' => 'cover-navy'],
        ];

        $allBooks = [];
        if (!empty($latestBooks)) {
          foreach ($latestBooks as $idx => $bk) {
            $coverUrl = !empty($bk['book_cover']) ? getBookCoverUrl($bk['book_cover']) : null;
            $hasRealCover = !empty($coverUrl) && ($coverUrl !== base_url(BOOK_COVER_URI . DEFAULT_BOOK_COVER));
            
            $allBooks[] = [
              'title'    => $bk['title'],
              'cat'      => $bk['category'] ?: 'Umum',
              'cat_cls'  => 'tag-default',
              'stock'    => (int)($bk['quantity'] ?? 0),
              'hasCover' => $hasRealCover,
              'coverUrl' => $coverUrl,
              'style'    => 'cover-default'
            ];
          }
        } else {
          foreach ($dummyBooks as $db) {
            $allBooks[] = [
              'title'    => $db['title'],
              'cat'      => $db['cat'],
              'cat_cls'  => $db['cat_cls'],
              'stock'    => $db['stock'],
              'hasCover' => true,
              'coverUrl' => $db['img'],
              'style'    => 'cover-default'
            ];
          }
        }
        ?>

        <div class="hero-queue-showcase" id="koleksiShowcase">
          <!-- Card Utama (Hero Book - Left Side) -->
          <div class="hero-book-slot" id="heroBookSlot">
            <div class="hero-card-inner">
              <div class="hero-cover-container">
                <img id="heroImg" src="" alt="" class="hero-book-img">
                <div id="heroMock" class="hero-mock-cover cover-default" style="display: none;">
                  <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                  <div id="heroMockTitle" class="hero-mock-title"></div>
                </div>
              </div>
              <div class="hero-details">
                <h3 id="heroTitle" class="hero-book-title"></h3>
                <div class="d-flex align-items-center gap-2 mt-2">
                  <span id="heroCat" class="hero-cat-tag"></span>
                  <span class="hero-stock-badge">Tersedia: <strong id="heroStock"></strong></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Queue Stack (Card 2 & Card 3 - Right Side Stacked) -->
          <div class="queue-books-stack">
            <!-- Card 2 (Top Right) -->
            <div class="queue-card-slot queue-card-top" id="queueCardTop">
              <div class="queue-card-inner">
                <div class="queue-cover-container">
                  <img id="q2Img" src="" alt="" class="queue-book-img">
                  <div id="q2Mock" class="queue-mock-cover cover-default" style="display: none;">
                    <div id="q2MockTitle" class="queue-mock-title"></div>
                  </div>
                </div>
                <div class="queue-details">
                  <h5 id="q2Title" class="queue-book-title"></h5>
                  <div class="queue-sub"><span id="q2Cat" class="queue-cat"></span> • Tersedia: <strong id="q2Stock"></strong></div>
                </div>
              </div>
            </div>

            <!-- Card 3 (Bottom Right) -->
            <div class="queue-card-slot queue-card-bottom" id="queueCardBottom">
              <div class="queue-card-inner">
                <div class="queue-cover-container">
                  <img id="q3Img" src="" alt="" class="queue-book-img">
                  <div id="q3Mock" class="queue-mock-cover cover-default" style="display: none;">
                    <div id="q3MockTitle" class="queue-mock-title"></div>
                  </div>
                </div>
                <div class="queue-details">
                  <h5 id="q3Title" class="queue-book-title"></h5>
                  <div class="queue-sub"><span id="q3Cat" class="queue-cat"></span> • Tersedia: <strong id="q3Stock"></strong></div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>

  <!-- Bottom Footer Bar -->
  <footer class="public-bottom-bar">
    <div class="bottom-left-msg">
      <div class="bottom-book-icon">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
          <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
        </svg>
      </div>
      <div class="footer-msg-text">
        <div class="font-bold">Jaga buku, jaga ilmu, jaga masa depan.</div>
        <div class="footer-msg-sub">Terima kasih telah berkunjung ke perpustakaan.</div>
      </div>
    </div>

    <div class="bottom-right-wifi">
      <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
        <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
        <line x1="12" y1="20" x2="12.01" y2="20"></line>
      </svg>
      <span>Wi-Fi: <strong>Perpus_Assalafiyyah</strong></span>
      <span class="wifi-sep">|</span>
      <span>Password: <strong>membaca123</strong></span>
    </div>
  </footer>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // Realtime Clock Update
  function updateDashboardClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('dashboardLiveClock').textContent = `${hours}:${minutes}`;

    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[now.getDay()];
    const dateNum = now.getDate();
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();

    document.getElementById('dashboardLiveDate').textContent = `${dayName}, ${dateNum} ${monthName} ${year}`;
  }

  setInterval(updateDashboardClock, 1000);
  updateDashboardClock();

  // Global Live Data Store
  window.liveDataStore = {
    books: <?= json_encode($allBooks); ?>,
    loans: <?= json_encode($loansPayload); ?>,
    visitors: <?= json_encode($visitorsPayload); ?>,
    tvBanners: <?= json_encode($tvBanners ?? []); ?>
  };

  // Rotasi Antrean Koleksi Buku (Hero Spotlight + Queue Rotation)
  (function() {
    let currentIndex = 0;

    function applyState(b1, b2, b3) {
      if (!b1 || !b2 || !b3) return;
      document.getElementById('heroTitle').textContent = b1.title;
      document.getElementById('heroCat').textContent = b1.cat;
      document.getElementById('heroStock').textContent = b1.stock;
      if (b1.hasCover && b1.coverUrl) {
        document.getElementById('heroImg').src = b1.coverUrl;
        document.getElementById('heroImg').style.display = 'block';
        document.getElementById('heroMock').style.display = 'none';
      } else {
        document.getElementById('heroImg').style.display = 'none';
        document.getElementById('heroMock').style.display = 'flex';
        document.getElementById('heroMockTitle').textContent = b1.title;
      }

      document.getElementById('q2Title').textContent = b2.title;
      document.getElementById('q2Cat').textContent = b2.cat;
      document.getElementById('q2Stock').textContent = b2.stock;
      if (b2.hasCover && b2.coverUrl) {
        document.getElementById('q2Img').src = b2.coverUrl;
        document.getElementById('q2Img').style.display = 'block';
        document.getElementById('q2Mock').style.display = 'none';
      } else {
        document.getElementById('q2Img').style.display = 'none';
        document.getElementById('q2Mock').style.display = 'flex';
        document.getElementById('q2MockTitle').textContent = b2.title;
      }

      document.getElementById('q3Title').textContent = b3.title;
      document.getElementById('q3Cat').textContent = b3.cat;
      document.getElementById('q3Stock').textContent = b3.stock;
      if (b3.hasCover && b3.coverUrl) {
        document.getElementById('q3Img').src = b3.coverUrl;
        document.getElementById('q3Img').style.display = 'block';
        document.getElementById('q3Mock').style.display = 'none';
      } else {
        document.getElementById('q3Img').style.display = 'none';
        document.getElementById('q3Mock').style.display = 'flex';
        document.getElementById('q3MockTitle').textContent = b3.title;
      }
    }

    function rotateQueueDisplay() {
      const books = window.liveDataStore.books || [];
      if (books.length === 0) return;
      const total = books.length;
      const b1 = books[currentIndex % total];
      const b2 = books[(currentIndex + 1) % total];
      const b3 = books[(currentIndex + 2) % total];

      const showcase = document.getElementById('koleksiShowcase');
      if (showcase) {
        showcase.classList.add('queue-rotating');
        setTimeout(() => {
          applyState(b1, b2, b3);
          showcase.classList.remove('queue-rotating');
        }, 300);
      } else {
        applyState(b1, b2, b3);
      }

      const indicator = document.getElementById('slideIndicator');
      if (indicator) {
        indicator.textContent = `Buku ${currentIndex + 1} dari ${total}`;
      }
    }

    rotateQueueDisplay();
    setInterval(function() {
      const books = window.liveDataStore.books || [];
      if (books.length > 1) {
        currentIndex = (currentIndex + 1) % books.length;
        rotateQueueDisplay();
      }
    }, 6000);
  })();

  // Single-Row Rotating Ticker for Peminjam Aktif (Rotasi t = 0s, 5s, 10s)
  (function() {
    const box = document.getElementById('loanTickerBox');
    const badge = document.getElementById('loanCounterBadge');
    if (!box) return;

    let idx = 0;
    function renderLoan() {
      const loans = window.liveDataStore.loans || [];
      if (!loans || loans.length === 0) {
        document.getElementById('loanMemberName').textContent = 'Belum ada peminjam aktif';
        document.getElementById('loanBookTitle').textContent = '-';
        document.getElementById('loanDateVal').textContent = '-';
        document.getElementById('loanDueVal').textContent = '-';
        if (badge) badge.textContent = 'Sedang Meminjam';
        return;
      }

      const item = loans[idx % loans.length];
      box.classList.add('ticker-animating');
      setTimeout(() => {
        document.getElementById('loanMemberName').textContent = item.name;
        document.getElementById('loanBookTitle').textContent = item.book;
        document.getElementById('loanDateVal').textContent = item.loan_date;
        document.getElementById('loanDueVal').textContent = item.due_date;
        if (badge) badge.textContent = `Peminjam ${idx + 1} dari ${loans.length}`;
        box.classList.remove('ticker-animating');
      }, 300);
    }

    window.renderLoanNow = renderLoan;

    renderLoan();
    setInterval(() => {
      const loans = window.liveDataStore.loans || [];
      if (loans.length > 1) {
        idx = (idx + 1) % loans.length;
        renderLoan();
      }
    }, 5000);
  })();

  // Single-Row Rotating Ticker for Daftar Pengunjung (Rotasi t = 1s, 6s, 11s - JEDA 1 DETIK!)
  (function() {
    const box = document.getElementById('visitorTickerBox');
    const badge = document.getElementById('visitorCounterBadge');
    if (!box) return;

    let idx = 0;
    function renderVisitor() {
      const visitors = window.liveDataStore.visitors || [];
      if (!visitors || visitors.length === 0) {
        document.getElementById('visitorNameVal').textContent = 'Belum ada pengunjung hari ini';
        document.getElementById('visitorTimeVal').textContent = '-';
        if (badge) badge.textContent = 'Hari Ini';
        return;
      }

      const item = visitors[idx % visitors.length];
      box.classList.add('ticker-animating');
      setTimeout(() => {
        document.getElementById('visitorNameVal').textContent = item.name;
        document.getElementById('visitorTimeVal').textContent = item.time;
        if (badge) badge.textContent = `Pengunjung ${idx + 1} dari ${visitors.length}`;
        box.classList.remove('ticker-animating');
      }, 300);
    }

    window.renderVisitorNow = renderVisitor;

    // Jeda 1 Detik (1000ms offset) agar pergantian tidak bersamaan!
    setTimeout(function() {
      renderVisitor();
      setInterval(() => {
        const visitors = window.liveDataStore.visitors || [];
        if (visitors.length > 1) {
          idx = (idx + 1) % visitors.length;
          renderVisitor();
        }
      }, 5000);
    }, 1000);
  })();


  // Real-Time Background Auto-Sync (Sinkronisasi Data Terbaru Setiap 3 Detik Tanpa Reload)
  (function() {
    async function syncLiveData() {
      try {
        const res = await fetch('<?= base_url('api/live-data'); ?>');
        if (!res.ok) return;
        const json = await res.json();
        if (json.status) {
          if (json.books) window.liveDataStore.books = json.books;
          if (json.loans) {
            const oldLen = (window.liveDataStore.loans || []).length;
            window.liveDataStore.loans = json.loans;
            if (window.renderLoanNow && (oldLen !== json.loans.length || oldLen === 0)) {
              window.renderLoanNow();
            }
          }
          if (json.visitors) {
            const oldLenV = (window.liveDataStore.visitors || []).length;
            window.liveDataStore.visitors = json.visitors;
            if (window.renderVisitorNow && (oldLenV !== json.visitors.length || oldLenV === 0)) {
              window.renderVisitorNow();
            }
          }
          if (json.tvBanners) window.liveDataStore.tvBanners = json.tvBanners;
          
          const totalVisitorEl = document.querySelector('.visitor-total-pill strong');
          if (totalVisitorEl && json.todayVisitorCount !== undefined) {
            totalVisitorEl.textContent = `${json.todayVisitorCount} orang`;
          }
        }
      } catch (err) {
        console.error('Real-time auto-sync error:', err);
      }
    }

    setInterval(syncLiveData, 3000);
  })();
</script>
<?= $this->endSection() ?>