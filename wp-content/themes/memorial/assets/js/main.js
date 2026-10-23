/* אתר זיכרון: JS קל, בלי תלויות */
(function () {
  'use strict';
  var doc = document, root = doc.documentElement;
  root.classList.add('js');

  var $ = function (s, c) { return (c || doc).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || doc).querySelectorAll(s)); };
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var store = {
    get: function (k) { try { return window.localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { window.localStorage.setItem(k, v); } catch (e) { /* ignore */ } }
  };

  /* ---------- תפריט נייד ---------- */
  var toggle = $('.nav-toggle'), nav = $('#nav');
  if (toggle && nav) {
    var setNav = function (open) {
      doc.body.classList.toggle('nav-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    };
    toggle.addEventListener('click', function () { setNav(!doc.body.classList.contains('nav-open')); });
    doc.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setNav(false); } });
    nav.addEventListener('click', function (e) { if (e.target.closest('a')) { setNav(false); } });
  }

  /* ---------- חשיפה בגלילה ---------- */
  if (!reduce && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
    $$('.section__head, .card--value, .path li, .timeline li, .featured__main, .benches-teaser__grid > *, .tell__grid > *, .gallery__item').forEach(function (el) {
      el.classList.add('reveal');
      io.observe(el);
    });
  }

  /* ---------- שיתוף והעתקה ---------- */
  $$('[data-share]').forEach(function (box) {
    var url = box.getAttribute('data-url'), title = box.getAttribute('data-title');
    var copy = $('[data-copy]', box), native = $('[data-native-share]', box);
    if (copy) {
      copy.addEventListener('click', function () {
        var label = $('span', copy), old = label.textContent;
        var done = function () { label.textContent = 'הקישור הועתק'; setTimeout(function () { label.textContent = old; }, 2200); };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(done, function () { window.prompt('העתיקו את הקישור:', url); });
        } else { window.prompt('העתיקו את הקישור:', url); }
      });
    }
    if (native && navigator.share) {
      native.hidden = false;
      native.addEventListener('click', function () { navigator.share({ title: title, url: url }).catch(function () {}); });
    }
  });

  /* ---------- סינון סיפורים וחיפוש ---------- */
  $$('[data-filters]').forEach(function (box) {
    var mode = box.getAttribute('data-mode');
    if (mode === 'sections') {
      var secBtns = $$('[data-filter-section]', box), secs = $$('[data-section]');
      var showSec = function (slug) {
        secBtns.forEach(function (b) { var on = b.getAttribute('data-filter-section') === slug; b.classList.toggle('is-on', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); });
        secs.forEach(function (s) { s.hidden = !!slug && s.getAttribute('data-section') !== slug; });
      };
      secBtns.forEach(function (b) { b.addEventListener('click', function () { showSec(b.getAttribute('data-filter-section')); }); });
      return;
    }
    var grid = $('[data-grid]'), empty = $('[data-empty]'), status = $('[data-filter-status]', box);
    if (!grid) { return; }
    var cards = $$('.card', grid);
    var text = $('[data-filter-text]', box);
    var chBtns = $$('[data-filter-chapter]', box), vlBtns = $$('[data-filter-value]', box);
    var state = { ch: '', vl: '', q: '' };
    var norm = function (s) { return (s || '').toLowerCase().replace(/[֑-ׇ"'״׳]/g, ''); };
    var apply = function () {
      var n = 0, q = norm(state.q).trim();
      cards.forEach(function (c) {
        var ok = (!state.ch || (' ' + c.getAttribute('data-chapters') + ' ').indexOf(' ' + state.ch + ' ') > -1) &&
                 (!state.vl || (' ' + c.getAttribute('data-values') + ' ').indexOf(' ' + state.vl + ' ') > -1) &&
                 (!q || norm(c.getAttribute('data-text')).indexOf(q) > -1);
        c.hidden = !ok;
        if (ok) { n++; }
      });
      chBtns.forEach(function (b) { var on = b.getAttribute('data-filter-chapter') === state.ch; b.classList.toggle('is-on', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); });
      vlBtns.forEach(function (b) { var on = b.getAttribute('data-filter-value') === state.vl; b.classList.toggle('is-on', on); b.setAttribute('aria-pressed', on ? 'true' : 'false'); });
      if (status) { status.textContent = n === 0 ? 'לא נמצאו סיפורים' : (n === 1 ? 'סיפור אחד' : n + ' סיפורים'); }
      if (empty) { empty.hidden = n !== 0; }
    };
    chBtns.forEach(function (b) { b.addEventListener('click', function () { state.ch = b.getAttribute('data-filter-chapter'); apply(); }); });
    vlBtns.forEach(function (b) { b.addEventListener('click', function () { var v = b.getAttribute('data-filter-value'); state.vl = state.vl === v ? '' : v; apply(); }); });
    if (text) { text.addEventListener('input', function () { state.q = text.value; apply(); }); }
    var reset = $('[data-filter-reset]');
    if (reset) { reset.addEventListener('click', function () { state = { ch: '', vl: '', q: '' }; if (text) { text.value = ''; } apply(); }); }
    // פרמטרים מהכתובת
    try {
      var p = new URLSearchParams(window.location.search);
      if (p.get('chapter')) { state.ch = p.get('chapter'); }
      if (p.get('value')) { state.vl = p.get('value'); }
      if (p.get('q')) { state.q = p.get('q'); if (text) { text.value = state.q; } }
    } catch (e) { /* ignore */ }
    apply();
  });

  /* ---------- Lightbox ---------- */
  var lb, lbImg, lbCap, lbCount, lbItems = [], lbIdx = 0, lbLast = null;
  var buildLb = function () {
    lb = doc.createElement('dialog');
    lb.className = 'lb';
    lb.setAttribute('aria-label', 'תצוגת תמונה מוגדלת');
    lb.innerHTML = '<div class="lb__stage"><img class="lb__img" alt=""><div class="lb__cap" aria-live="polite"></div></div>' +
      '<button type="button" class="lb__btn lb__close" aria-label="סגירה"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>' +
      '<button type="button" class="lb__btn lb__prev" aria-label="התמונה הקודמת"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button>' +
      '<button type="button" class="lb__btn lb__next" aria-label="התמונה הבאה"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>' +
      '<div class="lb__count" aria-hidden="true"></div>';
    doc.body.appendChild(lb);
    lbImg = $('.lb__img', lb); lbCap = $('.lb__cap', lb); lbCount = $('.lb__count', lb);
    $('.lb__close', lb).addEventListener('click', function () { lb.close(); });
    $('.lb__prev', lb).addEventListener('click', function () { show(lbIdx - 1); });
    $('.lb__next', lb).addEventListener('click', function () { show(lbIdx + 1); });
    lb.addEventListener('click', function (e) { if (e.target === lb || e.target.classList.contains('lb__stage')) { lb.close(); } });
    lb.addEventListener('close', function () { if (lbLast) { lbLast.focus(); } });
    lb.addEventListener('keydown', function (e) {
      // כיוון RTL: חץ שמאלה = הבא
      if (e.key === 'ArrowLeft') { show(lbIdx + 1); e.preventDefault(); }
      if (e.key === 'ArrowRight') { show(lbIdx - 1); e.preventDefault(); }
    });
    var sx = null;
    lb.addEventListener('touchstart', function (e) { sx = e.changedTouches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', function (e) {
      if (sx === null) { return; }
      var dx = e.changedTouches[0].clientX - sx; sx = null;
      if (Math.abs(dx) > 50) { show(dx < 0 ? lbIdx + 1 : lbIdx - 1); }
    }, { passive: true });
  };
  var show = function (i) {
    if (!lbItems.length) { return; }
    lbIdx = (i + lbItems.length) % lbItems.length;
    var a = lbItems[lbIdx];
    lbImg.src = a.href;
    lbImg.alt = a.getAttribute('data-alt') || '';
    lbCap.textContent = a.getAttribute('data-caption') || '';
    lbCount.textContent = (lbIdx + 1) + ' / ' + lbItems.length;
    var nx = lbItems[(lbIdx + 1) % lbItems.length];
    if (nx) { var pre = new Image(); pre.src = nx.href; }
  };
  doc.addEventListener('click', function (e) {
    var a = e.target.closest('[data-lightbox] .g-item');
    if (!a || e.metaKey || e.ctrlKey || e.shiftKey) { return; }
    e.preventDefault();
    if (!lb) { buildLb(); }
    var gal = a.closest('[data-lightbox]');
    lbItems = $$('.g-item', gal);
    lbLast = a;
    show(lbItems.indexOf(a));
    if (lb.showModal) { lb.showModal(); } else { lb.setAttribute('open', ''); }
  });

  /* ---------- טפסים (שליחה ברקע) ---------- */
  $$('form[data-ajax-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.fetch || !window.FormData) { return; }
      var msg = $('[data-msg]', form), btn = $('button[type=submit]', form);
      // בדיקת גודל קבצים
      var files = $$('input[type=file]', form), tooBig = false;
      files.forEach(function (f) { Array.prototype.forEach.call(f.files || [], function (file) { if (file.size > 12 * 1024 * 1024) { tooBig = true; } }); });
      var say = function (ok, text) {
        if (!msg) { return; }
        msg.hidden = false;
        msg.className = 'form-msg ' + (ok ? 'form-msg--ok' : 'form-msg--err');
        msg.textContent = text;
        msg.setAttribute('tabindex', '-1');
        if (ok === false || ok === true) { msg.focus({ preventScroll: false }); }
      };
      if (tooBig) { e.preventDefault(); say(false, 'אחד הקבצים גדול מ-12MB. אפשר לשלוח תמונה קטנה יותר.'); return; }
      e.preventDefault();
      var old = btn ? btn.innerHTML : '';
      if (btn) { btn.disabled = true; btn.setAttribute('aria-busy', 'true'); }
      say(null, 'שולחים…');
      msg.className = 'form-msg';
      fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'fetch' }, credentials: 'same-origin' })
        .then(function (r) { return r.json().catch(function () { return { ok: r.ok, message: 'התקבלה תשובה לא צפויה מהשרת.' }; }); })
        .then(function (j) {
          say(!!j.ok, j.message || (j.ok ? 'תודה!' : 'משהו השתבש.'));
          if (j.ok) { form.reset(); var dlg = form.closest('dialog'); if (dlg) { setTimeout(function () { if (dlg.open) { dlg.close(); } }, 2600); } }
        })
        .catch(function () { say(false, 'לא הצלחנו לשלוח. בדקו את החיבור ונסו שוב.'); })
        .then(function () { if (btn) { btn.disabled = false; btn.removeAttribute('aria-busy'); btn.innerHTML = old; } });
    });
  });

  /* ---------- דיאלוג משוב ---------- */
  var fb = $('#feedback-dialog');
  if (fb) {
    $$('[data-feedback-open]').forEach(function (b) { b.addEventListener('click', function () { if (fb.showModal) { fb.showModal(); } else { fb.setAttribute('open', ''); } var t = $('textarea', fb); if (t) { t.focus(); } }); });
    $$('[data-dialog-close]', fb).forEach(function (b) { b.addEventListener('click', function () { fb.close(); }); });
    fb.addEventListener('click', function (e) { if (e.target === fb) { fb.close(); } });
  }

  /* ---------- סרגל נגישות ---------- */
  var saved = {};
  try { saved = JSON.parse(store.get('memorial_a11y') || '{}'); } catch (e) { saved = {}; }
  var applyA11y = function () {
    root.classList.toggle('a11y-contrast', !!saved.contrast);
    root.classList.toggle('a11y-gray', !!saved.gray);
    root.classList.toggle('a11y-links', !!saved.links);
    root.classList.toggle('a11y-font', !!saved.font);
    root.classList.toggle('a11y-nomotion', !!saved.nomotion);
    ['a11y-big-1', 'a11y-big-2', 'a11y-big-3'].forEach(function (c) { root.classList.remove(c); });
    if (saved.size) { root.classList.add('a11y-big-' + saved.size); }
    store.set('memorial_a11y', JSON.stringify(saved));
    $$('.a11y__btn[data-key]').forEach(function (b) { b.setAttribute('aria-pressed', saved[b.getAttribute('data-key')] ? 'true' : 'false'); });
  };
  var icon = function (p) { return '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + p + '</svg>'; };
  var wrap = doc.createElement('div');
  wrap.className = 'a11y';
  wrap.innerHTML =
    '<button type="button" class="a11y__toggle" aria-expanded="false" aria-controls="a11y-panel" aria-label="תפריט נגישות" data-tip="נגישות">' +
      icon('<circle cx="16" cy="4" r="1"/><path d="m18 19 1-7-6 1"/><path d="m5 8 3-3 5.5 3-2.36 3.5"/><path d="M4.24 14.5a5 5 0 0 0 6.88 6"/><path d="M13.76 17.5a5 5 0 0 0-6.88-6"/>') + '</button>' +
    '<div class="a11y__panel" id="a11y-panel" role="group" aria-label="כלי נגישות">' +
      '<h2 class="a11y__title">נגישות</h2>' +
      '<div class="a11y__grid">' +
        '<button type="button" class="a11y__btn" data-act="bigger">' + icon('<path d="M5 12h14"/><path d="M12 5v14"/>') + 'הגדלת טקסט</button>' +
        '<button type="button" class="a11y__btn" data-act="smaller">' + icon('<path d="M5 12h14"/>') + 'הקטנת טקסט</button>' +
        '<button type="button" class="a11y__btn" data-key="contrast" aria-pressed="false">' + icon('<circle cx="12" cy="12" r="10"/><path d="M12 18a6 6 0 0 0 0-12v12z"/>') + 'ניגודיות גבוהה</button>' +
        '<button type="button" class="a11y__btn" data-key="gray" aria-pressed="false">' + icon('<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/>') + 'גווני אפור</button>' +
        '<button type="button" class="a11y__btn" data-key="links" aria-pressed="false">' + icon('<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>') + 'הדגשת קישורים</button>' +
        '<button type="button" class="a11y__btn" data-key="font" aria-pressed="false">' + icon('<polyline points="4 7 4 4 20 4 20 7"/><line x1="9" x2="15" y1="20" y2="20"/><line x1="12" x2="12" y1="4" y2="20"/>') + 'גופן קריא</button>' +
        '<button type="button" class="a11y__btn" data-key="nomotion" aria-pressed="false">' + icon('<rect x="14" y="4" width="4" height="16" rx="1"/><rect x="6" y="4" width="4" height="16" rx="1"/>') + 'עצירת אנימציות</button>' +
        '<button type="button" class="a11y__btn a11y__btn--wide" data-act="reset">' + icon('<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>') + 'איפוס הגדרות</button>' +
      '</div>' +
      '<a class="a11y__link" href="' + (window.MEMORIAL ? window.MEMORIAL.home : '/') + 'accessibility/">הצהרת נגישות</a>' +
    '</div>';
  doc.body.appendChild(wrap);
  var tgl = $('.a11y__toggle', wrap);
  tgl.addEventListener('click', function () {
    var open = !wrap.classList.contains('a11y--open');
    wrap.classList.toggle('a11y--open', open);
    tgl.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  wrap.addEventListener('keydown', function (e) { if (e.key === 'Escape') { wrap.classList.remove('a11y--open'); tgl.setAttribute('aria-expanded', 'false'); tgl.focus(); } });
  wrap.addEventListener('click', function (e) {
    var b = e.target.closest('.a11y__btn');
    if (!b) { return; }
    var k = b.getAttribute('data-key'), act = b.getAttribute('data-act');
    if (k) { saved[k] = !saved[k]; }
    if (act === 'bigger') { saved.size = Math.min(3, (saved.size || 0) + 1); }
    if (act === 'smaller') { saved.size = Math.max(0, (saved.size || 0) - 1); }
    if (act === 'reset') { saved = {}; }
    applyA11y();
  });
  applyA11y();

  /* ---------- סרטון מוטמע בלחיצה ---------- */
  $$('[data-vframe]').forEach(function (box) {
    var b = $('button', box);
    if (!b) { return; }
    b.addEventListener('click', function () {
      var f = document.createElement('iframe');
      f.src = box.getAttribute('data-src');
      f.allow = 'autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share';
      f.allowFullscreen = true;
      f.title = box.getAttribute('data-title') || 'סרטון';
      box.innerHTML = '';
      box.appendChild(f);
    });
  });
  /* ---------- פס תמונות מגולל ---------- */
  $$('[data-strip]').forEach(function (s) {
    var rail = $('.gallery', s);
    if (!rail) { return; }
    var go = function (dir) { rail.scrollBy({ left: dir * rail.clientWidth * 0.8, behavior: 'smooth' }); };
    var n = $('[data-strip-next]', s), p = $('[data-strip-prev]', s);
    if (n) { n.addEventListener('click', function () { go(-1); }); }
    if (p) { p.addEventListener('click', function () { go(1); }); }
  });
})();
