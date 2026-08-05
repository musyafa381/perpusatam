<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nota Pertanggungjawaban Buku - <?= esc($loan['uid']); ?></title>
  <link rel="stylesheet" href="<?= base_url('assets/css/styles.min.css'); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/libs/@tabler/icons-webfont/tabler-icons.min.css'); ?>">
  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      color: #1e293b;
      margin: 0;
      padding: 0;
    }
    
    .no-print-bar {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      gap: 10px;
    }

    .receipt-container {
      max-width: 540px;
      margin: 30px auto;
      background: #ffffff;
      padding: 30px 30px;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      border: 1px solid #cbd5e1;
      position: relative;
    }

    .dashed-divider {
      border-top: 1.5px dashed #cbd5e1;
      margin: 16px 0;
    }

    .solid-divider {
      border-top: 2px solid #1e293b;
      margin: 14px 0;
    }

    .kop-logo {
      width: 52px;
      height: 52px;
      object-fit: contain;
    }
    
    .receipt-title {
      font-weight: 800;
      color: #991b1b;
      font-size: 1.05rem;
      letter-spacing: 0.3px;
    }

    .identity-grid td {
      padding: 4px 2px;
      font-size: 0.85rem;
      vertical-align: top;
    }

    .table-receipt {
      width: 100%;
      table-layout: fixed;
      border-collapse: collapse;
    }

    .table-receipt th {
      font-size: 0.78rem;
      text-transform: uppercase;
      color: #64748b;
      border-bottom: 1.5px solid #cbd5e1;
      padding-bottom: 6px;
      letter-spacing: 0.5px;
    }

    .table-receipt td {
      font-size: 0.84rem;
      padding: 8px 0;
      vertical-align: top;
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    .statement-box {
      background-color: #fffbf0;
      border: 1px dashed #fde68a;
      border-radius: 8px;
      padding: 14px 16px;
      margin: 18px 0;
      font-size: 0.84rem;
      line-height: 1.5;
      color: #78350f;
    }

    .signature-space {
      height: 50px;
    }

    @media print {
      body {
        background-color: #ffffff !important;
      }
      .receipt-container {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
      }
      .no-print-bar {
        display: none !important;
      }
      @page {
        size: auto;
        margin: 1cm;
      }
    }
  </style>
</head>
<body>

  <?php
  if (!function_exists('terbilangRupiah')) {
      function terbilangRupiah($number) {
          $number = abs((float)$number);
          $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
          if ($number < 12) {
              return ' ' . $baca[(int)$number];
          } elseif ($number < 20) {
              return terbilangRupiah($number - 10) . ' Belas';
          } elseif ($number < 100) {
              return terbilangRupiah($number / 10) . ' Puluh' . terbilangRupiah($number % 10);
          } elseif ($number < 200) {
              return ' Seratus' . terbilangRupiah($number - 100);
          } elseif ($number < 1000) {
              return terbilangRupiah($number / 100) . ' Ratus' . terbilangRupiah($number % 100);
          } elseif ($number < 2000) {
              return ' Seribu' . terbilangRupiah($number - 1000);
          } elseif ($number < 1000000) {
              return terbilangRupiah($number / 1000) . ' Ribu' . terbilangRupiah($number % 1000);
          } elseif ($number < 1000000000) {
              return terbilangRupiah($number / 1000000) . ' Juta' . terbilangRupiah($number % 1000000);
          }
          return (string)$number;
      }
  }
  ?>

  <!-- Floating Controls (Hidden on Print) -->
  <div class="no-print-bar">
    <a href="<?= base_url('admin/returns/' . $loan['uid']); ?>" class="btn btn-outline-secondary btn-sm shadow-sm fw-bold bg-white rounded-pill px-3">
      <i class="ti ti-arrow-left me-1"></i> Detail
    </a>
    <?php
    $waPhone = preg_replace('/[^0-9]/', '', $loan['phone'] ?? '');
    if (str_starts_with($waPhone, '0')) {
        $waPhone = '62' . substr($waPhone, 1);
    }
    $waMsg = urlencode("Assalamu'alaikum wr. wb.\n\nYth. Bpk/Ibu/Sdr {$loan['first_name']} {$loan['last_name']},\nBerikut Nota Surat Pertanggungjawaban Kerusakan/Kehilangan Buku dari Perpustakaan Assalafiyyah (No: {$letterNumber}).\nTerima kasih.");
    ?>
    <?php if (!empty($waPhone)) : ?>
      <a href="https://wa.me/<?= $waPhone; ?>?text=<?= $waMsg; ?>" target="_blank" class="btn btn-success btn-sm shadow-sm fw-bold rounded-pill px-3">
        <i class="ti ti-brand-whatsapp me-1"></i> Kirim WA
      </a>
    <?php endif; ?>
    <button onclick="window.print()" class="btn btn-primary btn-sm shadow-sm fw-bold rounded-pill px-3">
      <i class="ti ti-printer me-1"></i> Cetak Struk / PDF
    </button>
  </div>

  <div class="receipt-container">
    
    <!-- Kop Struk Resmi Header -->
    <div class="text-center">
      <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan" class="shadow-sm mb-2" style="height: 54px; width: 54px; border-radius: 12px; object-fit: cover; border: 1px solid #e8decb;" onerror="this.src='<?= base_url('assets/images/logos/favicon.png'); ?>'">
      <h5 class="fw-extrabold text-dark mb-0 fs-4" style="color: #432818 !important;">PERPUSTAKAAN PUSAT ASSALAFIYYAH</h5>
      <div class="text-muted fs-1 mb-1">Jl. Assalafiyyah No. 01, Kompleks Pesantren Assalafiyyah</div>
      <span class="badge bg-danger text-white fs-1 px-3 py-1 rounded-pill">SURAT RESMI PERTANGGUNGJAWABAN</span>
    </div>

    <div class="dashed-divider"></div>

    <!-- Title & Number -->
    <div class="text-center mb-3">
      <div class="receipt-title text-uppercase">SURAT PERNYATAAN PERTANGGUNGJAWABAN</div>
      <div class="text-muted fs-1">No: <strong class="text-dark"><?= esc($letterNumber); ?></strong> | Tgl: <strong><?= date('d/m/Y', strtotime($loan['return_date'] ?? 'now')); ?></strong></div>
    </div>

    <!-- Section I: Identitas Simpel & Bagus -->
    <table class="table table-sm table-borderless identity-grid mb-0">
      <tbody>
        <tr>
          <td style="width: 130px;" class="fw-semibold text-muted">Nama Peminjam</td>
          <td style="width: 10px;" class="text-center">:</td>
          <td class="fw-bold text-dark"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?> <small class="text-primary">(<?= esc($loan['member_uid']); ?>)</small></td>
        </tr>
        <tr>
          <td class="fw-semibold text-muted">Kategori Anggota</td>
          <td class="text-center">:</td>
          <td><span class="badge bg-light text-dark border px-2 py-0 fs-1"><?= esc($loan['tier_name'] ?? 'Reguler'); ?></span></td>
        </tr>
        <tr>
          <td class="fw-semibold text-muted">No. Telepon / WA</td>
          <td class="text-center">:</td>
          <td><?= esc($loan['phone'] ?: '-'); ?></td>
        </tr>
        <tr>
          <td class="fw-semibold text-muted">Alamat</td>
          <td class="text-center">:</td>
          <td><?= esc($loan['address'] ?: '-'); ?></td>
        </tr>
      </tbody>
    </table>

    <div class="dashed-divider"></div>

    <!-- Section II: Tabel Rincian Struk (Fixed Width, No Scrollbar) -->
    <table class="table-receipt">
      <thead>
        <tr>
          <th style="width: 55%;">BUKU & EKSEMPLAR</th>
          <th style="width: 23%;" class="text-center">KONDISI</th>
          <th style="width: 22%;" class="text-end">DENDA</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = 1; 
        $totalResponsibilityFine = 0;
        foreach ($transactionLoans as $item) : 
          $cond = strtolower($item['return_condition'] ?? 'baik');
          $price = floatval($item['item_price'] ?? 50000);
          if ($price <= 0) $price = 50000;

          $pctLabel = ($cond === 'rusak') ? '50%' : '100%';
          $fineVal = ($cond === 'rusak') ? (0.5 * $price) : (1.0 * $price);
          $totalResponsibilityFine += $fineVal;
        ?>
          <tr>
            <td>
              <div class="fw-bold text-dark"><?= esc($item['title']); ?></div>
              <div class="text-muted fs-1"><i class="ti ti-barcode me-1"></i>Kode: <?= esc($item['item_code'] ?: '-'); ?></div>
              <div class="text-muted fs-1">Harga: Rp <?= number_format($price, 0, ',', '.'); ?></div>
            </td>
            <td class="text-center">
              <?php if ($cond === 'rusak') : ?>
                <span class="badge bg-warning text-dark fw-bold px-2 py-1 fs-1 rounded-pill"><i class="ti ti-alert-triangle me-1"></i>Rusak (50%)</span>
              <?php else : ?>
                <span class="badge bg-danger text-white fw-bold px-2 py-1 fs-1 rounded-pill"><i class="ti ti-x me-1"></i>Hilang (100%)</span>
              <?php endif; ?>
            </td>
            <td class="text-end fw-bold text-danger fs-2">
              Rp <?= number_format($fineVal, 0, ',', '.'); ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="solid-divider"></div>

    <!-- Ringkasan Total Denda -->
    <div class="d-flex justify-content-between align-items-center mb-1">
      <span class="fw-bold text-dark fs-3">TOTAL DENDA KOMPENSASI:</span>
      <span class="fw-extrabold text-danger fs-5">Rp <?= number_format($totalResponsibilityFine, 0, ',', '.'); ?></span>
    </div>
    <div class="text-end text-muted fs-1 fst-italic mb-3">
      (<?= trim(terbilangRupiah($totalResponsibilityFine)); ?> Rupiah)
    </div>

    <!-- Teks Pernyataan Ringkas Berkesan Struk -->
    <div class="statement-box">
      <div class="fw-bold mb-1"><i class="ti ti-file-certificate me-1"></i> PERNYATAAN KESEDIAAN:</div>
      "Dengan ini saya menyatakan bertanggung jawab penuh atas kondisi buku di atas dan bersedia menyelesaikan penggantian denda kompensasi sebesar <strong>Rp <?= number_format($totalResponsibilityFine, 0, ',', '.'); ?></strong> sesuai aturan Perpustakaan Islam Assalafiyyah."
    </div>

    <!-- Area Tanda Tangan Struk Simpel -->
    <div class="signature-section pt-2">
      <div class="row text-center fs-2">
        <div class="col-6">
          <div class="text-muted mb-1">Peminjam / Penanggung Jawab,</div>
          <div class="signature-space"></div>
          <div class="fw-bold text-dark text-decoration-underline"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></div>
        </div>
        <div class="col-6">
          <div class="text-muted mb-1">Petugas Perpustakaan,</div>
          <div class="signature-space"></div>
          <div class="fw-bold text-dark text-decoration-underline"><?= esc(auth()->user()->first_name ?? auth()->user()->username ?? 'Petugas'); ?></div>
        </div>
      </div>
    </div>

  </div>

  <?php if (service('request')->getGet('print') === 'true') : ?>
    <script>
      window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
          window.print();
        }, 500);
      });
    </script>
  <?php endif; ?>

</body>
</html>
