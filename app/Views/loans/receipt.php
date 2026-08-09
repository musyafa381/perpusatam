<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Struk Peminjaman Buku - <?= esc($loan['uid']); ?></title>
<style>
  /* Standard 58mm Thermal Printer Page Settings */
  @page {
    size: 58mm auto;
    margin: 0mm !important;
  }

  /* Desktop Preview Container */
  .thermal-preview-container {
    display: flex;
    justify-content: center;
    padding: 20px 0;
  }

  .thermal-receipt {
    width: 300px; /* Scaled preview for desktop screen (~52mm) */
    background: #ffffff;
    padding: 14px 10px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    font-family: Arial, Helvetica, sans-serif;
    color: #000000;
    font-size: 11px;
    line-height: 1.3;
    box-sizing: border-box;
    overflow: hidden;
    word-break: break-word;
    overflow-wrap: break-word;
  }

  .thermal-header {
    text-align: center;
    margin-bottom: 6px;
  }

  .thermal-logo {
    height: 38px;
    width: 38px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 3px;
    border: 1px solid #ccc;
  }

  .thermal-title {
    font-size: 13px;
    font-weight: 800;
    text-transform: uppercase;
    margin: 2px 0;
    color: #000;
    line-height: 1.2;
    word-break: break-word;
  }

  .thermal-sub {
    font-size: 9.5px;
    text-transform: uppercase;
    color: #111;
    display: block;
    margin-top: 1px;
    line-height: 1.2;
    word-break: break-word;
  }

  .thermal-badge {
    display: inline-block;
    border: 1px solid #000;
    font-weight: bold;
    font-size: 9px;
    padding: 2px 6px;
    text-transform: uppercase;
    margin-top: 4px;
    border-radius: 2px;
  }

  .thermal-divider {
    border-top: 1px dashed #000;
    margin: 6px 0;
    width: 100%;
  }

  .thermal-info-table {
    width: 100%;
    border-collapse: collapse;
    margin: 2px 0;
    font-size: 10.5px;
    table-layout: fixed;
  }

  .thermal-info-table td {
    padding: 2px 0;
    vertical-align: top;
    color: #000;
  }

  .thermal-books-table {
    width: 100%;
    border-collapse: collapse;
    margin: 2px 0;
    font-size: 10px;
    table-layout: fixed;
  }

  .thermal-books-table th {
    border-bottom: 1px dashed #000;
    text-align: left;
    padding: 3px 0;
    font-size: 9px;
    text-transform: uppercase;
    color: #000;
  }

  .thermal-books-table td {
    padding: 3px 0;
    vertical-align: top;
    word-break: break-word;
    color: #000;
  }

  .item-code-tag {
    font-family: monospace;
    font-weight: bold;
    font-size: 9px;
    display: block;
    margin-top: 1px;
    color: #000;
    word-break: break-all;
  }

  .thermal-footer {
    text-align: center;
    font-size: 9px;
    margin-top: 6px;
    line-height: 1.25;
    word-break: break-word;
  }

  /* CETAK PRINT STRUK THERMAL 58MM (CLEAR, LARGE, SCANNER-FRIENDLY BARCODE) */
  @media print {
    @page {
      size: 58mm auto;
      margin: 0mm !important;
    }

    html, body, #main-wrapper, .body-wrapper, .container-fluid, #spa-content-container {
      background: #ffffff !important;
      margin: 0 !important;
      padding: 0 !important;
      width: 58mm !important;
      max-width: 58mm !important;
      color: #000000 !important;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
      overflow: visible !important;
    }

    body * {
      visibility: hidden !important;
    }

    .printable-receipt-area,
    .printable-receipt-area * {
      visibility: visible !important;
    }

    .printable-receipt-area {
      position: fixed !important;
      left: 0 !important;
      top: 0 !important;
      width: 58mm !important;
      max-width: 58mm !important;
      margin: 0 !important;
      padding: 0 !important;
      box-sizing: border-box !important;
      z-index: 999999 !important;
    }

    .thermal-receipt {
      width: 52mm !important; /* Full optimal printable width for 58mm paper */
      max-width: 52mm !important;
      border: none !important;
      border-radius: 0 !important;
      box-shadow: none !important;
      padding: 1.5mm 0 !important;
      margin: 0 auto !important;
      font-size: 9.5pt !important;
      line-height: 1.25 !important;
      color: #000000 !important;
      background: transparent !important;
      font-family: Arial, Helvetica, sans-serif !important;
      box-sizing: border-box !important;
      word-break: break-word !important;
      overflow-wrap: break-word !important;
    }

    .no-print {
      display: none !important;
    }

    .thermal-title {
      font-size: 11pt !important;
      font-weight: bold !important;
      line-height: 1.15 !important;
    }

    .thermal-sub {
      font-size: 8.5pt !important;
    }

    .thermal-footer {
      font-size: 8.5pt !important;
    }

    .thermal-info-table {
      font-size: 9.5pt !important;
    }

    .thermal-books-table {
      font-size: 9pt !important;
    }

    .thermal-title, .thermal-sub, .thermal-badge, strong, td, th {
      color: #000000 !important;
    }

    .thermal-divider {
      border-top: 1px dashed #000000 !important;
    }

    .thermal-logo {
      height: 34px !important;
      width: 34px !important;
      filter: grayscale(100%) contrast(200%);
    }

    /* Barcode high contrast & scanner scannable height */
    svg {
      max-width: 100% !important;
      height: 48px !important;
    }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
