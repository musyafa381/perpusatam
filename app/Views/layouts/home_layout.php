<?php 
$isHomePage = (uri_string() === '' || uri_string() === '/' || uri_string() === 'home');
$isTvPage   = (uri_string() === 'tv' || uri_string() === 'tv-display');
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Georgia&display=swap" rel="stylesheet">

  <?= $this->include('layouts/head') ?>

  <!-- Extra head -->
  <?= $this->renderSection('head') ?>

  <link rel="stylesheet" href="<?= base_url('assets/css/home.css?v=' . time()); ?>">
  <style>
    html, body, .page-wrapper, .body-wrapper, #main-wrapper, .unida-main-body-wrapper {
      background-color: #faf7f2 !important;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='70' height='70' viewBox='0 0 70 70'%3E%3Cg fill='none' stroke='%236e4727' stroke-width='1.2' stroke-opacity='0.065'%3E%3Cellipse cx='35' cy='17.5' rx='14' ry='17.5'/%3E%3Cellipse cx='35' cy='52.5' rx='14' ry='17.5'/%3E%3Cellipse cx='17.5' cy='35' rx='17.5' ry='14'/%3E%3Cellipse cx='52.5' cy='35' rx='17.5' ry='14'/%3E%3Ccircle cx='35' cy='35' r='5' fill='%23c59b27' fill-opacity='0.08' stroke='%2359391f' stroke-width='0.8' stroke-opacity='0.08'/%3E%3Ccircle cx='0' cy='0' r='4' fill='%23c59b27' fill-opacity='0.06'/%3E%3Ccircle cx='70' cy='0' r='4' fill='%23c59b27' fill-opacity='0.06'/%3E%3Ccircle cx='0' cy='70' r='4' fill='%23c59b27' fill-opacity='0.06'/%3E%3Ccircle cx='70' cy='70' r='4' fill='%23c59b27' fill-opacity='0.06'/%3E%3C/g%3E%3C/svg%3E") !important;
      background-repeat: repeat !important;
      background-attachment: fixed !important;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* 8% Translucent Container Cards with Backdrop Blur */
    .translucent-container-8 {
      background: linear-gradient(135deg, rgba(252, 248, 242, 0.92) 0%, rgba(244, 234, 224, 0.92) 100%) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
    }

    .translucent-card-8 {
      background: rgba(255, 255, 255, 0.92) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
    }
    
    /* -------------------------------------------------------------
       UNIDA GONTOR 2-TIER TOPBAR & NAVBAR (Harmonized Theme Colors)
       ------------------------------------------------------------- */
    
    /* Tier 1: Dark Utility Ribbon Bar */
    .unida-utility-bar {
      background: #3d230e;
      color: rgba(255, 255, 255, 0.85);
      font-size: 0.75rem;
      padding: 6px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .unida-utility-bar a {
      color: rgba(255, 255, 255, 0.85);
      text-decoration: none;
      transition: color 0.2s ease;
    }
    .unida-utility-bar a:hover {
      color: #f0c968;
    }

    /* Tier 2: Main Header Navbar */
    .unida-main-navbar {
      background: #59391f;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      padding: 10px 0;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    /* Center Pill Capsule Nav Container (Exact UNIDA Style) */
    .unida-nav-capsule {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 50px;
      padding: 4px 6px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .unida-nav-item {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 700;
      font-size: 0.78rem;
      padding: 7px 16px;
      border-radius: 50px;
      text-decoration: none;
      transition: all 0.2s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .unida-nav-item:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
    }
    .unida-nav-item.active {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.4);
      box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.15);
    }

    /* Right Action Pill Group (Search Circle + Login White Pill) */
    .unida-action-capsule {
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      border-radius: 50px;
      padding: 4px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .unida-search-circle-btn {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.2s ease;
      border: none;
    }
    .unida-search-circle-btn:hover {
      background: #c59b27;
      color: #2d1e18;
    }

    .unida-login-pill-btn {
      background: #ffffff;
      color: #59391f !important;
      font-weight: 800;
      font-size: 0.825rem;
      border-radius: 50px;
      padding: 7px 20px;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      transition: all 0.2s ease;
    }
    .unida-login-pill-btn:hover {
      background: #c59b27;
      color: #ffffff !important;
      transform: translateY(-1px);
    }

    /* -------------------------------------------------------------
       UNIDA GONTOR STYLE SEARCH LOADING ANIMATION OVERLAY
       ------------------------------------------------------------- */
    .unida-search-loader-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(40, 22, 10, 0.95);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      z-index: 999999;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      opacity: 0;
      transition: opacity 0.5s ease-in-out;
      pointer-events: all;
    }
    .unida-search-loader-overlay.show {
      opacity: 1;
    }

    .unida-pulse-circle-wrapper {
      position: relative;
      width: 150px;
      height: 150px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .unida-pulse-ring {
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 50%;
      border: 2px solid rgba(240, 201, 104, 0.65);
      animation: unidaPulseRing 2.4s infinite cubic-bezier(0.25, 1, 0.5, 1);
    }

    @keyframes unidaPulseRing {
      0% { transform: scale(0.8); opacity: 0.95; }
      50% { transform: scale(1.22); opacity: 0.45; }
      100% { transform: scale(1.55); opacity: 0; }
    }

    .unida-search-icon-circle {
      width: 84px;
      height: 84px;
      border-radius: 50%;
      background: #ffffff;
      color: #59391f;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.45);
      z-index: 2;
    }

    .unida-loader-subtitle {
      font-size: 0.92rem;
      letter-spacing: 2px;
      color: rgba(255, 255, 255, 0.75);
      font-weight: 600;
      display: block;
    }

    .unida-loader-title {
      font-size: 1.9rem;
      color: #ffffff;
      font-family: 'Georgia', serif;
      text-shadow: 0 2px 10px rgba(0,0,0,0.35);
    }

    .unida-progress-container {
      width: 320px;
      height: 4px;
      background: rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      overflow: hidden;
      position: relative;
    }

    .unida-progress-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(90deg, #c59b27 0%, #f0c968 100%);
      border-radius: 10px;
      animation: unidaProgressBar 2.6s infinite ease-in-out;
    }

    @keyframes unidaProgressBar {
      0% { width: 0%; transform: translateX(0%); }
      50% { width: 70%; transform: translateX(25%); }
      100% { width: 100%; transform: translateX(0%); }
    }
  </style>
</head>

<body>

  <!-- =========================================================
       UNIDA GONTOR STYLE SEARCH LOADING ANIMATION OVERLAY
       ========================================================= -->
  <div id="unidaSearchLoader" class="unida-search-loader-overlay d-none">
    <div class="unida-loader-content text-center px-3">
      <!-- Animated Pulse Ring & Magnifying Glass Icon -->
      <div class="unida-pulse-circle-wrapper mx-auto mb-4">
        <div class="unida-pulse-ring"></div>
        <div class="unida-search-icon-circle">
          <i class="ti ti-search fs-2"></i>
        </div>
      </div>

      <!-- Search Term Heading -->
      <span class="unida-loader-subtitle text-uppercase">Mencari</span>
      <h3 id="unidaSearchKeywordText" class="unida-loader-title fw-extrabold mb-3">"Mencari..."</h3>

      <!-- Animated Progress Bar -->
      <div class="unida-progress-container mx-auto mb-2">
        <div class="unida-progress-bar"></div>
      </div>

      <small class="unida-loader-status text-white-50">Menyiapkan tampilan...</small>
    </div>
  </div>

  <!-- =========================================================
       STICKY MAIN HEADER NAVBAR (Tier 1 Scrollable, Tier 2 Paten Di Atas)
       ========================================================= -->
  <style>
    .unida-main-navbar {
      transition: background-color 0.25s ease, box-shadow 0.25s ease;
      background: #59391f;
      border-bottom: 1px solid rgba(255, 255, 255, 0.12);
      padding: 10px 0;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }
    .unida-main-navbar.is-fixed-top {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      right: 0 !important;
      width: 100% !important;
      z-index: 1040 !important;
      background: rgba(89, 57, 31, 0.98) !important;
      backdrop-filter: blur(12px) !important;
      -webkit-backdrop-filter: blur(12px) !important;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25) !important;
    }
    .unida-main-body-wrapper {
      padding-top: 0px;
    }
  </style>

  <?php if (!$isTvPage) : ?>
    <!-- TIER 1: DARK UTILITY RIBBON BAR (Normal Flow - Bisa Di-scroll Ikut Halaman) -->
    <div class="unida-utility-bar d-none d-md-block">
      <div class="container-fluid px-3 px-md-4">
        <div class="d-flex align-items-center justify-content-between">
          
          <!-- Left: Phone & Email Contact -->
          <div class="d-flex align-items-center gap-4">
            <a href="tel:081393128882" class="d-inline-flex align-items-center gap-2">
              <i class="ti ti-phone text-warning" style="color: #f0c968 !important;"></i>
              <span>0813-9312-8882</span>
            </a>
            <a href="mailto:perpusatku@gmail.com" class="d-inline-flex align-items-center gap-2">
              <i class="ti ti-mail text-warning" style="color: #f0c968 !important;"></i>
              <span>perpusatku@gmail.com</span>
            </a>
          </div>

          <!-- Right: Social Media Links -->
          <div class="d-flex align-items-center gap-3">
            <div class="d-inline-flex align-items-center gap-2 fs-6">
              <a href="https://youtube.com" target="_blank" title="YouTube Perpustakaan"><i class="ti ti-brand-youtube"></i></a>
              <a href="https://instagram.com" target="_blank" title="Instagram Perpustakaan"><i class="ti ti-brand-instagram"></i></a>
              <a href="https://tiktok.com" target="_blank" title="TikTok Perpustakaan"><i class="ti ti-brand-tiktok"></i></a>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- TIER 2: MAIN HEADER NAVBAR (Sticky Paten Di Atas Saat Di-scroll) -->
    <header class="unida-main-navbar">
      <div class="container-fluid px-3 px-md-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          
          <!-- Left: Official Logo & Brand Title -->
          <a href="<?= base_url(); ?>" class="d-flex align-items-center gap-3 text-decoration-none">
            <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan" class="flex-shrink-0" style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; border: 1.5px solid #f0c968;">
            <div class="d-flex flex-column justify-content-center">
              <h6 class="fw-extrabold mb-0 text-white" style="font-family: 'Georgia', serif; font-size: 0.98rem; letter-spacing: 0.5px; line-height: 1.2;">PERPUSTAKAAN PUSAT</h6>
              <small class="d-block" style="font-size: 0.72rem; font-weight: 700; color: #f0c968 !important; letter-spacing: 0.5px; margin-top: 2px;">ASSALAFIYYAH MLANGI</small>
            </div>
          </a>

          <!-- Center: Glassmorphism Pill Capsule Navigation Bar (Exact UNIDA Gontor) -->
          <div class="d-none d-lg-flex align-items-center">
            <div class="unida-nav-capsule">
              
              <a href="<?= base_url(); ?>" class="unida-nav-item <?= (uri_string() === '' || uri_string() === '/') ? 'active' : ''; ?>">
                <i class="ti ti-home"></i> HOME
              </a>

              <a href="<?= base_url('book'); ?>" class="unida-nav-item <?= (uri_string() === 'book' || str_contains(uri_string(), 'book/')) ? 'active' : ''; ?>">
                <i class="ti ti-books"></i> KATALOG BUKU
              </a>

              <a href="<?= base_url('buku-tamu'); ?>" class="unida-nav-item <?= uri_string() === 'buku-tamu' ? 'active' : ''; ?>">
                <i class="ti ti-id-badge-2"></i> BUKU TAMU
              </a>

              <a href="<?= base_url('tv'); ?>" target="_blank" class="unida-nav-item">
                <i class="ti ti-device-tv"></i> DISPLAY TV
              </a>

            </div>
          </div>

          <!-- Right: Dual Capsule Action Group (Search Circle + Login White Pill) -->
          <div class="d-flex align-items-center gap-2">
            <div class="unida-action-capsule">
              <a href="<?= base_url('book'); ?>" class="unida-search-circle-btn" title="Cari Pustaka">
                <i class="ti ti-search fs-5"></i>
              </a>
              <a href="<?= base_url('login'); ?>" class="unida-login-pill-btn">
                <i class="ti ti-login"></i> Login
              </a>
            </div>
          </div>

        </div>
      </div>
    </header>
  <?php endif; ?>

  <div class="page-wrapper unida-main-body-wrapper" id="main-wrapper">
    <div class="body-wrapper">
      <?= $this->renderSection('back') ?>
      <div class="w-100">
        <!-- Main content -->
        <?= $this->renderSection('content') ?>
      </div>

      <?php if (!$isTvPage) : ?>
      <!-- Footer UNIDA Style with Theme Colors -->
      <footer class="mt-5 py-4" style="background: #3d230e; color: rgba(255, 255, 255, 0.8); border-top: 3px solid #c59b27;">
        <div class="container text-center text-md-start">
          <div class="row align-items-center g-3">
            <div class="col-md-6">
              <h6 class="fw-extrabold text-white mb-1" style="font-family: 'Georgia', serif;">PERPUSTAKAAN PUSAT ASSALAFIYYAH MLANGI</h6>
              <small class="d-block" style="color: rgba(255, 255, 255, 0.65);">Pondok Pesantren Assalafiyyah Mlangi Sleman Yogyakarta</small>
            </div>
            <div class="col-md-6 text-md-end">
              <small style="color: rgba(255, 255, 255, 0.55);">&copy; <?= date('Y'); ?> Perpustakaan Assalafiyyah Mlangi. All rights reserved.</small>
            </div>
          </div>
        </div>
      </footer>
      <?php endif; ?>
    </div>
  </div>

  <!-- JavaScript Libraries & UNIDA Loader -->
  <script src="<?= base_url('assets/libs/jquery/jquery.min.js') ?>"></script>
  <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Sticky Paten Main Header Navbar Scroll Listener
      const mainNavbar = document.querySelector('.unida-main-navbar');
      const utilityBar = document.querySelector('.unida-utility-bar');
      if (mainNavbar) {
        function checkStickyNav() {
          const threshold = utilityBar ? utilityBar.offsetHeight : 32;
          if (window.scrollY >= threshold) {
            mainNavbar.classList.add('is-fixed-top');
          } else {
            mainNavbar.classList.remove('is-fixed-top');
          }
        }
        window.addEventListener('scroll', checkStickyNav, { passive: true });
        checkStickyNav();
      }

      // Smooth Full-Screen Search Loading Animation Handler
      const searchForms = document.querySelectorAll('form');
      const loader = document.getElementById('unidaSearchLoader');
      const keywordEl = document.getElementById('unidaSearchKeywordText');

      searchForms.forEach(form => {
        form.addEventListener('submit', function (e) {
          const searchInput = form.querySelector('input[name="search"]');
          if (!searchInput) return;

          if (form.dataset.submitting === 'true') return;

          e.preventDefault();

          const val = searchInput.value.trim();
          if (val !== '') {
            keywordEl.textContent = '"' + val + '"';
          } else {
            keywordEl.textContent = '"Memuat Katalog..."';
          }

          if (loader) {
            loader.classList.remove('d-none');
            requestAnimationFrame(() => {
              loader.classList.add('show');
            });
          }

          form.dataset.submitting = 'true';
          setTimeout(() => {
            form.submit();
          }, 650);
        });
      });
    });
  </script>

  <!-- Render Scripts Section from Child Views -->
  <?= $this->renderSection('scripts') ?>
</body>

</html>