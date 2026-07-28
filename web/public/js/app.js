(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }

  function initReveal() {
    var nodes = document.querySelectorAll('.reveal');
    if (!nodes.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      nodes.forEach(function (el) {
        el.classList.add('is-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
    );

    nodes.forEach(function (el) {
      observer.observe(el);
    });
  }

  function initHeader() {
    var header = document.querySelector('[data-site-header]');
    if (!header) return;

    var onScroll = function () {
      if (window.scrollY > 8) {
        header.classList.add('is-scrolled');
      } else {
        header.classList.remove('is-scrolled');
      }
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  function initMobileNav() {
    var toggle = document.querySelector('[data-nav-toggle]');
    var nav = document.querySelector('[data-site-nav]');
    if (!toggle || !nav) return;

    var setOpen = function (open) {
      nav.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      document.body.classList.toggle('nav-open', open);
    };

    toggle.addEventListener('click', function () {
      setOpen(!nav.classList.contains('is-open'));
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        setOpen(false);
      });
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') setOpen(false);
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 960) setOpen(false);
    });
  }

  function initSidebarToggle() {
    var toggle = document.querySelector('[data-sidebar-toggle]');
    var sidebar = document.querySelector('[data-sets-sidebar]');
    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
      var open = sidebar.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  function initAdminSidebarToggle() {
    var toggle = document.querySelector('[data-admin-nav-toggle]');
    var nav = document.querySelector('[data-admin-nav]');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      nav.hidden = !open && window.innerWidth <= 960;
    });

    var sync = function () {
      if (window.innerWidth > 960) {
        nav.hidden = false;
        nav.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
      } else if (!nav.classList.contains('is-open')) {
        nav.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
      }
    };

    sync();
    window.addEventListener('resize', sync);
  }

  ready(function () {
    initReveal();
    initHeader();
    initMobileNav();
    initSidebarToggle();
    initAdminSidebarToggle();
  });
})();
