<?php

/**
 * List of sidebar navigations
 */
$sidebarNavs =
  [
    'Home',
    [
      'name' => 'Dashboard',
      'link' => '/admin/dashboard',
      'icon' => 'ti ti-layout-dashboard'
    ],
    'Transaksi',
    [
      'name' => 'Buku Tamu & Booking',
      'link' => '/admin/visitors',
      'icon' => 'ti ti-id-badge-2'
    ],
    [
      'name' => 'Peminjaman',
      'link' => '/admin/loans',
      'icon' => 'ti ti-arrows-exchange'
    ],
    [
      'name' => 'Pengembalian',
      'link' => '/admin/returns',
      'icon' => 'ti ti-check'
    ],
    [
      'name' => 'Denda',
      'link' => '/admin/fines',
      'icon' => 'ti ti-report-money'
    ],
    'Master',
    [
      'name' => 'Anggota',
      'link' => '/admin/members',
      'icon' => 'ti ti-user'
    ],
    [
      'name' => 'Kartu Member',
      'link' => '/admin/members/cards',
      'icon' => 'ti ti-id-badge-2'
    ],
    [
      'name' => 'Buku',
      'link' => '/admin/books',
      'icon' => 'ti ti-book'
    ],
    [
      'name' => 'Atribut Buku',
      'link' => '/admin/book-attributes',
      'icon' => 'ti ti-tags'
    ],
    'Tampilan TV',
    [
      'name' => 'Konten TV Perpus',
      'link' => '/admin/tv-content',
      'icon' => 'ti ti-device-tv'
    ],
    'Sistem',
    [
      'name' => 'Pengaturan Perpus',
      'link' => '/admin/settings',
      'icon' => 'ti ti-settings'
    ],
  ];

if (auth()->user()->inGroup('superadmin') ?? false) {
  $sidebarNavs = array_merge(
    $sidebarNavs,
    [
      'Manajemen Akun',
      [
        'name' => 'Admin',
        'link' => '/admin/users',
        'icon' => 'ti ti-user-cog'
      ]
    ]
  );
}
?>

<!-- Sidebar Start -->
<aside class="left-sidebar">
  <!-- Sidebar scroll-->
  <div>
    <!-- Brand -->
    <div class="brand-logo d-flex align-items-center justify-content-between px-3 pt-3 pb-2">
      <a href="<?= base_url(); ?>" class="text-decoration-none d-flex align-items-center gap-2">
        <img src="<?= base_url('assets/images/logoku.jpg'); ?>" alt="Logo Perpustakaan Assalafiyyah" class="shadow-sm" style="height: 46px; width: 46px; border-radius: 12px; object-fit: cover; flex-shrink: 0; border: 1px solid #e8decb;">
        <div class="text-start">
          <h6 class="fw-bold mb-0" style="color: #6e4727; font-family: 'Georgia', 'Garamond', serif; font-size: 0.98rem; line-height: 1.15;">Perpustakaan Pusat</h6>
          <span class="fw-bold" style="font-size: 0.78rem; color: #c59b27; display: block; line-height: 1.2; margin-top: 1px;">Assalafiyyah</span>
        </div>
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-8"></i>
      </div>
    </div>

    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
      <ul id="sidebarnav">
        <?php foreach ($sidebarNavs as $nav) : ?>
          <?php if (gettype($nav) === 'string') : ?>
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu"><?= $nav; ?></span>
            </li>
          <?php else : ?>
            <li class="sidebar-item">
              <a class="sidebar-link" href="<?= base_url($nav['link']) ?>" aria-expanded="false">
                <span>
                  <i class="<?= $nav['icon']; ?>"></i>
                </span>
                <span class="hide-menu"><?= $nav['name']; ?></span>
              </a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </nav>
    <!-- End Sidebar navigation -->
  </div>
  <!-- End Sidebar scroll-->
</aside>
<!--  Sidebar End -->