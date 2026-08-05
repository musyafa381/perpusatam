<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Data User & Admin</title>
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
          <i class="ti ti-user-check me-1"></i> Hak Akses Sistem
        </div>
        <h3 class="text-white fw-bold mb-1">Data User & Admin</h3>
        <p class="text-white-50 mb-0">Kelola akun pengguna, hak akses administrator, dan petugas perpustakaan.</p>
      </div>
      <div>
        <a href="<?= base_url('admin/users/new'); ?>" class="btn btn-light text-primary fw-bold shadow-sm">
          <i class="ti ti-user-plus me-1"></i> Tambah Admin Baru
        </a>
      </div>
    </div>
  </div>
</div>

<div class="card info-card border-0 mb-4">
  <div class="card-body p-4">
    <div class="table-responsive rounded-3 border">
      <table class="table table-hover align-middle table-custom">
        <thead>
          <tr>
            <th scope="col" class="ps-3">#</th>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Tanggal Dibuat</th>
            <th scope="col" class="text-center">Peran / Group</th>
            <th scope="col" class="text-center pe-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1 + ($itemPerPage * ($currentPage - 1)) ?>
          <?php foreach ($users as $user) : ?>
            <?php
            $userAttributes = $user->toArray();
            $userIdentities = $user->identities[0]->toArray();
            $userGroup = $user->getGroups()[0];
            ?>
            <tr>
              <th scope="row" class="ps-3 text-muted"><?= $i++; ?></th>
              <td>
                <div class="d-flex align-items-center">
                  <div class="member-avatar me-2" style="width: 38px; height: 38px; font-size: 0.95rem;">
                    <?= strtoupper(substr($userAttributes['username'] ?? 'U', 0, 2)); ?>
                  </div>
                  <div class="fw-bold text-dark fs-3"><?= esc($userAttributes['username']); ?></div>
                </div>
              </td>
              <td>
                <div class="fw-semibold text-dark"><i class="ti ti-mail me-1 text-muted"></i><?= esc($userIdentities['secret']); ?></div>
              </td>
              <td>
                <small class="text-muted"><i class="ti ti-calendar me-1"></i><?= esc($userAttributes['created_at']); ?></small>
              </td>
              <td class="text-center">
                <?php if ($userGroup === 'superadmin') : ?>
                  <span class="badge badge-subtle-success fs-2 px-3 py-2"><i class="ti ti-shield-lock me-1"></i>Superadmin</span>
                <?php elseif ($userGroup === 'admin') : ?>
                  <span class="badge badge-subtle-primary fs-2 px-3 py-2"><i class="ti ti-user-cog me-1"></i>Admin</span>
                <?php else : ?>
                  <span class="badge badge-subtle-secondary fs-2 px-3 py-2"><?= esc($userGroup); ?></span>
                <?php endif; ?>
              </td>
              <td class="text-center pe-3">
                <div class="d-flex justify-content-center gap-2">
                  <a href="<?= base_url("admin/users/{$userAttributes['id']}/edit"); ?>" class="btn btn-primary btn-sm px-3 rounded-3">
                    <i class="ti ti-edit me-1"></i> Edit
                  </a>
                  <form action="<?= base_url("admin/users/{$userAttributes['id']}"); ?>" method="post">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-3" data-confirm="Apakah Anda yakin ingin menghapus user ini?">
                      <i class="ti ti-trash me-1"></i> Hapus
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <?= $pager->links('users', 'my_pager'); ?>
    </div>
  </div>
</div>
<?= $this->endSection() ?>