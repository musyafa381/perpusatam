<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('head') ?>
<title>Edit Anggota</title>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<a href="<?= previous_url() ?>" class="btn btn-outline-primary mb-3">
  <i class="ti ti-arrow-left"></i> Kembali
</a>

<?php if (session()->getFlashdata('msg')) : ?>
  <div class="pb-2">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('msg') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-4">Edit Data Anggota</h5>
    <form action="<?= base_url('admin/members/' . $member['uid']); ?>" method="post">
      <?= csrf_field(); ?>
      <input type="hidden" name="_method" value="PUT">

      <!-- Pilihan Tipe Anggota -->
      <div class="mb-4 p-3 bg-light rounded border border-light-subtle">
        <label class="form-label fw-bold d-block text-dark">Tipe Anggota:</label>
        <?php $selectedType = $oldInput['member_type'] ?? $member['member_type'] ?? 'siswa'; ?>
        <div class="btn-group w-100 mb-2" role="group" aria-label="Member Type">
          <input type="radio" class="btn-check" name="member_type" id="type_siswa" value="siswa" <?= $selectedType === 'siswa' ? 'checked' : ''; ?> onchange="toggleMemberType('siswa')">
          <label class="btn btn-outline-primary py-2 fw-semibold" for="type_siswa"><i class="ti ti-school me-1"></i> Siswa / Santri</label>

          <input type="radio" class="btn-check" name="member_type" id="type_petugas" value="petugas" <?= $selectedType === 'petugas' ? 'checked' : ''; ?> onchange="toggleMemberType('petugas')">
          <label class="btn btn-outline-primary py-2 fw-semibold" for="type_petugas"><i class="ti ti-user-cog me-1"></i> Petugas / Staf</label>
        </div>

        <div id="siswa_required_notice" class="alert alert-info py-2 px-3 mb-0 fs-2 rounded-3 border-0">
          <i class="ti ti-info-circle me-1"></i> <strong>Ketentuan Pengisian:</strong> Untuk <strong>Siswa / Santri</strong>, bidang yang <strong>wajib diisi</strong> adalah <strong>Nama Depan, Jenis Kelamin, Instansi Pendidikan, dan Kelas/Semester</strong>. Bidang kontak & tanggal lahir bersifat opsional.
        </div>
      </div>

      <!-- Input Barcode ID Card / UID -->
      <div class="mb-4 p-3 bg-light rounded-3 border border-warning-subtle">
        <label for="uid" class="form-label fw-bold text-dark"><i class="ti ti-barcode text-primary me-1"></i> Kode Barcode ID Card / UID Anggota <span class="text-danger">*</span></label>
        <div class="input-group">
          <span class="input-group-text bg-white text-primary border-primary-subtle"><i class="ti ti-scan fs-5"></i></span>
          <input type="text" class="form-control border-primary-subtle <?php if ($validation->hasError('uid')) : ?>is-invalid<?php endif ?>" id="uid" name="uid" value="<?= esc($oldInput['uid'] ?? $member['uid'] ?? ''); ?>" placeholder="Tembak barcode ID Card fisik siswa/pegawai (NIS/NIP) atau ketik manual..." required>
          <?php if ($validation->hasError('uid')) : ?>
            <div class="invalid-feedback">
              <?= $validation->getError('uid'); ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="form-text fs-1 text-muted mt-1"><i class="ti ti-info-circle me-1 text-primary"></i>Bidang wajib diisi dengan menembakkan barcode ID Card siswa/pegawai atau diketik manual.</div>
      </div>

      <!-- Data Diri Utama -->
      <div class="row">
        <div class="col-12 col-md-6 mb-3">
          <label for="first_name" class="form-label">Nama depan <span class="text-danger">*</span></label>
          <input type="text" class="form-control <?php if ($validation->hasError('first_name')) : ?>is-invalid<?php endif ?>" id="first_name" name="first_name" value="<?= $oldInput['first_name'] ?? $member['first_name'] ?? ''; ?>" required>
          <div class="invalid-feedback">
            <?= $validation->getError('first_name'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="last_name" class="form-label">Nama belakang</label>
          <input type="text" class="form-control <?php if ($validation->hasError('last_name')) : ?>is-invalid<?php endif ?>" id="last_name" name="last_name" value="<?= $oldInput['last_name'] ?? $member['last_name'] ?? ''; ?>">
          <div class="invalid-feedback">
            <?= $validation->getError('last_name'); ?>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12 col-md-6 mb-3">
          <label class="form-label">Jenis kelamin <span class="text-danger">*</span></label>
          <?php $gender = $oldInput['gender'] ?? $member['gender'] ?? ''; ?>
          <div class="my-2 <?php if ($validation->hasError('gender')) : ?>is-invalid<?php endif ?>">
            <div class="form-check form-check-inline">
              <input type="radio" class="form-check-input" id="male" name="gender" value="1" <?= ($gender == '1' || $gender == 'Male') ? 'checked' : ''; ?> required>
              <label class="form-check-label" for="male">Laki-laki</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" class="form-check-input" id="female" name="gender" value="2" <?= ($gender == '2' || $gender == 'Female') ? 'checked' : ''; ?> required>
              <label class="form-check-label" for="female">Perempuan</label>
            </div>
          </div>
          <div class="invalid-feedback">
            <?= $validation->getError('gender'); ?>
          </div>
        </div>
      </div>

      <!-- Seksi Khusus Siswa -->
      <div id="siswa_section" class="p-3 border rounded border-primary bg-primary-subtle mb-4">
        <h6 class="fw-bold text-primary mb-3"><i class="ti ti-building-community me-1"></i> Data Pendidikan Siswa</h6>
        <div class="row">
          <div class="col-12 col-md-6 mb-3">
            <label for="institution" class="form-label fw-semibold text-dark">Instansi Pendidikan <span class="text-danger">*</span></label>
            <?php $instVal = $oldInput['institution'] ?? $member['institution'] ?? ''; ?>
            <select class="form-select <?php if ($validation->hasError('institution')) : ?>is-invalid<?php endif ?>" id="institution" name="institution" onchange="updateClassOptions(this.value)">
              <option value="" disabled <?= empty($instVal) ? 'selected' : ''; ?>>--Pilih Instansi--</option>
              <option value="MTs" <?= $instVal === 'MTs' ? 'selected' : ''; ?>>MTs</option>
              <option value="MA" <?= $instVal === 'MA' ? 'selected' : ''; ?>>MA</option>
              <option value="SMK" <?= $instVal === 'SMK' ? 'selected' : ''; ?>>SMK</option>
              <option value="PAUD" <?= $instVal === 'PAUD' ? 'selected' : ''; ?>>PAUD</option>
              <option value="PDF" <?= $instVal === 'PDF' ? 'selected' : ''; ?>>PDF</option>
              <option value="Ma'had Aly" <?= $instVal === "Ma'had Aly" ? 'selected' : ''; ?>>Ma'had Aly</option>
            </select>
            <div class="invalid-feedback">
              <?= $validation->getError('institution'); ?>
            </div>
          </div>
          <div class="col-12 col-md-6 mb-3">
            <label for="class_level" class="form-label fw-semibold text-dark">Kelas / Semester <span class="text-danger">*</span></label>
            <select class="form-select <?php if ($validation->hasError('class_level')) : ?>is-invalid<?php endif ?>" id="class_level" name="class_level">
              <option value="" disabled selected>--Pilih Instansi Terlebih Dahulu--</option>
            </select>
            <div class="invalid-feedback">
              <?= $validation->getError('class_level'); ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Seksi Tingkatan Member / Tier Override -->
      <div class="mb-4 p-3 bg-light rounded-3 border border-primary-subtle">
        <label for="manual_tier" class="form-label fw-bold text-dark"><i class="ti ti-award text-primary me-1"></i> Tingkatan Member / Kuota Peminjaman</label>
        <?php $selectedManualTier = $oldInput['manual_tier'] ?? $member['manual_tier'] ?? 'none'; ?>
        <select class="form-select border-primary fw-bold" id="manual_tier" name="manual_tier">
          <option value="none" <?= $selectedManualTier === 'none' ? 'selected' : ''; ?>>Otomatis (Standar Donasi Buku)</option>
          <option value="living_library" <?= $selectedManualTier === 'living_library' ? 'selected' : ''; ?>>🏫 Living Library / Paket Kelas (Maks 50 Buku, 90 Hari)</option>
          <option value="silver" <?= $selectedManualTier === 'silver' ? 'selected' : ''; ?>>Silver Member (Maks 1 Buku, 7 Hari)</option>
          <option value="gold" <?= $selectedManualTier === 'gold' ? 'selected' : ''; ?>>Gold Member (Maks 3 Buku, 10 Hari)</option>
          <option value="platinum" <?= $selectedManualTier === 'platinum' ? 'selected' : ''; ?>>Platinum Member (Maks 5 Buku, 14 Hari)</option>
        </select>
        <div class="form-text fs-1 text-muted mt-1"><i class="ti ti-info-circle me-1"></i>Pilih <strong>Living Library / Paket Kelas</strong> untuk akun perwakilan kelas / program kelompok.</div>
      </div>

      <!-- Input Hidden untuk ID -->
      <input type="hidden" name="id" value="<?= $member['id']; ?>">


      <!-- Seksi Tambahan -->
      <div id="additional_fields" class="row">

        <div class="col-12 col-md-6 mb-3">
          <label for="email" class="form-label" id="label_email">Email <span class="text-danger" id="req_email">*</span></label>
          <input type="email" class="form-control <?php if ($validation->hasError('email')) : ?>is-invalid<?php endif ?>" id="email" name="email" value="<?= $oldInput['email'] ?? $member['email'] ?? ''; ?>">
          <div class="invalid-feedback">
            <?= $validation->getError('email'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="phone" class="form-label" id="label_phone">Nomor telepon <span class="text-danger" id="req_phone">*</span></label>
          <input type="tel" class="form-control <?php if ($validation->hasError('phone')) : ?>is-invalid<?php endif ?>" id="phone" name="phone" value="<?= $oldInput['phone'] ?? $member['phone'] ?? ''; ?>">
          <div class="invalid-feedback">
            <?= $validation->getError('phone'); ?>
          </div>
        </div>
        <div class="col-12 col-md-6 mb-3">
          <label for="date_of_birth" class="form-label" id="label_dob">Tanggal lahir <span class="text-danger" id="req_dob">*</span></label>
          <input type="date" class="form-control <?php if ($validation->hasError('date_of_birth')) : ?>is-invalid<?php endif ?>" id="date_of_birth" name="date_of_birth" value="<?= $oldInput['date_of_birth'] ?? $member['date_of_birth'] ?? ''; ?>">
          <div class="invalid-feedback">
            <?= $validation->getError('date_of_birth'); ?>
          </div>
        </div>
        <div class="col-12 mb-3">
          <label for="address" class="form-label" id="label_address">Alamat <span class="text-danger" id="req_address">*</span></label>
          <textarea class="form-control <?php if ($validation->hasError('address')) : ?>is-invalid<?php endif ?>" id="address" name="address" rows="2"><?= $oldInput['address'] ?? $member['address'] ?? ''; ?></textarea>
          <div class="invalid-feedback">
            <?= $validation->getError('address'); ?>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-pill-gold mt-2 px-4 py-2 fw-bold shadow-sm"><i class="ti ti-device-floppy me-1"></i> Simpan Perubahan Data Anggota</button>
    </form>

  </div>
</div>

<script>
  const savedClassVal = '<?= esc($oldInput['class_level'] ?? $member['class_level'] ?? ''); ?>';

  function toggleMemberType(type) {
    const siswaSection = document.getElementById('siswa_section');
    const noticeEl = document.getElementById('siswa_required_notice');
    const instSelect = document.getElementById('institution');
    const classSelect = document.getElementById('class_level');
    
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('phone');
    const addressField = document.getElementById('address');
    const dobField = document.getElementById('date_of_birth');

    const reqEmail = document.getElementById('req_email');
    const reqPhone = document.getElementById('req_phone');
    const reqAddress = document.getElementById('req_address');
    const reqDob = document.getElementById('req_dob');

    if (type === 'siswa') {
      siswaSection.classList.remove('d-none');
      instSelect.required = true;
      classSelect.required = true;

      emailField.required = false;
      phoneField.required = false;
      addressField.required = false;
      dobField.required = false;

      reqEmail.classList.add('d-none');
      reqPhone.classList.add('d-none');
      reqAddress.classList.add('d-none');
      reqDob.classList.add('d-none');

      if (noticeEl) {
        noticeEl.innerHTML = '<i class="ti ti-info-circle me-1"></i> <strong>Ketentuan Pengisian:</strong> Untuk <strong>Siswa / Santri</strong>, bidang yang <strong>WAJIB diisi</strong> adalah <strong>Nama Depan, Jenis Kelamin, Instansi Pendidikan, dan Kelas/Semester</strong>. Bidang kontak & tanggal lahir bersifat opsional.';
      }
    } else {
      siswaSection.classList.add('d-none');
      instSelect.required = false;
      classSelect.required = false;

      emailField.required = true;
      phoneField.required = true;
      addressField.required = false;
      dobField.required = false;

      reqEmail.classList.remove('d-none');
      reqPhone.classList.remove('d-none');
      reqAddress.classList.add('d-none');
      reqDob.classList.add('d-none');

      if (noticeEl) {
        noticeEl.innerHTML = '<i class="ti ti-info-circle me-1"></i> <strong>Ketentuan Pengisian:</strong> Untuk <strong>Petugas / Staf</strong>, bidang yang <strong>WAJIB diisi</strong> adalah <strong>Nama Depan, Jenis Kelamin, Email, dan Telepon</strong>. Alamat & Tanggal Lahir bersifat opsional.';
      }
    }
  }

  function updateClassOptions(institution) {
    const classSelect = document.getElementById('class_level');
    classSelect.innerHTML = '<option value="" disabled>--Pilih Kelas / Semester--</option>';

    let options = [];

    if (institution === 'MTs') {
      options = ['Kelas 7', 'Kelas 8', 'Kelas 9'];
    } else if (['MA', 'SMK', 'PDF'].includes(institution)) {
      options = ['Kelas 10', 'Kelas 11', 'Kelas 12'];
    } else if (institution === "Ma'had Aly") {
      options = [
        'Semester 1', 'Semester 2', 'Semester 3', 'Semester 4',
        'Semester 5', 'Semester 6', 'Semester 7', 'Semester 8'
      ];
    } else if (institution === 'PAUD') {
      options = ['PAUD A', 'PAUD B'];
    }

    let hasSelected = false;
    options.forEach(opt => {
      const isSelected = (opt === savedClassVal) ? 'selected' : '';
      if (isSelected) hasSelected = true;
      classSelect.innerHTML += `<option value="${opt}" ${isSelected}>${opt}</option>`;
    });

    if (!hasSelected) {
      classSelect.options[0].selected = true;
    }
  }

  function initEditMemberForm() {
    const checkedType = document.querySelector('input[name="member_type"]:checked');
    if (checkedType) {
      toggleMemberType(checkedType.value);
    }

    const instEl = document.getElementById('institution');
    if (instEl && instEl.value) {
      updateClassOptions(instEl.value);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initEditMemberForm);
  } else {
    initEditMemberForm();
  }
</script>
<?= $this->endSection() ?>