<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Dashboard Perpustakaan</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0 overflow-hidden position-relative">
  <div class="card-body p-4 p-lg-5 position-relative z-1">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div class="badge bg-white text-primary fw-bold px-3 py-2 mb-3 rounded-pill fs-2 shadow-sm border border-light d-inline-flex align-items-center">
          <i class="ti ti-building-arch me-1 fs-4 text-warning"></i> Sistem Informasi Perpustakaan
        </div>
        <h2 class="text-white fw-extrabold mb-2 display-6" style="letter-spacing: -0.5px;">Selamat Datang di Panel Kontrol</h2>
        <p class="text-white-50 mb-0 fs-3" style="max-width: 650px;">Pantau seluruh statistik peminjaman, statistik buku, dan aktivitas transaksi perpustakaan secara real-time.</p>
      </div>
      <div class="d-flex gap-2 align-items-center flex-wrap">
        <a href="<?= base_url('admin/loans/new/members/search'); ?>" class="btn btn-light text-primary fw-bold shadow-sm px-4 py-2 rounded-3 d-inline-flex align-items-center">
          <i class="ti ti-plus me-2 fs-4"></i> Peminjaman Baru
        </a>
        <a href="<?= base_url('admin/returns'); ?>" class="btn btn-outline-light text-white fw-bold shadow-sm px-4 py-2 rounded-3 d-inline-flex align-items-center" style="border-color: rgba(255,255,255,0.4);">
          <i class="ti ti-rotate-clockwise me-2 fs-4"></i> Pengembalian
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Primary Metric Stat Cards Grid -->
<div class="row g-3 mb-4">
  <!-- Total Transaksi -->
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="<?= base_url('admin/loans'); ?>" class="text-decoration-none">
      <div class="card info-card border-0 h-100 shadow-sm overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 h-100 bg-primary" style="width: 4px;"></div>
        <div class="card-body p-3 ps-4">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="member-avatar me-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%);">
                <i class="ti ti-arrows-exchange"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Transaksi</small>
                <h3 class="fw-extrabold text-dark mb-0 fs-5"><?= $totalTransactionsCount; ?> <span class="fs-2 fw-semibold text-muted">Transaksi</span></h3>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-light-subtle text-primary rounded-pill px-2 py-1 fs-1 fw-bold"><i class="ti ti-chevron-right"></i></span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Peminjaman Aktif -->
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="<?= base_url('admin/loans'); ?>" class="text-decoration-none">
      <div class="card info-card border-0 h-100 shadow-sm overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 h-100 bg-warning" style="width: 4px;"></div>
        <div class="card-body p-3 ps-4">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="member-avatar me-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: linear-gradient(135deg, #8b5e3c 0%, #c59b27 100%);">
                <i class="ti ti-book"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 0.5px;">Peminjaman Aktif</small>
                <h3 class="fw-extrabold text-dark mb-0 fs-5"><?= $activeLoansCount; ?> <span class="fs-2 fw-semibold text-muted">Peminjaman</span></h3>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-light-subtle text-primary rounded-pill px-2 py-1 fs-1 fw-bold"><i class="ti ti-chevron-right"></i></span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Pengembalian Selesai -->
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="<?= base_url('admin/returns'); ?>" class="text-decoration-none">
      <div class="card info-card border-0 h-100 shadow-sm overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 h-100 bg-success" style="width: 4px;"></div>
        <div class="card-body p-3 ps-4">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="member-avatar me-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="ti ti-circle-check"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 0.5px;">Pengembalian Selesai</small>
                <h3 class="fw-extrabold text-dark mb-0 fs-5"><?= $returnsCount; ?> <span class="fs-2 fw-semibold text-muted">Pengembalian</span></h3>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-light-subtle text-primary rounded-pill px-2 py-1 fs-1 fw-bold"><i class="ti ti-chevron-right"></i></span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Total Anggota -->
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="<?= base_url('admin/members'); ?>" class="text-decoration-none">
      <div class="card info-card border-0 h-100 shadow-sm overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 h-100 bg-warning" style="width: 4px;"></div>
        <div class="card-body p-3 ps-4">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="member-avatar me-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: linear-gradient(135deg, #8b5e3c 0%, #c59b27 100%);">
                <i class="ti ti-users"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Anggota Terdaftar</small>
                <h3 class="fw-extrabold text-dark mb-0 fs-5"><?= count($members); ?> <span class="fs-2 fw-semibold text-muted">Anggota</span></h3>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-light-subtle text-primary rounded-pill px-2 py-1 fs-1 fw-bold"><i class="ti ti-chevron-right"></i></span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Judul Buku & Stok -->
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="<?= base_url('admin/books'); ?>" class="text-decoration-none">
      <div class="card info-card border-0 h-100 shadow-sm overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 h-100 bg-primary" style="width: 4px;"></div>
        <div class="card-body p-3 ps-4">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="member-avatar me-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: linear-gradient(135deg, #6e4727 0%, #c59b27 100%);">
                <i class="ti ti-books"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 0.5px;">Katalog Buku & Stok</small>
                <h3 class="fw-extrabold text-dark mb-0 fs-5"><?= count($books); ?> <span class="fs-2 fw-semibold text-muted">Judul (<?= $totalBookStock; ?> Eksemplar)</span></h3>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-light-subtle text-primary rounded-pill px-2 py-1 fs-1 fw-bold"><i class="ti ti-chevron-right"></i></span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>

  <!-- Rak & Kategori -->
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="<?= base_url('admin/categories'); ?>" class="text-decoration-none">
      <div class="card info-card border-0 h-100 shadow-sm overflow-hidden position-relative">
        <div class="position-absolute top-0 start-0 h-100 bg-warning" style="width: 4px;"></div>
        <div class="card-body p-3 ps-4">
          <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
              <div class="member-avatar me-3 shadow-sm" style="width: 52px; height: 52px; font-size: 1.35rem; background: linear-gradient(135deg, #c59b27 0%, #8b5e3c 100%);">
                <i class="ti ti-category"></i>
              </div>
              <div>
                <small class="text-muted fw-bold d-block text-uppercase tracking-wider" style="font-size: 0.72rem; letter-spacing: 0.5px;">Kategori & Rak Buku</small>
                <h3 class="fw-extrabold text-dark mb-0 fs-5"><?= count($categories); ?> <span class="fs-2 fw-semibold text-muted">Kategori (<?= count($racks); ?> Rak)</span></h3>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-light-subtle text-primary rounded-pill px-2 py-1 fs-1 fw-bold"><i class="ti ti-chevron-right"></i></span>
            </div>
          </div>
        </div>
      </div>
    </a>
  </div>
