<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak ID Card KTP - <?= esc("{$member['first_name']} {$member['last_name']}"); ?></title>

  <!-- Google Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      background-color: #f4efe6;
      color: #2d241e;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      padding: 24px;
    }

    /* Action Control Bar */
    .control-bar {
      background: #ffffff;
      border: 1px solid #ebd9bc;
      border-radius: 16px;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
      box-shadow: 0 4px 14px rgba(110, 71, 39, 0.08);
      flex-wrap: wrap;
    }

    .btn-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 18px;
      border-radius: 50px;
      font-weight: 700;
      font-size: 0.875rem;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .btn-primary-action {
      background: linear-gradient(135deg, #6e4727, #8b5e3c);
      color: #ffffff;
      box-shadow: 0 4px 10px rgba(110, 71, 39, 0.2);
    }
    .btn-primary-action:hover {
      background: linear-gradient(135deg, #59391f, #6e4727);
      transform: translateY(-1px);
    }
    .btn-light-action {
      background: #ffffff;
      color: #6e4727;
      border: 1.5px solid #ebd9bc;
    }
    .btn-light-action:hover {
      background: #fdfbf7;
      border-color: #6e4727;
    }

    /* ID Card KTP Standard Container */
    .id-card-wrapper {
      width: 600px;
      height: 380px;
      background: linear-gradient(135deg, #ffffff 0%, #fcf8f2 100%);
      border: 2.5px solid #6e4727;
      border-radius: 18px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 16px 36px rgba(110, 71, 39, 0.2);
      padding: 16px 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    /* Background Watermark Pattern */
    .id-card-wrapper::before {
      content: '';
      position: absolute;
      top: -40px;
      right: -40px;
      width: 260px;
      height: 260px;
      background: radial-gradient(circle, rgba(197, 155, 39, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
      pointer-events: none;
    }

    /* Header Kop KTP */
    .card-header-kop {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2.5px double #6e4727;
      padding-bottom: 10px;
    }
    .header-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .header-logo {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      object-fit: cover;
      border: 1.5px solid #e8decb;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .header-title-box h3 {
      font-size: 0.95rem;
      font-weight: 800;
      color: #6e4727;
      letter-spacing: -0.2px;
      line-height: 1.2;
    }
    .header-title-box span {
      font-size: 0.72rem;
      font-weight: 700;
      color: #8b5e3c;
      display: block;
    }
    .header-badge-type {
      background: linear-gradient(135deg, #6e4727, #8b5e3c);
      color: #ffffff;
      font-size: 0.68rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 20px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* NIK / UID Section */
    .nik-section {
      background: #f8f2e6;
      border: 1px solid #ebd9bc;
      border-radius: 8px;
      padding: 4px 12px;
      margin: 8px 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .nik-label {
      font-size: 0.68rem;
      font-weight: 800;
      color: #8b5e3c;
      letter-spacing: 0.5px;
    }
    .nik-value {
      font-family: 'JetBrains Mono', monospace;
      font-size: 1.05rem;
      font-weight: 800;
      color: #2d241e;
      letter-spacing: 1px;
    }

    /* Body Layout (Foto Left, Details Right) */
    .card-body-ktp {
      display: flex;
      gap: 16px;
      flex: 1;
      align-items: center;
    }

    /* Pasfoto Box */
    .pasfoto-container {
      width: 105px;
      height: 140px;
      background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
      border: 2px solid #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      flex-shrink: 0;
    }
    .pasfoto-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .avatar-placeholder {
      font-size: 2.5rem;
      font-weight: 900;
      color: #ffffff;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* Details Grid */
    .ktp-details-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.78rem;
    }
    .ktp-details-table td {
      padding: 2.5px 0;
      vertical-align: top;
    }
    .ktp-label {
      font-weight: 700;
      color: #6e4727;
      width: 105px;
    }
    .ktp-colon {
      width: 10px;
      font-weight: 700;
      color: #6e4727;
    }
    .ktp-val {
      font-weight: 700;
      color: #2d241e;
    }

    /* Footer Verification */
    .card-footer-ktp {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1.5px dashed #ebd9bc;
      padding-top: 6px;
      margin-top: 4px;
    }
    .barcode-box {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .barcode-svg-container {
      height: 32px;
      overflow: hidden;
    }
    .signature-stamp-box {
      text-align: right;
      font-size: 0.62rem;
      color: #6e4727;
      font-weight: 700;
    }

    /* Print Media Rules */
    @media print {
      body {
        background: none;
        padding: 0;
      }
      .control-bar {
        display: none !important;
      }
      .id-card-wrapper {
        box-shadow: none;
        border: 2px solid #000;
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body>

  <!-- Top Action Control Bar -->
  <div class="control-bar">
    <a href="<?= base_url("admin/members/{$member['uid']}"); ?>" class="btn-action btn-light-action">
      <i class="ti ti-arrow-left"></i> Kembali ke Detail
    </a>
    <button onclick="window.print()" class="btn-action btn-primary-action">
      <i class="ti ti-printer"></i> Cetak ID Card (Print)
    </button>
    <button onclick="downloadIdCardPNG()" class="btn-action btn-light-action">
      <i class="ti ti-photo-down"></i> Unduh Gambar (PNG HD)
    </button>
  </div>

  <!-- Physical KTP ID Card Container (CR80 Dimension: 85.6mm x 54mm) -->
  <div class="id-card-wrapper" id="ktpCardElement">
    
    <!-- Kop Header -->
    <div class="card-header-kop">
      <div class="header-left">
        <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan" class="header-logo">
        <div class="header-title-box">
          <h3>KARTU IDENTITAS ANGGOTA PERPUSTAKAAN</h3>
          <span>PONDOK PESANTREN ASSALAFIYYAH MLANGI</span>
        </div>
      </div>
      <div>
        <span class="header-badge-type">
          <?= esc(($member['member_type'] ?? 'siswa') === 'siswa' ? 'ANGGOTA' : 'PUSTAKAWAN'); ?>
        </span>
      </div>
    </div>

    <!-- NIK / UID Box -->
    <div class="nik-section">
      <span class="nik-label">UID ANGGOTA:</span>
      <span class="nik-value"><?= esc($member['uid']); ?></span>
    </div>

    <!-- Main Body: Pasfoto Left & Data Details Right -->
    <div class="card-body-ktp">
      <!-- Pasfoto Box -->
      <div class="pasfoto-container">
        <?php if (!empty($member['avatar'])) : ?>
          <img src="<?= base_url('uploads/avatars/' . $member['avatar']); ?>" alt="Foto <?= esc($member['first_name']); ?>" class="pasfoto-img">
        <?php else : ?>
          <div class="avatar-placeholder">
            <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1)); ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- Data Details Table (Style KTP) -->
      <table class="ktp-details-table">
        <tr>
          <td class="ktp-label">Nama Lengkap</td>
          <td class="ktp-colon">:</td>
          <td class="ktp-val"><?= esc("{$member['first_name']} {$member['last_name']}"); ?></td>
        </tr>
        <tr>
          <td class="ktp-label">Jabatan / Status</td>
          <td class="ktp-colon">:</td>
          <td class="ktp-val">
            <?= esc(($member['member_type'] ?? 'siswa') === 'siswa' ? 'Siswa / Santri' : 'Pustakawan / Staf'); ?>
            (<?= esc($tierDetails['name'] ?? 'Member'); ?>)
          </td>
        </tr>
        <tr>
          <td class="ktp-label">Instansi / Kelas</td>
          <td class="ktp-colon">:</td>
          <td class="ktp-val"><?= esc(($member['institution'] ?? 'Assalafiyyah') . ' ' . ($member['class_level'] ?? '')); ?></td>
        </tr>
        <tr>
          <td class="ktp-label">No. Telepon</td>
          <td class="ktp-colon">:</td>
          <td class="ktp-val"><?= esc(!empty($member['phone']) && $member['phone'] !== '-' ? $member['phone'] : '-'); ?></td>
        </tr>
        <tr>
          <td class="ktp-label">Alamat Domisili</td>
          <td class="ktp-colon">:</td>
          <td class="ktp-val"><?= esc(!empty($member['address']) ? $member['address'] : 'Sleman, Yogyakarta'); ?></td>
        </tr>
        <tr>
          <td class="ktp-label">Masa Berlaku</td>
          <td class="ktp-colon">:</td>
          <td class="ktp-val" style="color: #b45309; font-weight: 800;">SELAMA MENJADI ANGGOTA</td>
        </tr>
      </table>
    </div>

    <!-- Card Footer Verification -->
    <div class="card-footer-ktp">
      <div class="barcode-box">
        <div class="barcode-svg-container">
          <?= generateBarcodeSVG($member['uid'], 32); ?>
        </div>
      </div>
      <div class="signature-stamp-box">
        <div>Assalafiyyah</div>
        <div style="font-weight: 800; color: #2d241e; text-transform: uppercase;">Pustakawan Aktif</div>
      </div>
    </div>

  </div>

  <script>
    <?php if ($autoPrint) : ?>
      window.addEventListener('load', () => {
        setTimeout(() => {
          window.print();
        }, 500);
      });
    <?php endif; ?>

    function downloadIdCardPNG() {
      const element = document.getElementById('ktpCardElement');
      if (!element) return;

      html2canvas(element, {
        scale: 3,
        useCORS: true,
        backgroundColor: '#ffffff',
        logging: false
      }).then(canvas => {
        const link = document.createElement('a');
        const memberName = '<?= url_title($member['first_name'] . '-' . $member['last_name'], '-', true); ?>';
        link.download = `ID_Card_KTP_${memberName}_<?= esc($member['uid']); ?>.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
      });
    }
  </script>
</body>
</html>