use CodeIgniter\I18n\Time;

$loanDate = Time::parse($loan['loan_date'], locale: 'id');
$dueDate = Time::parse($loan['due_date'], locale: 'id');
$libraryName = $settings['library_name'] ?? 'PERPUSTAKAAN ASSALAFIYYAH';
$libraryAddress = $settings['library_address'] ?? '';
$libraryContact = $settings['library_contact'] ?? '';
$footerNote = $settings['struk_footer_note'] ?? 'Simpan & bawa struk ini saat pengembalian buku.';
?>

<div class="d-flex align-items-center justify-content-between mb-4 no-print">
  <div>
    <a href="<?= base_url('admin/loans'); ?>" class="btn btn-outline-secondary rounded-pill me-2">
      <i class="ti ti-arrow-left me-1"></i> Daftar Peminjaman
    </a>
    <a href="<?= base_url('admin/loans/new/members/search'); ?>" class="btn btn-pill-gold">
      <i class="ti ti-plus me-1"></i> Tambah Peminjaman Baru
    </a>
  </div>
  <div>
    <button type="button" class="btn btn-pill-gold fw-bold shadow-sm px-4" onclick="window.print();">
      <i class="ti ti-printer me-1"></i> Cetak Struk 58mm
    </button>
  </div>
</div>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2 no-print">
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Printable Thermal Receipt Area (58mm Spec) -->
<div class="printable-receipt-area thermal-preview-container mb-5">
  <div class="thermal-receipt">
    
    <!-- Kop Header -->
    <div class="thermal-header">
      <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo" class="thermal-logo" onerror="this.src='<?= base_url('assets/images/logos/favicon.png'); ?>'">
      <div class="thermal-title"><?= esc($libraryName); ?></div>
      <?php if (!empty($libraryAddress)) : ?>
        <span class="thermal-sub"><?= esc($libraryAddress); ?></span>
      <?php endif; ?>
      <?php if (!empty($libraryContact)) : ?>
        <span class="thermal-sub">TELP/WA: <?= esc($libraryContact); ?></span>
      <?php endif; ?>
      <div class="thermal-badge">STRUK PEMINJAMAN BUKU</div>
    </div>

    <div class="thermal-divider"></div>

    <!-- Barcode & TRX UID (Scanner-Friendly Size) -->
    <div class="text-center my-2">
      <div style="height: 48px; display: flex; justify-content: center; align-items: center; overflow: hidden; background: #ffffff; padding: 2px 0;">
        <?= generateBarcodeSVG($loan['uid'], 48); ?>
      </div>
      <div style="font-weight: bold; font-size: 9.5pt; margin-top: 3px; font-family: monospace; letter-spacing: 0.5px; word-break: break-all;">
        NO: TRX-<?= esc($loan['uid']); ?>
      </div>
    </div>

    <div class="thermal-divider"></div>

    <!-- Info Peminjam & Tanggal (Fixed Table Grid - Clear & No Overflow) -->
    <table class="thermal-info-table">
      <tr>
        <td style="width: 68px;">Peminjam:</td>
        <td style="text-align: right; font-weight: bold; word-break: break-word;">
          <?= esc("{$loan['first_name']} {$loan['last_name']}"); ?>
        </td>
      </tr>
      <tr>
        <td style="width: 68px;">ID Anggota:</td>
        <td style="text-align: right; font-weight: bold; font-family: monospace; word-break: break-all;">
          <?= esc($loan['member_uid']); ?>
        </td>
      </tr>
      <tr>
        <td style="width: 68px;">Tgl Pinjam:</td>
        <td style="text-align: right; font-weight: bold; word-break: break-word;">
          <?= $loanDate->toLocalizedString('dd/MM/yy HH:mm'); ?>
        </td>
      </tr>
      <tr>
        <td style="width: 68px;">Tenggat:</td>
        <td style="text-align: right; font-weight: bold; text-decoration: underline; word-break: break-word;">
          <?= $dueDate->toLocalizedString('dd/MM/yy'); ?>
        </td>
      </tr>
    </table>

    <div class="thermal-divider"></div>

    <!-- List Buku -->
    <div style="font-weight: bold; margin-bottom: 3px; font-size: 9pt; text-transform: uppercase;">
      BUKU DIPINJAM (<?= count($allSessionLoans); ?> EKS):
    </div>
    <table class="thermal-books-table">
      <thead>
        <tr>
          <th style="width: 16px;">#</th>
          <th>JUDUL & KODE EX.</th>
        </tr>
      </thead>
      <tbody>
        <?php $idx = 1; ?>
        <?php foreach ($allSessionLoans as $sBook) : ?>
          <?php 
            $rackName = !empty($sBook['item_rack_name']) ? $sBook['item_rack_name'] : (!empty($sBook['rack_name']) ? $sBook['rack_name'] : ($sBook['rack'] ?? null));
            $rackFloor = !empty($sBook['item_rack_floor']) ? $sBook['item_rack_floor'] : ($sBook['rack_floor'] ?? ($sBook['floor'] ?? null));
          ?>
          <tr>
            <td style="vertical-align: top; width: 16px;"><?= $idx++; ?>.</td>
            <td style="vertical-align: top; word-break: break-word;">
              <strong style="display: block; font-size: 9pt; color: #000;"><?= esc($sBook['book_title'] ?? $sBook['title']); ?></strong>
              <span class="item-code-tag">
                [<?= esc($sBook['item_code'] ?: 'EKS-DEF'); ?>]
                <?php if (!empty($rackName)) : ?>
                  • Rak: <?= esc($rackName); ?><?= !empty($rackFloor) ? " (Lt.{$rackFloor})" : ''; ?>
                <?php endif; ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="thermal-divider"></div>

    <!-- Catatan Footnote Struk -->
    <div class="thermal-footer">
      <p style="margin: 0 0 3px 0; font-weight: 600; font-style: italic;">
        <?= esc($footerNote); ?>
      </p>
      <div style="font-size: 7.5pt; opacity: 0.9; margin-top: 3px;">
        Printed: <?= date('d/m/Y H:i'); ?> WIB<br>
        *** TERIMA KASIH ***
      </div>
    </div>

  </div>
</div>

<?php if ((request()->getGet('print') ?? null) === 'true' || isset($_GET['print'])) : ?>
  <script>
    window.addEventListener('DOMContentLoaded', (event) => {
      setTimeout(() => {
        window.print();
      }, 400);
    });
  </script>
<?php endif; ?>
<?= $this->endSection() ?>
