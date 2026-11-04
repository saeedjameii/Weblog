/* site-ui.js - فقط افزودنی.
   به attributeهای data، به idهای منو، تقویم شمسی و مرتب‌سازی دسته‌بندی و به
   منطق گالری app.js ([data-product-thumbnail]) دست نمی‌زند. */
(function () {
  'use strict';
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    document.documentElement.classList.add('js');

    /* هدر: جمع شدن با اسکرول (کلاس is-scrolled از قبل در style.css هم تعریف شده) */
    var header = document.querySelector('.site-header-glass');
    if (header) {
      var ticking = false;
      var onScroll = function () {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(function () {
          header.classList.toggle('is-scrolled', window.scrollY > 12);
          ticking = false;
        });
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    }

    /* نوار پیشرفت مطالعه */
    var target = document.querySelector('[data-reading-target]');
    var bar = document.querySelector('[data-read-progress]');
    if (target && bar) {
      var upd = function () {
        var r = target.getBoundingClientRect();
        var total = r.height - window.innerHeight;
        var p = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 0;
        bar.style.transform = 'scaleX(' + p + ')';
      };
      window.addEventListener('scroll', upd, { passive: true });
      window.addEventListener('resize', upd);
      upd();
    }

    /* اسکلت بارگذاری عکس */
    document.querySelectorAll('.frame.is-loading img').forEach(function (img) {
      var frame = img.closest('.frame');
      var done = function () { frame.classList.remove('is-loading'); };
      if (img.complete) done();
      else { img.addEventListener('load', done, { once: true }); img.addEventListener('error', done, { once: true }); }
    });

    /* ظاهر شدن با اسکرول */
    var reveals = document.querySelectorAll('.reveal');
    if (reveals.length) {
      if (reduceMotion || !('IntersectionObserver' in window)) {
        reveals.forEach(function (el) { el.classList.add('is-visible'); });
      } else {
        var io = new IntersectionObserver(function (entries) {
          entries.forEach(function (e) {
            if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); }
          });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
      }
    }

    /* Bottom Sheet فیلتر (موبایل). در دسکتاپ پنل همیشه نمایان است. */
    var sheet = document.querySelector('[data-sheet]');
    var backdrop = document.querySelector('[data-sheet-backdrop]');
    if (sheet) {
      var lastFocus = null;
      var openers = document.querySelectorAll('[data-sheet-open]');
      var setSheet = function (open) {
        sheet.classList.toggle('is-open', open);
        if (backdrop) backdrop.classList.toggle('is-open', open);
        openers.forEach(function (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); });
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) { lastFocus = document.activeElement; var f = sheet.querySelector('input,select,a,button'); if (f) f.focus(); }
        else if (lastFocus) { lastFocus.focus(); lastFocus = null; }
      };
      openers.forEach(function (b) { b.addEventListener('click', function () { setSheet(true); }); });
      document.querySelectorAll('[data-sheet-close]').forEach(function (b) { b.addEventListener('click', function () { setSheet(false); }); });
      if (backdrop) backdrop.addEventListener('click', function () { setSheet(false); });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && sheet.classList.contains('is-open')) setSheet(false); });
      window.matchMedia('(min-width: 1024px)').addEventListener('change', function (m) { if (m.matches && sheet.classList.contains('is-open')) setSheet(false); });
    }

    /* تب‌ها: بدون JS همه‌ی پنل‌ها دیده می‌شوند و tablist مخفی است. */
    document.querySelectorAll('[data-tabs]').forEach(function (wrap) {
      var list = wrap.querySelector('[role="tablist"]');
      var tabs = Array.prototype.slice.call(wrap.querySelectorAll('[role="tab"]'));
      if (!tabs.length) return;
      if (list) list.hidden = false;
      var select = function (tab) {
        tabs.forEach(function (t) {
          var on = t === tab;
          t.setAttribute('aria-selected', on ? 'true' : 'false');
          t.tabIndex = on ? 0 : -1;
          var p = document.getElementById(t.getAttribute('aria-controls'));
          if (p) p.hidden = !on;
        });
      };
      tabs.forEach(function (t, i) {
        t.addEventListener('click', function () { select(t); });
        t.addEventListener('keydown', function (e) {
          var n = e.key === 'ArrowLeft' ? i + 1 : e.key === 'ArrowRight' ? i - 1 : null; /* RTL */
          if (n === null) return;
          n = (n + tabs.length) % tabs.length; tabs[n].focus(); select(tabs[n]); e.preventDefault();
        });
      });
      select(tabs.filter(function (t) { return t.getAttribute('aria-selected') === 'true'; })[0] || tabs[0]);
    });

    /* بستن خودکار پیام موفقیت: [data-auto-dismiss="5000"] */
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
      var ms = parseInt(el.getAttribute('data-auto-dismiss'), 10) || 5000;
      setTimeout(function () { el.hidden = true; }, ms);
    });
  });
})();