</div>

<!-- REPORT TODAY CARD -->
<div class="card info-card border-0 mb-4 shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
      <div>
        <h5 class="fw-bold text-dark mb-1 d-flex align-items-center">
          <i class="ti ti-calendar-event text-primary fs-6 me-2"></i> Laporan Aktivitas Hari Ini
        </h5>
        <small class="text-muted">Ringkasan transaksi dan pertumbuhan anggota harian</small>
      </div>
      <span class="badge badge-subtle-primary fs-3 px-3 py-2 rounded-pill shadow-xs d-flex align-items-center" style="background-color: #f8f2e6; color: #8b5e3c; border: 1px solid #e8decb;">
        <i class="ti ti-clock me-1.5 fs-4 text-warning"></i> <?= $dateNow->toLocalizedString('dd MMMM Y'); ?>
      </span>
    </div>

    <div class="row text-center g-3">
      <div class="col-6 col-md-3">
        <div class="stat-box p-3 rounded-4 bg-light-subtle border-0 shadow-xs h-100 transition-all">
          <small class="text-muted d-block fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Anggota Baru</small>
          <h2 class="fw-extrabold text-primary mb-0 display-6"><?= count($newMembersToday) ?></h2>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box p-3 rounded-4 bg-light-subtle border-0 shadow-xs h-100 transition-all">
          <small class="text-muted d-block fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Peminjaman Hari Ini</small>
          <h2 class="fw-extrabold text-warning mb-0 display-6"><?= count($newLoansToday) ?></h2>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box p-3 rounded-4 bg-light-subtle border-0 shadow-xs h-100 transition-all">
          <small class="text-muted d-block fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pengembalian Hari Ini</small>
          <h2 class="fw-extrabold text-success mb-0 display-6"><?= count($newBookReturnsToday) ?></h2>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box p-3 rounded-4 bg-light-subtle border-0 shadow-xs h-100 transition-all">
          <small class="text-muted d-block fw-bold text-uppercase mb-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Jatuh Tempo Hari Ini</small>
          <h2 class="fw-extrabold text-danger mb-0 display-6"><?= count($returnDueToday) ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- OVERVIEW CHART -->
  <div class="col-lg-8 d-flex align-items-stretch">
    <div class="card info-card border-0 w-100">
      <div class="card-body p-4">
        <div class="d-sm-flex d-block align-items-center justify-content-between mb-3">
          <div>
            <h5 class="fw-bold text-dark mb-0"><i class="ti ti-chart-bar text-primary me-2"></i> Grafik Ikhtisar 1 Bulan Terakhir (30 Hari)</h5>
            <small class="text-muted">Tren statistik aktivitas transaksi bulanan perpustakaan</small>
          </div>
        </div>
        <div id="chart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="row g-4">
      <!-- PENDAPATAN DENDA -->
      <div class="col-12">
        <div class="card info-card border-0 overflow-hidden">
          <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="ti ti-cash text-primary me-2"></i> Pendapatan Denda</h5>
            <div class="row align-items-start">
              <div class="col-9">
                <h3 class="fw-bold text-primary mb-2">Rp <?= number_format($fineIncomeThisMonth['value'] ?? 0, 0, ',', '.'); ?></h3>
                <div class="d-flex align-items-center">
                  <span class="fs-2 text-muted fw-semibold"><i class="ti ti-calendar me-1"></i><?= $dateNow->toLocalizedString('MMMM Y'); ?></span>
                </div>
              </div>
              <div class="col-3 text-end">
                <div class="member-avatar ms-auto" style="width: 44px; height: 44px; font-size: 1.1rem; background: linear-gradient(135deg, #8b5e3c 0%, #c59b27 100%);">
                  <i class="ti ti-currency-dollar"></i>
                </div>
              </div>
            </div>

            <?php
            $thisMonth = $fineIncomeThisMonth['value'];
            $lastMonth = $fineIncomeLastMonth['value'];
            $percentage = (($thisMonth - $lastMonth == 0 || $lastMonth == 0) ? 0 : round(($thisMonth - $lastMonth) / $lastMonth * 100));
            ?>
            <div class="d-flex align-items-center mt-3 pt-3 border-top">
              <span class="me-2 rounded-circle bg-primary-subtle p-1 d-flex align-items-center justify-content-center">
                <i class="ti <?= $percentage >= 0 ? 'ti-arrow-up-left text-primary' : 'ti-arrow-down-left text-primary'; ?>"></i>
              </span>
              <p class="text-dark fw-bold me-1 fs-3 mb-0"><?= ($percentage >= 0 ? '+' : '') . $percentage; ?>%</p>
              <p class="fs-2 mb-0 text-muted">dibandingkan bulan sebelumnya</p>
            </div>
          </div>
          <div id="fine"></div>
        </div>
      </div>

      <!-- TOTAL TUNGGAKAN -->
      <div class="col-12">
        <div class="card info-card border-0 overflow-hidden">
          <div class="card-body p-4">
            <div class="row align-items-start">
              <div class="col-9">
                <h5 class="fw-bold text-dark mb-3"><i class="ti ti-alert-triangle text-primary me-2"></i> Total Tunggakan Denda</h5>
                <h3 class="fw-bold text-primary mb-2">Rp <?= number_format($totalArrears ?? 0, 0, ',', '.'); ?></h3>
                <div class="d-flex align-items-center">
                  <span class="fs-2 text-muted fw-semibold"><i class="ti ti-history me-1"></i>Akumulasi Denda Belum Lunas</span>
                </div>
              </div>
              <div class="col-3 text-end">
                <div class="member-avatar ms-auto" style="width: 44px; height: 44px; font-size: 1.1rem; background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 100%);">
                  <i class="ti ti-currency-dollar"></i>
                </div>
              </div>
            </div>
          </div>
          <div id="arrears"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url("assets/libs/apexcharts/apexcharts.min.js") ?>"></script>
