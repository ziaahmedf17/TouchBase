/* =============================================
   TouchBase CRM — Client JS
   Vanilla, no build tools required
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

  // ── CSRF token helper ──────────────────────
  function csrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  }

  function post(url, data = {}) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken(),
        'Accept': 'application/json',
      },
      body: JSON.stringify(data),
    }).then(function (r) {
      return r.text().then(function (text) {
        var json = null;
        try { json = JSON.parse(text); } catch (e) {}
        if (!r.ok) {
          var msg = (json && json.message) ? json.message : ('Server error ' + r.status);
          throw new Error(msg);
        }
        if (!json) throw new Error('Invalid server response');
        return json;
      });
    });
  }

  function del(url) {
    return fetch(url, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrfToken(),
        'Accept': 'application/json',
      },
    }).then(r => r.json());
  }

  // ── Hamburger menu (mobile) ───────────────
  var hamburgerBtn = document.getElementById('hamburger-btn');
  var navLinks     = document.getElementById('nav-links');

  if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = navLinks.classList.toggle('open');
      hamburgerBtn.classList.toggle('open', open);
      hamburgerBtn.setAttribute('aria-expanded', open);
    });
    document.addEventListener('click', function (e) {
      if (!navLinks.contains(e.target) && e.target !== hamburgerBtn) {
        navLinks.classList.remove('open');
        hamburgerBtn.classList.remove('open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      }
    });
    // Close menu when a nav link is tapped on mobile
    navLinks.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        navLinks.classList.remove('open');
        hamburgerBtn.classList.remove('open');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
      });
    });
  }

  // ── User menu dropdown ────────────────────
  var userMenuBtn    = document.getElementById('user-menu-btn');
  var userDropdown   = document.getElementById('user-dropdown');

  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle('open');
      // close bell if open
      if (notifDropdown) notifDropdown.classList.remove('open');
    });
    document.addEventListener('click', function (e) {
      if (!userDropdown.contains(e.target)) {
        userDropdown.classList.remove('open');
      }
    });
  }

  // ── Notification Bell ─────────────────────
  const bellBtn     = document.getElementById('bell-btn');
  const bellBadge   = document.getElementById('bell-badge');
  const notifDropdown = document.getElementById('notif-dropdown');

  function updateBellCount(count) {
    if (!bellBadge) return;
    if (count > 0) {
      bellBadge.textContent = count > 99 ? '99+' : count;
      bellBadge.classList.remove('hidden');
    } else {
      bellBadge.classList.add('hidden');
    }
  }

  // Poll unread count every 60 s
  function pollUnreadCount() {
    fetch('/notifications/unread-count')
      .then(r => r.json())
      .then(d => updateBellCount(d.count))
      .catch(() => {});
  }
  pollUnreadCount();
  setInterval(pollUnreadCount, 60000);

  // Toggle dropdown
  if (bellBtn && notifDropdown) {
    bellBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      notifDropdown.classList.toggle('open');
      // close user menu if open
      var ud = document.getElementById('user-dropdown');
      if (ud) ud.classList.remove('open');
    });
    document.addEventListener('click', function (e) {
      if (!notifDropdown.contains(e.target)) {
        notifDropdown.classList.remove('open');
      }
    });
  }

  // ── Mark notification read (delegated) ───────────────────
  document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-mark-read]');
    if (!el || e.target.closest('[data-delete-notif]')) return;
    const id  = el.dataset.markRead;
    const row = el.closest('.notif-item');
    post('/notifications/' + id + '/read').then(function (d) {
      if (d.ok && row) row.classList.remove('unread');
      pollUnreadCount();
    });
  });

  // ── Delete notification (delegated) ──────────────────────
  document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-delete-notif]');
    if (!el) return;
    e.stopPropagation();
    const id  = el.dataset.deleteNotif;
    const row = el.closest('.notif-item, tr');
    if (!confirm('Remove this notification?')) return;
    del('/notifications/' + id).then(function (d) {
      if (d.ok && row) row.remove();
      pollUnreadCount();
    });
  });

  // ── Update Alerts button ──────────────────
  const updateBtn  = document.getElementById('btn-update-alerts');
  const alertResult = document.getElementById('alert-result');

  if (updateBtn) {
    updateBtn.addEventListener('click', function () {
      updateBtn.classList.add('loading');
      updateBtn.textContent = 'Checking…';

      post('/update-alerts').then(function (data) {
        // Refresh the notifications list
        var body = document.getElementById('recent-alerts-body');
        if (body && data.html) body.innerHTML = data.html;

        // Refresh the unread stat card
        var statUnread = document.getElementById('stat-unread');
        if (statUnread) statUnread.textContent = data.unread_stat;

        if (alertResult) {
          alertResult.style.display = '';
          alertResult.className = 'alert alert-' + (data.created > 0 ? 'success' : 'info');
          alertResult.textContent = data.message;
        }
        updateBellCount(data.unread_count);
      }).catch(function (err) {
        if (alertResult) {
          alertResult.style.display = '';
          alertResult.className = 'alert alert-danger';
          alertResult.textContent = (err && err.message) ? err.message : 'Could not reach server. Please try again.';
        }
      }).finally(function () {
        updateBtn.classList.remove('loading');
        updateBtn.textContent = '🔔 Update Alerts';
      });
    });
  }

  // ── Copy phone to clipboard (delegated) ──────────────────
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;
    const text = btn.dataset.copy;
    if (!text) return;
    navigator.clipboard.writeText(text).then(function () {
      const orig = btn.textContent;
      btn.textContent = 'Copied!';
      setTimeout(() => { btn.textContent = orig; }, 1500);
    }).catch(function () {
      const ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      const orig = btn.textContent;
      btn.textContent = 'Copied!';
      setTimeout(() => { btn.textContent = orig; }, 1500);
    });
  });

  // ── Calendar day click → show events ─────
  document.querySelectorAll('.cal-cell[data-day]').forEach(function (cell) {
    cell.addEventListener('click', function () {
      const day    = cell.dataset.day;
      const events = cell.dataset.events ? JSON.parse(cell.dataset.events) : [];
      const modal  = document.getElementById('cal-modal');
      const body   = document.getElementById('cal-modal-body');
      const title  = document.getElementById('cal-modal-title');
      if (!modal) return;

      title.textContent = 'Events on ' + day;
      if (events.length === 0) {
        body.innerHTML = '<p class="text-muted">No events on this day.</p>';
      } else {
        body.innerHTML = events.map(function (ev) {
          return '<div class="notif-item">'
            + '<div class="notif-item-title">'
            +   '<span class="badge ' + escHtml(ev.badge || '') + '" style="margin-right:.4rem;">' + escHtml(ev.type) + '</span>'
            +   escHtml(ev.client_name)
            + '</div>'
            + (ev.label ? '<div class="notif-item-meta">' + escHtml(ev.label) + '</div>' : '')
            + '<div class="notif-actions mt-2">'
            + (ev.client_url ? '<a href="' + escHtml(ev.client_url) + '" class="btn btn-sm btn-primary">View Client</a>' : '')
            + (ev.phone ? '<a href="tel:' + escHtml(ev.phone) + '" class="btn btn-sm btn-success">Call</a>' : '')
            + (ev.phone ? '<a href="https://wa.me/' + escHtml(ev.phone.replace(/\D/g,'')) + '" target="_blank" class="btn btn-sm btn-primary">WhatsApp</a>' : '')
            + (ev.phone ? '<button class="btn btn-sm btn-secondary" data-copy="' + escHtml(ev.phone) + '">Copy #</button>' : '')
            + '</div></div>';
        }).join('');

        // Re-bind copy buttons inside modal
        body.querySelectorAll('[data-copy]').forEach(function (btn) {
          btn.addEventListener('click', function () {
            navigator.clipboard.writeText(btn.dataset.copy).then(function () {
              const orig = btn.textContent;
              btn.textContent = 'Copied!';
              setTimeout(() => { btn.textContent = orig; }, 1500);
            });
          });
        });
      }
      modal.style.display = 'flex';
    });
  });

  // Close modal
  const calModal = document.getElementById('cal-modal');
  if (calModal) {
    calModal.addEventListener('click', function (e) {
      if (e.target === calModal) calModal.style.display = 'none';
    });
    const closeBtn = document.getElementById('cal-modal-close');
    if (closeBtn) closeBtn.addEventListener('click', () => calModal.style.display = 'none');
  }

  // ── Interaction Modals ────────────────────

  function openModal(id) {
    var m = document.getElementById(id);
    if (m) m.classList.add('open');
  }

  function closeModal(id) {
    var m = document.getElementById(id);
    if (m) m.classList.remove('open');
  }

  // Close on overlay click or close button
  document.addEventListener('click', function (e) {
    var closeBtn = e.target.closest('[data-close-modal]');
    if (closeBtn) {
      closeModal(closeBtn.dataset.closeModal);
      return;
    }
    if (e.target.classList.contains('modal-overlay')) {
      e.target.classList.remove('open');
    }
  });

  // Close modals on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(function (m) {
        m.classList.remove('open');
      });
    }
  });

  // Open Log Interaction modal
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-open-log]');
    if (!btn) return;
    document.getElementById('log-client-id').value       = btn.dataset.clientId || '';
    document.getElementById('log-notification-id').value = btn.dataset.notificationId || '';
    document.getElementById('log-contacted-at').value    = new Date().toISOString().slice(0, 16);
    document.getElementById('log-notes').value           = '';
    document.getElementById('log-type').value            = 'call';
    document.getElementById('log-status').value          = 'reached_out';
    openModal('log-modal');
  });

  // Open Update Response modal
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-open-response]');
    if (!btn) return;
    var form = document.getElementById('response-form');
    form.action = '/interactions/' + btn.dataset.interactionId;
    document.getElementById('resp-status').value = btn.dataset.status || 'reached_out';
    document.getElementById('resp-notes').value  = btn.dataset.responseNotes || '';
    document.getElementById('resp-at').value     = btn.dataset.responseAt || '';
    openModal('response-modal');
  });

  // ── Dark mode toggle ─────────────────────
  var themeToggleBtn = document.getElementById('theme-toggle');
  var themeIcon      = document.getElementById('theme-icon');

  function applyTheme(t) {
    if (t === 'dark') {
      document.documentElement.setAttribute('data-theme', 'dark');
      if (themeIcon) themeIcon.textContent = '☀'; // ☀
    } else {
      document.documentElement.removeAttribute('data-theme');
      if (themeIcon) themeIcon.textContent = '☾'; // ☾
    }
  }

  applyTheme(localStorage.getItem('theme') || 'light');

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', function () {
      var current = document.documentElement.getAttribute('data-theme');
      var next = current === 'dark' ? 'light' : 'dark';
      try { localStorage.setItem('theme', next); } catch(e) {}
      applyTheme(next);
    });
  }

  // ── Auto-dismiss flash alerts ─────────────
  document.querySelectorAll('.alert[data-auto-dismiss]').forEach(function (el) {
    setTimeout(() => { el.style.display = 'none'; }, 4000);
  });

  // ── Confirm delete forms ──────────────────
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!confirm(form.dataset.confirm || 'Are you sure?')) {
        e.preventDefault();
      }
    });
  });

  function escHtml(str) {
    return String(str)
      .replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

});
