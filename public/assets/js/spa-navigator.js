/**
 * SPA Dynamic Content Navigator & Form Handler for Perpustakaan Admin Dashboard
 * Smoothly updates content container and processes all CRUD form submissions without full page reload.
 */

(function () {
  'use strict';

  const CONTAINER_SELECTOR = '#spa-content-container';
  const PROGRESS_BAR_ID = 'top-progress-bar';

  function getProgressBar() {
    let bar = document.getElementById(PROGRESS_BAR_ID);
    if (!bar) {
      bar = document.createElement('div');
      bar.id = PROGRESS_BAR_ID;
      document.body.appendChild(bar);
    }
    return bar;
  }

  function startProgress() {
    const bar = getProgressBar();
    bar.style.opacity = '1';
    bar.style.width = '15%';
    setTimeout(() => { bar.style.width = '45%'; }, 100);
    setTimeout(() => { bar.style.width = '75%'; }, 250);
  }

  function endProgress() {
    const bar = getProgressBar();
    bar.style.width = '100%';
    setTimeout(() => {
      bar.style.opacity = '0';
      setTimeout(() => { bar.style.width = '0%'; }, 300);
    }, 200);
  }

  // Cleanup open Bootstrap modals and backdrops
  function cleanupModals() {
    if (typeof jQuery !== 'undefined') {
      try {
        jQuery('.modal').modal('hide');
      } catch (e) {}
    }
    document.querySelectorAll('.modal.show').forEach((modalEl) => {
      if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        try {
          const instance = bootstrap.Modal.getInstance(modalEl);
          if (instance) instance.hide();
        } catch (e) {}
      }
    });
    document.querySelectorAll('.modal-backdrop').forEach((el) => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
  }

  // -------------------------------------------------------
  // MOBILE SIDEBAR MANAGER
  // Handles: auto-close on menu click, overlay backdrop,
  // and re-binding toggler events after SPA navigation.
  // -------------------------------------------------------

  // Create or get the sidebar overlay (dark backdrop behind sidebar on mobile)
  function getSidebarOverlay() {
    let overlay = document.getElementById('spa-sidebar-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'spa-sidebar-overlay';
      overlay.style.cssText = [
        'display:none',
        'position:fixed',
        'top:0',
        'left:0',
        'width:100vw',
        'height:100vh',
        'background:rgba(0,0,0,0.45)',
        'z-index:9998',
        'transition:opacity 0.25s ease',
        'opacity:0',
        '-webkit-tap-highlight-color:transparent',
        'cursor:pointer',
      ].join(';');
      document.body.appendChild(overlay);

      // Click overlay → close sidebar
      overlay.addEventListener('click', function () {
        closeMobileSidebar();
      });
    }
    return overlay;
  }

  // Check if we're in mobile viewport (sidebar is overlay mode)
  function isMobileViewport() {
    return window.innerWidth < 1199;
  }

  // Open sidebar on mobile
  function openMobileSidebar() {
    if (!isMobileViewport()) return;
    const wrapper = document.getElementById('main-wrapper');
    if (!wrapper) return;
    wrapper.classList.add('show-sidebar');
    // Show overlay
    const overlay = getSidebarOverlay();
    overlay.style.display = 'block';
    requestAnimationFrame(() => { overlay.style.opacity = '1'; });
  }

  // Close sidebar on mobile and hide overlay
  function closeMobileSidebar() {
    const wrapper = document.getElementById('main-wrapper');
    if (wrapper) {
      wrapper.classList.remove('show-sidebar');
      wrapper.classList.add('mini-sidebar');
      wrapper.setAttribute('data-sidebartype', 'mini-sidebar');
    }
    const overlay = getSidebarOverlay();
    overlay.style.opacity = '0';
    setTimeout(() => { overlay.style.display = 'none'; }, 250);
  }

  // Re-initialize sidebar toggler after SPA navigation.
  // IMPORTANT: app.min.js binds .sidebartoggler with plain .on('click', ...)
  // (no namespace). After SPA navigation those handlers still exist and conflict
  // with ours. We must .off('click') ALL handlers then re-bind only ours.
  function reinitSidebar() {
    if (typeof jQuery === 'undefined') return;
    const $ = jQuery;

    // Ensure overlay exists
    getSidebarOverlay();

    // Remove ALL click handlers (including app.min.js originals) — prevents
    // double-fire conflict where app.min.js toggles show-sidebar first, then
    // our handler sees the post-toggle state and does the wrong action.
    $('.sidebartoggler').off('click');

    // Re-bind a single, unified sidebar toggler handler
    $('.sidebartoggler').on('click', function () {
      const wrapper = document.getElementById('main-wrapper');
      if (!wrapper) return;

      if (isMobileViewport()) {
        // Mobile mode: toggle show-sidebar + overlay
        if (wrapper.classList.contains('show-sidebar')) {
          closeMobileSidebar();
        } else {
          openMobileSidebar();
        }
      } else {
        // Desktop mode: toggle mini-sidebar (mirroring app.min.js original logic)
        const isMini = wrapper.classList.toggle('mini-sidebar');
        wrapper.setAttribute('data-sidebartype', isMini ? 'mini-sidebar' : 'full');
        // Ensure overlay & show-sidebar are cleared on desktop
        wrapper.classList.remove('show-sidebar');
        const overlay = getSidebarOverlay();
        overlay.style.opacity = '0';
        overlay.style.display = 'none';
      }
    });

    // Auto-close sidebar when any sidebar link is clicked on mobile
    $(document).off('click.spa-sidebar-link').on('click.spa-sidebar-link', '.sidebar-link', function () {
      if (isMobileViewport()) {
        closeMobileSidebar();
      }
    });
  }


  // Inject any extra <style> or <link rel="stylesheet"> from new page
  function injectStyles(doc) {
    const styles = doc.querySelectorAll('head style, head link[rel="stylesheet"]');
    styles.forEach((s) => {
      if (s.tagName.toLowerCase() === 'style') {
        const styleEl = document.createElement('style');
        styleEl.textContent = s.textContent;
        document.head.appendChild(styleEl);
      }
    });
  }

  // Execute scripts inside loaded container and section scripts
  function runContainerScripts(doc, container) {
    // 1. Run external/inline scripts found in the container
    const containerScripts = container.querySelectorAll('script');
    containerScripts.forEach((oldScript) => {
      const newScript = document.createElement('script');
      Array.from(oldScript.attributes).forEach((attr) => {
        newScript.setAttribute(attr.name, attr.value);
      });
      newScript.textContent = oldScript.textContent;
      oldScript.parentNode.replaceChild(newScript, oldScript);
    });

    // 2. Run scripts found in doc body/head (e.g., renderSection('scripts'))
    if (doc) {
      const pageScripts = doc.querySelectorAll('body > script, head > script');
      pageScripts.forEach((oldScript) => {
        // Skip scripts that are part of global layout imports
        const src = oldScript.getAttribute('src') || '';
        if (src.includes('spa-navigator.js') || src.includes('bootstrap') || src.includes('jquery.min.js')) {
          return;
        }

        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach((attr) => {
          newScript.setAttribute(attr.name, attr.value);
        });
        newScript.textContent = oldScript.textContent;
        document.body.appendChild(newScript);
      });
    }

    // Re-initialize Select2 if available
    if (typeof window.initSelect2Search === 'function') {
      try {
        window.initSelect2Search(container);
      } catch (e) {}
    } else if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
      try {
        jQuery('.select2').select2({ theme: 'bootstrap-5' });
      } catch (e) {}
    }
  }

  // Update active sidebar item
  function updateSidebarActiveState(targetUrlStr) {
    if (typeof window.highlightSidebarMenu === 'function') {
      window.highlightSidebarMenu();
      return;
    }

    const sidebarLinks = Array.from(document.querySelectorAll('.sidebar-link, .sidebar-nav a'));
    if (!sidebarLinks.length) return;

    let targetPath = window.location.pathname;
    try {
      targetPath = new URL(targetUrlStr, window.location.origin).pathname;
    } catch (e) {}

    // First remove active/selected from all
    sidebarLinks.forEach((link) => {
      link.classList.remove('active');
      const parentLi = link.closest('.sidebar-item');
      if (parentLi) parentLi.classList.remove('selected');
    });

    let bestLink = null;
    let maxMatchLen = -1;

    sidebarLinks.forEach((link) => {
      const hrefAttr = link.getAttribute('href');
      if (!hrefAttr || hrefAttr === '#' || hrefAttr.startsWith('javascript:')) return;

      let linkPath = hrefAttr;
      try {
        linkPath = new URL(link.href, window.location.origin).pathname;
      } catch (e) {}

      // Exact match or prefix match
      if (targetPath === linkPath) {
        if (linkPath.length + 100 > maxMatchLen) {
          maxMatchLen = linkPath.length + 100; // Priority to exact match
          bestLink = link;
        }
      } else if (targetPath.startsWith(linkPath.endsWith('/') ? linkPath : linkPath + '/')) {
        if (linkPath.length > maxMatchLen) {
          maxMatchLen = linkPath.length;
          bestLink = link;
        }
      }
    });

    if (bestLink) {
      bestLink.classList.add('active');
      const parentLi = bestLink.closest('.sidebar-item');
      if (parentLi) parentLi.classList.add('selected');
    }
  }

  // Main page loader function (GET)
  async function loadPage(url, pushHistory = true) {
    const container = document.querySelector(CONTAINER_SELECTOR);
    if (!container) {
      window.location.href = url;
      return;
    }

    cleanupModals();
    startProgress();

    try {
      const response = await fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-PJAX': 'true',
        },
      });

      if (!response.ok) {
        window.location.href = url;
        return;
      }

      const html = await response.text();
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      // Update document title
      if (doc.title) {
        document.title = doc.title;
      }

      // Inject dynamic styles
      injectStyles(doc);

      // Find new content container
      const newContainer = doc.querySelector(CONTAINER_SELECTOR);
      if (newContainer) {
        container.innerHTML = newContainer.innerHTML;
        container.classList.remove('spa-fade-in');
        void container.offsetWidth; // Trigger reflow
        container.classList.add('spa-fade-in');

        runContainerScripts(doc, container);
      } else {
        window.location.href = url;
        return;
      }

      if (pushHistory) {
        window.history.pushState({ spa: true, url: url }, doc.title || '', url);
      }

      updateSidebarActiveState(url);
      reinitSidebar();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err) {
      console.error('SPA load error:', err);
      window.location.href = url;
    } finally {
      endProgress();
    }
  }

  // Main CRUD form submission function (POST / PUT / DELETE)
  async function submitForm(form, submitter) {
    const container = document.querySelector(CONTAINER_SELECTOR);
    if (!container) {
      form.submit();
      return;
    }

    startProgress();

    try {
      const actionUrl = form.action || window.location.href;
      const formData = new FormData(form);

      if (submitter && submitter.name) {
        formData.append(submitter.name, submitter.value || '');
      }

      let method = (form.getAttribute('method') || 'POST').toUpperCase();
      const spoofMethodInput = form.querySelector('input[name="_method"]');
      const spoofMethod = spoofMethodInput ? spoofMethodInput.value.toUpperCase() : null;

      // Note: HTML forms submit as POST or GET. Method spoofing (_method=PUT/DELETE) is parsed by CI4 server-side.
      // JS fetch() must use POST so browser includes FormData body intact (browsers strip body on DELETE/PUT).
      if (method !== 'GET') {
        method = 'POST';
      }

      if (method === 'GET') {
        const urlObj = new URL(actionUrl, window.location.href);
        for (const [key, val] of formData.entries()) {
          if (val !== '' && val !== null) {
            urlObj.searchParams.set(key, val);
          } else {
            urlObj.searchParams.delete(key);
          }
        }
        cleanupModals();
        loadPage(urlObj.href, true);
        return;
      }

      cleanupModals();

      const response = await fetch(actionUrl, {
        method: method,
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-PJAX': 'true',
        },
      });

      if (!response.ok && response.status !== 422 && response.status !== 400) {
        window.location.href = actionUrl;
        return;
      }

      const responseUrl = response.url || actionUrl;
      const html = await response.text();

      // Check if JSON response returned
      try {
        const json = JSON.parse(html);
        if (json && typeof json === 'object') {
          if (json.msg || json.message) {
            if (typeof Swal !== 'undefined') {
              Swal.fire({
                icon: json.error ? 'error' : 'success',
                title: json.msg || json.message,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
              });
            }
          }
          if (json.redirect) {
            loadPage(json.redirect, true);
          } else {
            loadPage(window.location.href, false);
          }
          return;
        }
      } catch (e) {
        // HTML response
      }

      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');

      if (doc.title) {
        document.title = doc.title;
      }

      injectStyles(doc);

      const newContainer = doc.querySelector(CONTAINER_SELECTOR);
      if (newContainer) {
        container.innerHTML = newContainer.innerHTML;
        container.classList.remove('spa-fade-in');
        void container.offsetWidth;
        container.classList.add('spa-fade-in');

        runContainerScripts(doc, container);
      } else {
        window.location.href = responseUrl;
        return;
      }

      window.history.pushState({ spa: true, url: responseUrl }, doc.title || '', responseUrl);

      updateSidebarActiveState(responseUrl);
      reinitSidebar();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (err) {
      console.error('SPA form submit error:', err);
      window.location.href = form.action || window.location.href;
    } finally {
      endProgress();
    }
  }

  // Intercept click events on links
  document.addEventListener('click', function (e) {
    const anchor = e.target.closest('a');
    if (!anchor) return;

    const href = anchor.getAttribute('href');
    if (!href) return;

    // Skip special links
    if (
      href.startsWith('#') ||
      href.startsWith('javascript:') ||
      href.startsWith('mailto:') ||
      href.startsWith('tel:') ||
      anchor.hasAttribute('download') ||
      anchor.getAttribute('target') === '_blank' ||
      anchor.hasAttribute('data-no-pjax') ||
      anchor.hasAttribute('data-confirm')
    ) {
      return;
    }

    // Check same origin
    const targetUrl = new URL(anchor.href, window.location.href);
    if (targetUrl.origin !== window.location.origin) return;

    // Skip if clicking current URL
    if (targetUrl.href === window.location.href) {
      e.preventDefault();
      return;
    }

    e.preventDefault();
    loadPage(targetUrl.href, true);
  });

  // Intercept form submit events globally
  document.addEventListener('submit', function (e) {
    const form = e.target;

    // Skip forms with data-no-pjax, target="_blank", or file upload forms
    if (
      form.hasAttribute('data-no-pjax') ||
      form.getAttribute('target') === '_blank' ||
      form.classList.contains('no-spa') ||
      form.getAttribute('enctype') === 'multipart/form-data'
    ) {
      return;
    }

    const container = document.querySelector(CONTAINER_SELECTOR);
    if (!container) return;

    // Check if form or submitter requires SweetAlert confirmation before submitting
    const submitter = e.submitter;
    const hasConfirm = form.hasAttribute('data-confirm') || (submitter && submitter.hasAttribute('data-confirm'));
    if (hasConfirm && !form.dataset.confirmed) {
      // Let basic_scripts.php handle the SweetAlert popup first
      return;
    }

    if (form.dataset.confirmed) {
      delete form.dataset.confirmed;
    }

    e.preventDefault();
    submitForm(form, submitter);
  });

  // Handle browser back / forward
  window.addEventListener('popstate', function (e) {
    loadPage(window.location.href, false);
  });

  // Initial state setup
  window.history.replaceState({ spa: true, url: window.location.href }, document.title, window.location.href);

  // Initialize sidebar manager on first page load.
  // MUST run AFTER app.min.js has bound its handlers (app.min.js uses $(function(){...})).
  // By using jQuery's ready queue here, we ensure our reinitSidebar() runs after
  // all $(function(){...}) callbacks from other scripts have already executed.
  function waitForJQueryThenInit() {
    if (typeof jQuery !== 'undefined') {
      // Queue inside jQuery ready — runs after app.min.js $(function(){...}) handlers
      jQuery(function () {
        // Extra tick to ensure we're truly after all ready callbacks
        setTimeout(reinitSidebar, 0);
      });
    } else {
      // jQuery not yet loaded, retry
      setTimeout(waitForJQueryThenInit, 50);
    }
  }
  waitForJQueryThenInit();


  // Expose global methods
  window.spaLoadPage = loadPage;
  window.spaSubmitForm = submitForm;
  window.spaCloseSidebar = closeMobileSidebar;
  window.spaOpenSidebar = openMobileSidebar;
})();
