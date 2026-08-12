<!--  Header Start -->
<style>
  @media only screen and (max-width: 768px) {
    #navBtn {
      display: none;
    }
  }
  .profile-dropdown-menu {
    min-width: 310px;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
  }
</style>
<header class="app-header">
  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapseBtn" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end gap-2">
        <li class="nav-item" id="navBtn">
          <a href="<?= base_url('admin/loans/new/members/search'); ?>" target="_blank" class="btn btn-primary text-nowrap">
            <i class="ti ti-plus me-1"></i> Ajukan peminjaman
          </a>
        </li>
        <li class="nav-item" id="navBtn">
          <a href="<?= base_url('admin/returns/new/search'); ?>" class="btn btn-outline-primary text-nowrap">
            <i class="ti ti-rotate-clockwise-2 me-1"></i> Pengembalian buku
          </a>
        </li>
        <li class="nav-item" id="navBtn">
          <a href="<?= base_url('admin/fines/returns/search'); ?>" class="btn btn-outline-warning text-nowrap">
            <i class="ti ti-receipt-tax me-1"></i> Bayar denda
          </a>
        </li>
        <?php if (auth()->user()->inGroup('superadmin')) : ?>
          <li class="nav-item" id="navBtn">
            <a href="<?= base_url('admin/fines/settings'); ?>" class="btn btn-outline-danger text-nowrap">
              <i class="ti ti-settings me-1"></i> Pengaturan Denda
            </a>
          </li>
        <?php endif; ?>

        <!-- Profile Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon-hover position-relative p-1" href="javascript:void(0)" id="profileDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle border border-2 border-primary bg-white d-flex align-items-center justify-content-center shadow-xs" style="width: 40px; height: 40px; cursor: pointer;">
              <i class="ti ti-user fs-6 text-primary" style="pointer-events: none;"></i>
            </div>
          </a>
          <div class="dropdown-menu dropdown-menu-end profile-dropdown-menu p-3 animated fadeIn" aria-labelledby="profileDropdownBtn">
            <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
              <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold fs-5 shadow-xs" style="width: 48px; height: 48px;">
                <?= strtoupper(substr(auth()->user()->username ?? 'A', 0, 1)); ?>
              </div>
              <div class="overflow-hidden">
                <h6 class="fw-bold text-dark mb-0 text-truncate"><?= esc(auth()->user()->username); ?></h6>
                <small class="text-muted d-block text-truncate"><?= esc(auth()->user()->email); ?></small>
                <?php
                $userGroups = auth()->user()->getGroups();
                $userGroup = !empty($userGroups) ? $userGroups[0] : 'admin';
                ?>
                <div class="mt-1">
                  <?php if ($userGroup === 'superadmin') : ?>
                    <span class="badge bg-danger text-white fs-1 rounded-pill"><i class="ti ti-shield-lock me-1"></i>Superadmin</span>
                  <?php elseif ($userGroup === 'admin') : ?>
                    <span class="badge bg-primary text-white fs-1 rounded-pill"><i class="ti ti-user-check me-1"></i>Admin Petugas</span>
                  <?php else : ?>
                    <span class="badge bg-secondary text-white fs-1 rounded-pill"><?= esc($userGroup); ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <div class="d-grid gap-2">
              <button type="button" class="btn btn-light text-start text-dark fw-semibold btn-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#profileInfoModal">
                <i class="ti ti-id-badge fs-5 text-primary"></i> Detail Akun Saya
              </button>
              <a href="<?= base_url('logout'); ?>" class="btn btn-outline-danger btn-sm fw-bold d-flex align-items-center justify-content-center gap-2 mt-1">
                <i class="ti ti-logout fs-5"></i> Keluar / Logout
              </a>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</header>

<!-- Modal Detail Akun -->
<div class="modal fade" id="profileInfoModal" tabindex="-1" aria-labelledby="profileInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header card-gradient-header rounded-top-4 text-white p-3">
        <h5 class="modal-title fw-bold text-white mb-0" id="profileInfoModalLabel">
          <i class="ti ti-user-circle me-2"></i> Informasi Profil Akun Petugas
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="text-center mb-4">
          <div class="rounded-circle bg-primary text-white mx-auto d-flex align-items-center justify-content-center fw-bold fs-8 shadow-sm mb-2" style="width: 72px; height: 72px;">
            <?= strtoupper(substr(auth()->user()->username ?? 'A', 0, 1)); ?>
          </div>
          <h5 class="fw-bold text-dark mb-0"><?= esc(auth()->user()->username); ?></h5>
          <span class="badge bg-primary-subtle text-primary fw-bold mt-1 px-3 py-1 fs-2">
            <i class="ti ti-shield-check me-1"></i> Level Akses: <?= strtoupper($userGroup); ?>
          </span>
        </div>

        <div class="bg-light p-3 rounded-3 border mb-3">
          <div class="row g-2">
            <div class="col-12">
              <small class="text-muted fw-bold d-block">USERNAME AKUN</small>
              <span class="fw-bold text-dark fs-3"><?= esc(auth()->user()->username); ?></span>
            </div>
            <div class="col-12 mt-2">
              <small class="text-muted fw-bold d-block">EMAIL ALAMAT</small>
              <span class="fw-bold text-dark fs-3"><?= esc(auth()->user()->email); ?></span>
            </div>
            <div class="col-12 mt-2">
              <small class="text-muted fw-bold d-block">HAK AKSES / PERAN</small>
              <span class="fw-bold text-dark fs-3"><?= implode(', ', auth()->user()->getGroups()); ?></span>
            </div>
          </div>
        </div>

        <div class="alert alert-info rounded-3 mb-0 fs-2">
          <i class="ti ti-info-circle me-1"></i> Akun terautentikasi dan memiliki hak akses penuh untuk mengelola modul Sistem Informasi Perpustakaan.
        </div>
      </div>
      <div class="modal-footer bg-light rounded-bottom-4">
        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup</button>
        <a href="<?= base_url('logout'); ?>" class="btn btn-danger rounded-pill px-4 fw-bold">
          <i class="ti ti-logout me-1"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('click', function (e) {
  const btn = e.target.closest('#profileDropdownBtn');
  if (!btn) return;
  
  const menu = btn.nextElementSibling;
  if (!menu) return;

  if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) {
    e.preventDefault();
    e.stopPropagation();
    menu.classList.toggle('show');
  }
});
</script>
<!--  Header End -->