<script>
  $(function() {
    // =====================================
    // Overview Chart
    // =====================================
    const newMembersData = [
      <?php foreach ($newMembersOverview as $value) : ?>
        <?= "'{$value}', "; ?>
      <?php endforeach; ?>
    ].map((value => parseInt(value) || 0));

    const loansData = [
      <?php foreach ($loansOverview as $value) : ?>
        <?= "'{$value}', "; ?>
      <?php endforeach; ?>
    ].map((value => parseInt(value) || 0));

    const returnsData = [
      <?php foreach ($returnsOverview as $value) : ?>
        <?= "'{$value}', "; ?>
      <?php endforeach; ?>
    ].map((value => parseInt(value) || 0));

    const highestValue = Math.max(
      0,
      Math.max(...newMembersData),
      Math.max(...loansData),
      Math.max(...returnsData)
    );

    const roundedHighestValue = (Math.ceil(highestValue / 10) * 10);
    const chartMaxY = roundedHighestValue <= 30 ? (roundedHighestValue === 0 ? 10 : roundedHighestValue + 5) : roundedHighestValue + 10;

    var chart = {
      series: [{
          name: "Anggota baru",
          type: 'bar',
          data: newMembersData
        },
        {
          name: "Transaksi peminjaman",
          type: 'bar',
          data: loansData
        },
        {
          name: "Transaksi pengembalian",
          type: 'bar',
          data: returnsData
        },
      ],
      chart: {
        type: "bar",
        height: 380,
        toolbar: {
          show: false
        },
        foreColor: "#786c60",
        fontFamily: 'inherit',
        sparkline: {
          enabled: false
        },
      },
      plotOptions: {
        bar: {
          columnWidth: '55%',
          borderRadius: 4,
          dataLabels: {
            position: 'top',
          }
        }
      },
      colors: ["#c59b27", "#8b5e3c", "#6e4727"],
      markers: {
        size: 0
      },
      dataLabels: {
        enabled: false,
      },
      legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'right',
      },
      grid: {
        borderColor: "#e8decb",
        strokeDashArray: 4,
        xaxis: {
          lines: {
            show: false,
          },
        },
      },
      xaxis: {
        type: "category",
        categories: [
          <?php foreach ($lastMonthDateStringRange as $value) : ?>
            <?= "'{$value}', "; ?>
          <?php endforeach; ?>
        ],
        labels: {
          style: {
            colors: "#5c4838"
          },
        },
      },
      yaxis: {
        show: true,
        min: 0,
        max: chartMaxY,
        tickAmount: 5,
        labels: {
          style: {
            colors: "#5c4838",
          },
        },
      },
      tooltip: {
        theme: "light"
      },
      responsive: [{
        breakpoint: 600,
        options: {
          plotOptions: {
            bar: {
              columnWidth: '90%',
            }
          },
        }
      }]
    };
    new ApexCharts(document.querySelector("#chart"), chart).render();

    // =====================================
    // FINES CHART
    // =====================================
    var fines = {
      chart: {
        type: "area",
        height: 60,
        sparkline: {
          enabled: true,
        },
        group: "sparklines",
        fontFamily: "'Plus Jakarta Sans', sans-serif",
        foreColor: "#8b5e3c",
      },
      series: [{
        name: "Denda terkumpul",
        color: "#8b5e3c",
        data: [<?= $fineIncomeLastMonth['value'] ?? 0; ?>, <?= $fineIncomeThisMonth['value'] ?? 0; ?>],
      }],
      xaxis: {
        type: "category",
        categories: ['<?= $fineIncomeLastMonth['month'] ?? ''; ?>', '<?= $fineIncomeThisMonth['month'] ?? ''; ?>'],
        labels: {
          style: {
            cssClass: "fill-color"
          },
        },
      },
      stroke: {
        curve: "smooth",
        width: 2,
      },
      fill: {
        colors: ["#f8f2e6"],
        type: "solid",
        opacity: 0.2,
      },
      markers: {
        size: 0,
      },
      tooltip: {
        theme: "light",
        fixed: {
          enabled: true,
          position: "right",
        },
        x: {
          show: true,
        },
      },
    };
    new ApexCharts(document.querySelector("#fine"), fines).render();

    // =====================================
    // ARREARS CHART
    // =====================================
    var arrears = {
      chart: {
        type: "area",
        height: 60,
        sparkline: {
          enabled: true,
        },
        group: "sparklines",
        fontFamily: "'Plus Jakarta Sans', sans-serif",
        foreColor: "#6e4727",
      },
      series: [{
        name: "Total tunggakan (akumulasi)",
        color: "#6e4727",
        data: [
          <?php foreach ($arrears as $arrear) : ?>
            <?= "'{$arrear['arrear']}', "; ?>
          <?php endforeach; ?>
        ],
      }],
      xaxis: {
        type: "category",
        categories: [
          <?php foreach ($arrears as $arrear) : ?>
            <?= "'{$arrear['date']}', "; ?>
          <?php endforeach; ?>
        ],
        labels: {
          style: {
            cssClass: "fill-color"
          },
        },
      },
      stroke: {
        curve: "smooth",
        width: 2,
      },
      fill: {
        colors: ["#f6efe2"],
        type: "solid",
        opacity: 0.2,
      },
      markers: {
        size: 0,
      },
      tooltip: {
        theme: "light",
        fixed: {
          enabled: true,
          position: "right",
        },
        x: {
          show: true,
        },
      },
    };
    new ApexCharts(document.querySelector("#arrears"), arrears).render();
  });
</script>
<?= $this->endSection() ?>