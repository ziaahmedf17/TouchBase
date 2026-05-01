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
    }).then(r => r.json());
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
    });
    document.addEventListener('click', function (e) {
      if (!notifDropdown.contains(e.target)) {
        notifDropdown.classList.remove('open');
      }
    });
  }

  // ── Mark notification read ────────────────
  document.querySelectorAll('[data-mark-read]').forEach(function (el) {
    el.addEventListener('click', function () {
      const id  = el.dataset.markRead;
      const row = el.closest('.notif-item');
      post('/notifications/' + id + '/read').then(function (d) {
        if (d.ok && row) {
          row.classList.remove('unread');
        }
        pollUnreadCount();
      });
    });
  });

  // ── Delete notification ───────────────────
  document.querySelectorAll('[data-delete-notif]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.stopPropagation();
      const id  = el.dataset.deleteNotif;
      const row = el.closest('.notif-item, tr');
      if (!confirm('Remove this notification?')) return;
      del('/notifications/' + id).then(function (d) {
        if (d.ok && row) row.remove();
        pollUnreadCount();
      });
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
        if (alertResult) {
          alertResult.style.display = 'block';
          alertResult.className = 'alert alert-' + (data.created > 0 ? 'success' : 'info');
          alertResult.textContent = data.message;
        }
        updateBellCount(data.unread_count);
      }).catch(function () {
        if (alertResult) {
          alertResult.style.display = 'block';
          alertResult.className = 'alert alert-danger';
          alertResult.textContent = 'Could not reach server. Please try again.';
        }
      }).finally(function () {
        updateBtn.classList.remove('loading');
        updateBtn.textContent = 'Update Alerts';
      });
    });
  }

  // ── Copy phone to clipboard ───────────────
  document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const text = btn.dataset.copy;
      if (!text) return;
      navigator.clipboard.writeText(text).then(function () {
        const orig = btn.textContent;
        btn.textContent = 'Copied!';
        setTimeout(() => { btn.textContent = orig; }, 1500);
      }).catch(function () {
        // Fallback for older browsers / non-HTTPS
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
            + '<div class="notif-item-title">' + escHtml(ev.client) + ' — ' + escHtml(ev.type) + '</div>'
            + '<div class="notif-item-meta">' + escHtml(ev.label || '') + '</div>'
            + '<div class="notif-actions mt-2">'
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
