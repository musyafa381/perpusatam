<?= $this->extend('layouts/home_layout') ?>

<?= $this->section('head') ?>
<title><?= lang('Auth.register') ?> - Perpustakaan Assalafiyyah Mlangi</title>
<style>
  body {
    background: linear-gradient(135deg, #faf6f0 0%, #f4eae0 50%, #e9ded0 100%) !important;
    min-height: 100vh;
  }
  .register-wrapper {
    min-height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
  }
  .register-card {
    background: rgba(255, 255, 255, 0.94);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1.5px solid #e2d5c3 !important;
    box-shadow: 0 12px 40px rgba(110, 71, 39, 0.12) !important;
    border-radius: 1.5rem !important;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .register-card:hover {
    box-shadow: 0 16px 48px rgba(110, 71, 39, 0.16) !important;
  }
  .brand-badge {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 50%, #c59b27 100%);
    box-shadow: 0 8px 20px rgba(110, 71, 39, 0.25);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
  .form-control-custom {
    border: 1.5px solid #d4c4b0;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    font-size: 0.925rem;
    transition: all 0.2s ease;
    color: #2d241e;
    background-color: #ffffff;
  }
  .form-control-custom:focus {
    border-color: #8b5e3c !important;
    box-shadow: 0 0 0 0.25rem rgba(139, 94, 60, 0.18) !important;
    background-color: #ffffff;
  }
  .input-icon-prefix {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #8b5e3c;
    font-size: 1.15rem;
    pointer-events: none;
    z-index: 5;
  }
  .input-icon-suffix {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #8b5e3c;
    background: transparent;
    border: none;
    cursor: pointer;
    z-index: 5;
    padding: 0.25rem 0.5rem;
  }
  .btn-register-submit {
    background: linear-gradient(135deg, #6e4727 0%, #8b5e3c 50%, #c59b27 100%);
    border: none;
    color: #ffffff;
    padding: 0.85rem 1.5rem;
    font-weight: 700;
    border-radius: 0.75rem;
    letter-spacing: 0.3px;
    box-shadow: 0 6px 18px rgba(110, 71, 39, 0.22);
    transition: all 0.25s ease;
  }
  .btn-register-submit:hover {
    background: linear-gradient(135deg, #5b3a1f 0%, #764f31 50%, #b48c20 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(110, 71, 39, 0.3);
    color: #ffffff;
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('back') ?>
<div class="container pt-3">
  <a href="<?= url_to('login') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold bg-white shadow-sm border-0" style="color: #6e4727; box-shadow: 0 2px 10px rgba(0,0,0,0.06) !important;">
    <i class="ti ti-arrow-left me-1"></i> Kembali ke Halaman Login
  </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="register-wrapper">
  <div class="card register-card col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 mx-auto p-2 p-sm-4">
    <div class="card-body p-3 p-sm-4">
      
      <!-- Brand Header -->
      <div class="text-center mb-4">
        <div class="brand-badge mb-3">
          <i class="ti ti-user-plus fs-2 text-white"></i>
        </div>
        <h4 class="fw-extrabold mb-1" style="color: #4a3424; font-family: 'Georgia', serif;">Pendaftaran Pengguna</h4>
        <p class="text-muted small mb-0">Perpustakaan Assalafiyyah Mlangi</p>
      </div>

      <!-- Flash Error Alerts -->
      <?php if (session('error') !== null) : ?>
        <div class="alert alert-danger rounded-3 d-flex align-items-start gap-2 shadow-sm fs-7 mb-3" role="alert">
          <i class="ti ti-alert-circle fs-5 flex-shrink-0 mt-0.5"></i>
          <div><?= session('error') ?></div>
        </div>
      <?php elseif (session('errors') !== null) : ?>
        <div class="alert alert-danger rounded-3 shadow-sm fs-7 mb-3" role="alert">
          <div class="d-flex align-items-center gap-2 mb-1 fw-bold">
            <i class="ti ti-alert-triangle fs-5"></i> Periksa kembali data Anda:
          </div>
          <ul class="mb-0 ps-3">
            <?php if (is_array(session('errors'))) : ?>
              <?php foreach (session('errors') as $error) : ?>
                <li><?= esc($error) ?></li>
              <?php endforeach ?>
            <?php else : ?>
              <li><?= esc(session('errors')) ?></li>
            <?php endif ?>
          </ul>
        </div>
      <?php endif ?>

      <?php if (session('message') !== null) : ?>
        <div class="alert alert-success rounded-3 d-flex align-items-center gap-2 shadow-sm fs-7 mb-3" role="alert">
          <i class="ti ti-circle-check fs-5 flex-shrink-0"></i>
          <div><?= session('message') ?></div>
        </div>
      <?php endif ?>

      <!-- Register Form -->
      <form action="<?= base_url('admin/users') ?>" method="post" id="registerForm">
        <?= csrf_field() ?>

        <!-- Email Field -->
        <div class="mb-3">
          <label for="emailInput" class="form-label fw-bold small" style="color: #6e4727;"><?= lang('Auth.email') ?></label>
          <div class="position-relative">
            <i class="ti ti-mail input-icon-prefix"></i>
            <input type="email" 
                   id="emailInput"
                   class="form-control form-control-custom" 
                   name="email" 
                   inputmode="email" 
                   autocomplete="email" 
                   placeholder="nama@email.com" 
                   value="<?= old('email') ?>" 
                   required />
          </div>
        </div>

        <!-- Username Field -->
        <div class="mb-3">
          <label for="usernameInput" class="form-label fw-bold small" style="color: #6e4727;"><?= lang('Auth.username') ?></label>
          <div class="position-relative">
            <i class="ti ti-user input-icon-prefix"></i>
            <input type="text" 
                   id="usernameInput"
                   class="form-control form-control-custom" 
                   name="username" 
                   inputmode="text" 
                   autocomplete="username" 
                   placeholder="username_pengguna" 
                   value="<?= old('username') ?>" 
                   required />
          </div>
        </div>

        <!-- Password Field -->
        <div class="mb-3">
          <label for="passwordInput" class="form-label fw-bold small" style="color: #6e4727;"><?= lang('Auth.password') ?></label>
          <div class="position-relative">
            <i class="ti ti-lock input-icon-prefix"></i>
            <input type="password" 
                   id="passwordInput"
                   class="form-control form-control-custom" 
                   name="password" 
                   inputmode="text" 
                   autocomplete="new-password" 
                   placeholder="••••••••" 
                   required />
            <button type="button" id="togglePasswordBtn" class="input-icon-suffix" title="Tampilkan/Sembunyikan Password">
              <i class="ti ti-eye" id="togglePasswordIcon"></i>
            </button>
          </div>
        </div>

        <!-- Confirm Password Field -->
        <div class="mb-4">
          <label for="passwordConfirmInput" class="form-label fw-bold small" style="color: #6e4727;"><?= lang('Auth.passwordConfirm') ?></label>
          <div class="position-relative">
            <i class="ti ti-lock-check input-icon-prefix"></i>
            <input type="password" 
                   id="passwordConfirmInput"
                   class="form-control form-control-custom" 
                   name="password_confirm" 
                   inputmode="text" 
                   autocomplete="new-password" 
                   placeholder="••••••••" 
                   required />
            <button type="button" id="toggleConfirmBtn" class="input-icon-suffix" title="Tampilkan/Sembunyikan Konfirmasi Password">
              <i class="ti ti-eye" id="toggleConfirmIcon"></i>
            </button>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-register-submit d-flex align-items-center justify-content-center">
            <i class="ti ti-user-plus me-2 fs-5"></i> Daftar Akun Baru
          </button>
        </div>

      </form>

      <!-- Footer Navigation Link -->
      <div class="text-center mt-3 pt-3 border-top" style="border-color: #eee4d5 !important;">
        <p class="small text-muted mb-0">
          Sudah Memiliki Akun? 
          <a href="<?= url_to('login') ?>" class="fw-bold text-decoration-none" style="color: #8b5e3c;">Masuk ke Sistem <i class="ti ti-arrow-right fs-7 ms-0.5"></i></a>
        </p>
      </div>

    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Password Toggle
    const passwordInput = document.getElementById('passwordInput');
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
      toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleIcon.className = isPassword ? 'ti ti-eye-off' : 'ti ti-eye';
      });
    }

    // Password Confirm Toggle
    const passwordConfirmInput = document.getElementById('passwordConfirmInput');
    const toggleConfirmBtn = document.getElementById('toggleConfirmBtn');
    const toggleConfirmIcon = document.getElementById('toggleConfirmIcon');

    if (toggleConfirmBtn && passwordConfirmInput && toggleConfirmIcon) {
      toggleConfirmBtn.addEventListener('click', function() {
        const isPassword = passwordConfirmInput.type === 'password';
        passwordConfirmInput.type = isPassword ? 'text' : 'password';
        toggleConfirmIcon.className = isPassword ? 'ti ti-eye-off' : 'ti ti-eye';
      });
    }
  });
</script>
<?= $this->endSection() ?>