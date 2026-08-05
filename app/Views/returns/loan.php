<?php
use CodeIgniter\I18n\Time;

$now = Time::now(locale: 'id');

if (empty($loans)) : ?>
  <div class="alert alert-warning d-flex align-items-center my-3 rounded-3 border-0 shadow-sm" role="alert">
    <i class="ti ti-alert-circle fs-6 me-2 text-warning"></i>
    <div>
      <b>Peminjaman tidak ditemukan</b>
      <?php if (!empty($msg)) : ?>
        <p class="mb-0 fs-2"><?= esc($msg); ?></p>
      <?php endif; ?>
    </div>
  </div>
<?php else : ?>
  <h5 class="fw-bold text-dark my-3"><i class="ti ti-search text-primary me-2"></i> Hasil Pencarian Data Peminjaman (<?= count($loans); ?> Transaksi Ditampilkan)</h5>
  <div class="table-responsive rounded-3 border">
    <table class="table table-hover align-middle table-custom mb-0">
      <thead>
        <tr>
          <th scope="col" class="ps-3">#</th>
          <th scope="col">Nama Peminjam</th>
          <th scope="col" class="text-center">Total Buku</th>
          <th scope="col">Tgl Pinjam</th>
          <th scope="col">Tenggat</th>
          <th scope="col" class="text-center">Status</th>
          <th scope="col" class="text-center pe-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1 ?>
        <?php foreach ($loans as $loan) :
          $loanCreateDate = Time::parse($loan['loan_date'], locale: 'id');
          $loanDueDate = Time::parse($loan['due_date'], locale: 'id');

          $isLate = $now->isAfter($loanDueDate);
          $isDueDate = $now->today()->equals($loanDueDate);
        ?>
          <?php if (!$loan['deleted_at']) : ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="fw-bold text-dark fs-3"><?= esc("{$loan['first_name']} {$loan['last_name']}"); ?></div>
              </td>



              <td class="text-center">
                <span class="badge badge-subtle-primary fs-2 px-3 py-2">
                  <i class="ti ti-books me-1"></i><?= esc($loan['total_books'] ?? 1); ?> Buku
                </span>
              </td>
              <td>
                <div class="fw-semibold text-dark"><?= $loanCreateDate->toLocalizedString('dd/MM/y'); ?></div>
                <small class="text-muted"><i class="ti ti-clock me-1"></i><?= $loanCreateDate->toLocalizedString('HH:mm:ss'); ?></small>
              </td>
              <td>
                <div class="fw-bold text-dark"><?= $loanDueDate->toLocalizedString('dd/MM/y'); ?></div>
              </td>
              <td class="text-center">
                <?php if ($now->isBefore($loanDueDate) && !$isDueDate) : ?>
                  <span class="badge badge-subtle-primary fs-2 px-3 py-2"><i class="ti ti-circle-check me-1"></i> Normal</span>
                <?php elseif ($isDueDate) : ?>
                  <span class="badge badge-subtle-warning fs-2 px-3 py-2"><i class="ti ti-clock-alert me-1"></i> Jatuh Tempo</span>
                <?php else : ?>
                  <span class="badge badge-subtle-warning fs-2 px-3 py-2"><i class="ti ti-alert-triangle me-1"></i> Terlambat</span>
                <?php endif; ?>
              </td>
              <td class="text-center pe-3">
                <a href="<?= base_url("admin/returns/new?loan-uid={$loan['uid']}"); ?>" class="btn btn-pill-gold btn-sm px-3 fw-bold shadow-sm">
                  <i class="ti ti-check me-1"></i> Pilih Peminjaman
                </a>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>