<script src="<?= base_url("assets/libs/jquery/jquery.min.js") ?>"></script>
<script src="<?= base_url("assets/libs/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
<script src="<?= base_url("assets/libs/jsbarcode/JsBarcode.all.min.js") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function initSelect2Search(context) {
  const $context = $(context || document);
  $context.find('select.select2, select.select-search, select.searchable-select').each(function() {
    const $el = $(this);
    if ($el.hasClass('select2-hidden-accessible')) {
      return;
    }
    const $modal = $el.closest('.modal');
    $el.select2({
      theme: 'bootstrap-5',
      width: '100%',
      placeholder: $el.find('option:disabled:first-child').text() || 'Cari / Pilih...',
      allowClear: !$el.prop('required'),
      closeOnSelect: true,
      dropdownParent: $modal.length ? $modal : $(document.body)
    });
  });
}
window.initSelect2Search = initSelect2Search;

document.addEventListener('DOMContentLoaded', function() {
  initSelect2Search();

  $('.modal').on('shown.bs.modal', function() {
    initSelect2Search(this);
  });

  document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.dataset.confirmed) {
      return true;
    }

    const submitter = e.submitter;
    let confirmMsg = null;
    if (form.hasAttribute('data-confirm')) {
      confirmMsg = form.getAttribute('data-confirm');
    } else if (submitter && submitter.hasAttribute('data-confirm')) {
      confirmMsg = submitter.getAttribute('data-confirm');
    }

    if (confirmMsg) {
      e.preventDefault();
      e.stopPropagation();
      const isDelete = form.querySelector('input[name="_method"][value="DELETE"]') || (submitter && submitter.classList.contains('btn-danger'));

      Swal.fire({
        title: isDelete ? 'Konfirmasi Hapus' : 'Konfirmasi Aksi',
        text: confirmMsg,
        icon: isDelete ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonText: isDelete ? 'Ya, Hapus!' : 'Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        focusCancel: true,
        customClass: {
          confirmButton: isDelete ? 'btn btn-danger me-2' : 'btn btn-primary me-2',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          form.dataset.confirmed = "true";
          if (window.spaSubmitForm) {
            window.spaSubmitForm(form, submitter);
          } else {
            form.submit();
          }
        }
      });
    }
  });

  document.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-confirm-click]');
    if (btn) {
      e.preventDefault();
      const msg = btn.getAttribute('data-confirm-click');
      const href = btn.getAttribute('href');
      Swal.fire({
        title: 'Konfirmasi',
        text: msg,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
        focusCancel: true,
        customClass: {
          confirmButton: 'btn btn-danger me-2',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed && href) {
          if (window.spaLoadPage) {
            window.spaLoadPage(href);
          } else {
            window.location.href = href;
          }
        }
      });
    }
  });
});
</script>
<?php if (session()->getFlashdata('msg')) : ?>
<script>
(function() {
  function showToastMsg() {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: '<?= (session()->getFlashdata('error') ?? false) ? 'error' : 'success'; ?>',
        title: '<?= addslashes(session()->getFlashdata('msg')); ?>',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true
      });
    }
  }
  window.showToastMsg = showToastMsg;
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showToastMsg);
  } else {
    showToastMsg();
  }
})();
</script>
<?php endif; ?>
<!-- AOS Animation Script -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
      });
    }
  });
</script>