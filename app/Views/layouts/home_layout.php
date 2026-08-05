<?php 
$isHomePage = (uri_string() === '' || uri_string() === '/' || uri_string() === 'home');
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <?= $this->include('layouts/head') ?>

  <!-- Extra head -->
  <?= $this->renderSection('head') ?>

  <link rel="stylesheet" href="<?= base_url('assets/css/home.css?v=' . time()); ?>">
</head>

<body class="<?= $isHomePage ? 'public-kiosk-mode' : ''; ?>">


  <div class="page-wrapper" id="main-wrapper">
    <div class="body-wrapper">
      <?= $this->renderSection('back') ?>
      <div class="w-100">
        <!-- Main content -->
        <?= $this->renderSection('content') ?>
      </div>

      <?php if (!$isHomePage): ?>
      <footer class="mt-5 py-4 border-top bg-white">
        <div class="container text-center text-md-start">
          <div class="row align-items-center">
            <div class="col-md-6 mb-2 mb-md-0">
              <span class="fw-bold text-dark fs-6">Perpustakaan Assalafiyyah Mlangi</span> &copy; <?= date('Y') ?>. Pameran Pustaka & Keilmuan.
            </div>
            <div class="col-md-6 text-md-end text-muted">
              <small><i class="ti ti-map-pin me-1"></i> Dusun Mlangi, Nogotirto, Gamping, Sleman, DI Yogyakarta</small>
            </div>
          </div>
        </div>
      </footer>
      <?php endif; ?>
    </div>
  </div>

  <!-- Scripts -->
  <?= $this->include('imports/scripts/basic_scripts') ?>

  <!-- Extra scripts -->
  <?= $this->renderSection('scripts') ?>
  <script>
    (function() {
      if (window.location.pathname.includes('buku-tamu')) {
        let isHovered = false;
        const header = document.querySelector('.gramedia-header');
        const topUtil = document.querySelector('.gramedia-top-utility');
        const trigger = document.getElementById('navHoverTrigger');

        function checkAutoHide() {
          if (window.scrollY > 50) {
            document.body.classList.add('autohide-nav-active');
          } else {
            document.body.classList.remove('autohide-nav-active');
          }
        }

        window.addEventListener('scroll', checkAutoHide);

        [header, topUtil, trigger].forEach(el => {
          if (!el) return;
          el.addEventListener('mouseenter', () => {
            document.body.classList.remove('autohide-nav-active');
          });
          el.addEventListener('mouseleave', () => {
            checkAutoHide();
          });
        });
      }
    })();
  </script>
</body>

</html>