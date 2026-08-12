<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
  
  <!-- Header Banner -->
  <div class="card bg-light-info shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
      <div class="row align-items-center">
        <div class="col-9">
          <h4 class="fw-semibold mb-8">📺 Kelola Konten TV Perpus (Multi-Banner)</h4>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a class="text-muted" href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Konten TV Perpus</li>
            </ol>
          </nav>
        </div>
        <div class="col-3 text-end">
          <i class="ti ti-device-tv text-primary" style="font-size: 3.5rem; opacity: 0.85;"></i>
        </div>
      </div>
    </div>
  </div>

  <?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
      <i class="ti ti-circle-check fs-5 me-2"></i> <?= session()->getFlashdata('message') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show p-3 shadow-sm mb-4" role="alert">
      <i class="ti ti-alert-triangle fs-5 me-2"></i> <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="row">
    <!-- Form Tambah Banner Baru -->
    <div class="col-12 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
          <h5 class="card-title fw-bold mb-0 text-primary">
            <i class="ti ti-plus me-2"></i>Tambah Banner TV Baru
          </h5>
        </div>
        <div class="card-body">
          <form action="<?= base_url('admin/tv-content/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3">
              <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Judul / Keterangan Banner</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Pengumuman Jam Layanan" required />
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Upload File Gambar Banner</label>
                <input type="file" name="poster" class="form-control" accept="image/png, image/jpeg, image/webp" />
                <div class="form-text text-muted">Format: PNG, JPG, WEBP.</div>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Atau URL Link Gambar</label>
                <input type="url" name="poster_url" class="form-control" placeholder="https://example.com/banner.jpg" />
              </div>
            </div>

            <div class="mt-3 text-end">
              <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                <i class="ti ti-check me-1"></i> Tambahkan ke Urutan TV
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Daftar Banner & Urutan -->
    <div class="col-12 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
          <h5 class="card-title fw-bold mb-0 text-dark">
            <i class="ti ti-list-numbers me-2"></i>Daftar Banner TV Active (<?= count($banners) ?> Banner)
          </h5>
          <span class="badge bg-light-primary text-primary fw-bold">Rotasi Otomatis di TV</span>
        </div>
        <div class="card-body p-0">
          <?php if (empty($banners)): ?>
            <div class="p-5 text-center text-muted">
              <i class="ti ti-photo-off text-secondary fs-9 mb-2"></i>
              <h5>Belum Ada Banner Khusus</h5>
              <p class="small mb-0">Silakan tambahkan banner baru dari form di atas.</p>
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="width: 70px;" class="text-center">URUTAN</th>
                    <th style="width: 140px;">THUMBNAIL</th>
                    <th>JUDUL BANNER</th>
                    <th style="width: 130px;" class="text-center">ATUR URUTAN</th>
                    <th style="width: 90px;" class="text-center">AKSI</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($banners as $idx => $b): ?>
                    <tr>
                      <td class="text-center font-bold fs-5">
                        <span class="badge bg-primary rounded-circle" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                          <?= $idx + 1 ?>
                        </span>
                      </td>
                      <td>
                        <div class="rounded overflow-hidden border border-1 border-secondary-subtle" style="width: 120px; height: 65px;">
                          <img src="<?= esc($b['url']) ?>" alt="<?= esc($b['title']) ?>" class="w-100 h-100" style="object-fit: cover;" />
                        </div>
                      </td>
                      <td>
                        <div class="fw-bold text-dark fs-4 mb-1"><?= esc($b['title']) ?></div>
                        <small class="text-muted d-block text-truncate" style="max-width: 280px;"><?= esc($b['url']) ?></small>
                      </td>
                      <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                          <?php if ($idx > 0): ?>
                            <a href="<?= base_url('admin/tv-content/move/' . $b['id'] . '/up') ?>" class="btn btn-outline-secondary" title="Naikkan Urutan">
                              <i class="ti ti-arrow-up"></i> Naik
                            </a>
                          <?php else: ?>
                            <button class="btn btn-outline-secondary" disabled><i class="ti ti-arrow-up"></i></button>
                          <?php endif; ?>

                          <?php if ($idx < count($banners) - 1): ?>
                            <a href="<?= base_url('admin/tv-content/move/' . $b['id'] . '/down') ?>" class="btn btn-outline-secondary" title="Turunkan Urutan">
                              <i class="ti ti-arrow-down"></i> Turun
                            </a>
                          <?php else: ?>
                            <button class="btn btn-outline-secondary" disabled><i class="ti ti-arrow-down"></i></button>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="text-center">
                        <a href="<?= base_url('admin/tv-content/delete/' . $b['id']) ?>" class="btn btn-sm btn-outline-danger" data-confirm-click="Apakah Anda yakin ingin menghapus banner '<?= esc($b['title']) ?>' ini?">
                          <i class="ti ti-trash me-1"></i> Hapus
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form[action*="tv-content/store"]');
  const fileInput = form ? form.querySelector('input[name="poster"]') : null;

  if (form && fileInput) {
    form.addEventListener('submit', function(e) {
      if (!fileInput.files || !fileInput.files[0]) return;
      const file = fileInput.files[0];

      // If file > 1.5MB and not yet compressed, auto-compress using canvas
      if (file.size > 1.5 * 1024 * 1024 && !form.dataset.compressed) {
        e.preventDefault();
        e.stopPropagation();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Mengompres Gambar...',
            text: 'Mengoptimalkan ukuran gambar banner agar tidak melebihi kapasitas server.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
          });
        }

        const reader = new FileReader();
        reader.onload = function(evt) {
          const img = new Image();
          img.onload = function() {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;

            const maxWidth = 1920;
            if (width > maxWidth) {
              height = Math.round((height * maxWidth) / width);
              width = maxWidth;
            }

            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob(function(blob) {
              if (blob) {
                const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                  type: 'image/jpeg',
                  lastModified: Date.now()
                });

                const container = new DataTransfer();
                container.items.add(compressedFile);
                fileInput.files = container.files;
              }

              form.dataset.compressed = "true";
              if (typeof Swal !== 'undefined') Swal.close();

              if (window.spaSubmitForm) {
                window.spaSubmitForm(form);
              } else {
                form.submit();
              }
            }, 'image/jpeg', 0.82);
          };
          img.src = evt.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
});
</script>
<?= $this->endSection() ?>
