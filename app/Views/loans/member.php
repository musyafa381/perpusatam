<?php if (empty($members)) : ?>
  <div class="p-4 text-center text-danger bg-light rounded-3 border border-danger border-opacity-25">
    <i class="ti ti-user-x fs-7 d-block mb-1"></i>
    <b>Anggota tidak ditemukan</b>
    <p class="mb-0 text-muted fs-2"><?= $msg ?? 'Silakan coba cari dengan nama, email, atau UID lain.'; ?></p>
  </div>
<?php else : ?>
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h5 class="fw-bold text-dark mb-0"><i class="ti ti-users text-primary me-2"></i> Hasil Pencarian Anggota (<?= count($members); ?> Ditemukan)</h5>
    <small class="text-muted">Klik tombol Pilih pada baris anggota yang sesuai</small>
  </div>

  <div class="table-responsive rounded-4 border overflow-hidden shadow-sm">
    <table class="table table-hover align-middle table-assalafiyyah mb-0">
      <thead>
        <tr>
          <th scope="col" class="text-center" style="width: 50px;">#</th>
          <th scope="col">Nama Lengkap</th>
          <th scope="col">Email</th>
          <th scope="col">Jenis Kelamin</th>
          <th scope="col" class="text-center pe-4">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php foreach ($members as $member) : ?>
          <?php if (!$member['deleted_at']) : ?>
            <tr>
              <th scope="row" class="col-index"><?= $i++; ?></th>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <div class="table-avatar-initial">
                    <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1) . substr($member['last_name'] ?? '', 0, 1)); ?>
                  </div>
                  <div>
                    <div class="fw-bold text-dark fs-3 mb-0"><?= esc($member['first_name'] . ' ' . $member['last_name']); ?></div>
                    <div class="d-flex align-items-center gap-1 mt-1">
                      <small class="text-muted"><i class="ti ti-barcode me-1"></i>UID: <?= esc($member['uid']); ?></small>
                      <?php if (!empty($member['unreturned_count']) && $member['unreturned_count'] > 0) : ?>
                        <span class="badge bg-danger text-white fs-2"><i class="ti ti-alert-triangle me-1"></i>Belum Kembalikan <?= $member['unreturned_count']; ?> Buku</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </td>
              <td><small class="text-muted"><i class="ti ti-mail me-1 text-primary"></i><?= esc($member['email'] ?: '-'); ?></small></td>
              <td>
                <span class="badge badge-subtle-secondary fs-2">
                  <i class="ti <?= strtolower($member['gender'] ?? '') === 'male' || strtolower($member['gender'] ?? '') === 'l' ? 'ti-gender-male' : 'ti-gender-female'; ?> me-1"></i>
                  <?= strtolower($member['gender'] ?? '') === 'male' || strtolower($member['gender'] ?? '') === 'l' ? 'Laki-Laki' : 'Perempuan'; ?>
                </span>
              </td>
              <td class="text-center pe-4" style="width: 140px;">
                <?php if (!empty($member['unreturned_count']) && $member['unreturned_count'] > 0) : ?>
                  <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3" onclick="alert('⛔ Anggota <?= esc($member['first_name'] . ' ' . $member['last_name']); ?> masih memiliki <?= $member['unreturned_count']; ?> buku yang belum dikembalikan!\n\nHarap selesaikan pengembalian buku sebelumnya sebelum dapat melakukan peminjaman baru.')">
                    <i class="ti ti-lock me-1"></i> Terkunci
                  </button>
                <?php else : ?>
                  <a href="<?= base_url("admin/loans/new/books/search?member-uid={$member['uid']}"); ?>" class="btn btn-pill-gold btn-sm d-inline-flex align-items-center justify-content-center gap-1">
                    <i class="ti ti-check"></i> Pilih
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endif; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>