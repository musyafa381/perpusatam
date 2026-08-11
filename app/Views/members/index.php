<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Data Anggota</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert <?= (session()->getFlashdata('error') ?? false) ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<!-- Header Banner -->
<div class="card card-gradient-header shadow-sm mb-4 border-0">
  <div class="card-body p-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <div class="badge bg-white text-primary fw-bold px-3 py-1 mb-2 rounded-pill fs-2 shadow-sm">
          <i class="ti ti-users me-1"></i> Data Keanggotaan
        </div>
        <h3 class="text-white fw-bold mb-1">Data Anggota Perpustakaan</h3>
        <p class="text-white-50 mb-0">Kelola informasi siswa, santri, guru, dan petugas perpustakaan.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/members/new'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-user-plus me-1"></i> Tambah Anggota Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <!-- Filter Multi-kriteria -->
    <div class="p-3 bg-light rounded-3 border mb-4">
      <form action="" method="get">
        <div class="row g-2">
          <div class="col-12 col-md-4">
            <input type="text" class="form-control" name="search" value="<?= esc($search ?? ''); ?>" placeholder="Cari nama, email, barcode...">
          </div>
          <div class="col-6 col-md-3">
            <select class="form-select" name="type" onchange="this.form.requestSubmit()">
              <option value="">-- Semua Tipe --</option>
              <option value="siswa" <?= ($typeFilter ?? '') === 'siswa' ? 'selected' : ''; ?>>Siswa / Santri</option>
              <option value="petugas" <?= ($typeFilter ?? '') === 'petugas' ? 'selected' : ''; ?>>Petugas / Staf</option>
            </select>
          </div>
          <div class="col-6 col-md-3">
            <select class="form-select" name="institution" onchange="this.form.requestSubmit()">
              <option value="">-- Semua Instansi --</option>
              <option value="MTs" <?= ($institutionFilter ?? '') === 'MTs' ? 'selected' : ''; ?>>MTs</option>
              <option value="MA" <?= ($institutionFilter ?? '') === 'MA' ? 'selected' : ''; ?>>MA</option>
              <option value="SMK" <?= ($institutionFilter ?? '') === 'SMK' ? 'selected' : ''; ?>>SMK</option>
              <option value="PAUD" <?= ($institutionFilter ?? '') === 'PAUD' ? 'selected' : ''; ?>>PAUD</option>
              <option value="PDF" <?= ($institutionFilter ?? '') === 'PDF' ? 'selected' : ''; ?>>PDF</option>
              <option value="Ma'had Aly" <?= ($institutionFilter ?? '') === "Ma'had Aly" ? 'selected' : ''; ?>>Ma'had Aly</option>
            </select>
          </div>
          <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100 fw-semibold" type="submit"><i class="ti ti-search me-1"></i> Cari</button>
          </div>
        </div>
      </form>
    </div>

    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Nama Lengkap</th>
            <th scope="col">Tipe & Pendidikan</th>
            <th scope="col">Jenis Kelamin</th>
            <th scope="col">Kontak / Email</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1 + ($itemPerPage * ($currentPage - 1)) ?>
          <?php if (empty($members)) : ?>
            <tr>
              <td class="text-center py-4" colspan="6">
                <i class="ti ti-info-circle fs-6 d-block mb-1 text-muted"></i>
                <b>Tidak ada data anggota ditemukan</b>
              </td>
            </tr>
          <?php endif; ?>
          <?php foreach ($members as $member) : ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="d-flex align-items-center">
                  <div class="member-avatar me-2" style="width: 38px; height: 38px; font-size: 0.95rem;">
                    <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1) . substr($member['last_name'] ?? '', 0, 1)); ?>
                  </div>
                  <div>
                    <div class="fw-bold text-dark fs-3 mb-1"><?= esc("{$member['first_name']} {$member['last_name']}"); ?></div>
                    <?php $mTier = \App\Models\MemberModel::getTierDetails($member); ?>
                    <?php if (($mTier['code'] ?? 'none') === 'living_library') : ?>
                      <span class="badge bg-primary text-white fs-1 px-2 py-1"><i class="ti ti-building-community me-1"></i>Member Living Library</span>
                    <?php elseif (($mTier['code'] ?? 'none') !== 'none') : ?>
                      <span class="badge <?= $mTier['badge']; ?> fs-1 px-2 py-1"><i class="ti <?= $mTier['icon']; ?> me-1"></i><?= $mTier['name']; ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </td>

              <td>
                <?php if (($member['member_type'] ?? 'siswa') === 'siswa') : ?>
                  <span class="badge badge-subtle-primary me-1"><i class="ti ti-school me-1"></i>Siswa</span>
                  <span class="badge badge-subtle-secondary">
                    <?= esc($member['institution'] ?? 'Umum'); ?><?= !empty($member['class_level']) ? ' - ' . esc($member['class_level']) : ''; ?>
                  </span>
                <?php else : ?>
                  <span class="badge badge-subtle-success"><i class="ti ti-user-cog me-1"></i>Petugas / Staf</span>
                <?php endif; ?>
              </td>
              <td><?= ($member['gender'] == '1' || $member['gender'] == 'Male') ? 'Laki-laki' : 'Perempuan'; ?></td>
              <td>
                <?php if (!empty($member['email']) && strpos($member['email'], 'student_') === false) : ?>
                  <div><i class="ti ti-mail me-1 text-muted"></i><?= esc($member['email']); ?></div>
                <?php endif; ?>
                <?php if (!empty($member['phone']) && $member['phone'] !== '-') : ?>
                  <div class="text-muted fs-2"><i class="ti ti-phone me-1 text-muted"></i><?= esc($member['phone']); ?></div>
                <?php endif; ?>
                <?php if (empty($member['phone']) && (empty($member['email']) || strpos($member['email'], 'student_') !== false)) : ?>
                  <span class="text-muted fs-2">-</span>
                <?php endif; ?>
              </td>
              <td class="text-center pe-3">
                <a href="<?= base_url("admin/members/{$member['uid']}"); ?>" class="btn btn-primary btn-sm px-3 rounded-3">
                  Detail
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager->links('members', 'my_pager'); ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>