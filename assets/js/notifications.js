/**
 * notifications.js — NusaShare
 *
 * Fitur:
 * 1. Dropdown notifikasi (AJAX, buka via klik lonceng)
 * 2. Badge unread count real-time
 * 3. Toast pop-up otomatis saat notifikasi baru tiba
 * 4. Mark as read per item atau semua sekaligus
 * 5. Background Sync integration (dari Service Worker via pwa.js)
 *    → Saat SW selesai sync, event 'nusa:notif-sync' di-dispatch ke window
 *    → Polling 15s tetap aktif sebagai fallback jika BG Sync tidak tersedia
 */
document.addEventListener('DOMContentLoaded', () => {
    // ─── Base URL ───────────────────────────────────────────────
    const rawBase = window.nusaAppData?.baseUrl ?? '';
    const BASE = rawBase.replace(/\/+$/, '')
              || (window.location.origin + window.location.pathname.replace(/\/[^/]*$/, ''));

    // ─── State ──────────────────────────────────────────────────
    let sharedNotifications = [];
    let sharedUnreadCount   = 0;
    /** Set ID notifikasi yang sudah pernah ditampilkan toastnya di sesi ini */
    const seenIds = new Set();
    /** Apakah ini fetch pertama (jangan toast saat baru load halaman) */
    let isFirstFetch = true;
    /** Apakah Background Sync tersedia */
    const hasBgSync = 'serviceWorker' in navigator && 'SyncManager' in window;

    // ─── Widget containers ──────────────────────────────────────
    const containers = Array.from(
        document.querySelectorAll('.notification-widget, #notification-dropdown-container')
    );
    const widgets = containers
        .map(c => ({
            container: c,
            bell:    c.querySelector('[data-notification-bell], #notification-bell'),
            dropdown:c.querySelector('[data-notification-dropdown], #notification-dropdown'),
            badge:   c.querySelector('[data-notification-badge], #notification-badge'),
            list:    c.querySelector('[data-notification-list], #notification-list'),
            markAll: c.querySelector('[data-mark-all-read], #mark-all-read'),
            isOpen:  false,
        }))
        .filter(w => w.bell && w.dropdown && w.badge && w.list);

    // ─── Toast container (dibuat sekali, ditempel ke body) ──────
    let toastContainer = document.getElementById('nusa-toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'nusa-toast-container';
        toastContainer.style.cssText = [
            'position:fixed',
            'bottom:24px',
            'right:24px',
            'z-index:99999',
            'display:flex',
            'flex-direction:column-reverse',
            'gap:10px',
            'pointer-events:none',
            'max-width:360px',
            'width:calc(100vw - 48px)',
        ].join(';');
        document.body.appendChild(toastContainer);
    }

    // ════════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════════
    function escapeHtml(v) {
        return String(v ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function resolveLink(raw) {
        if (!raw) return '#';
        if (raw.startsWith('/') && !raw.startsWith('//')) {
            return BASE + raw;
        }
        return raw;
    }

    function getIcon(type) {
        const MAP = {
            follow:   'person_add',
            like:     'favorite',
            comment:  'forum',
            purchase: 'shopping_cart',
            unlock:   'lock_open',
            topup:    'account_balance_wallet',
            system:   'info',
            reward:   'stars',
        };
        return MAP[type] ?? 'notifications';
    }

    function getTypeColor(type) {
        const MAP = {
            follow:   '#4F46E5',
            like:     '#E11D48',
            comment:  '#0891B2',
            purchase: '#059669',
            unlock:   '#7C3AED',
            topup:    '#D97706',
            system:   '#64748B',
            reward:   '#F59E0B',
        };
        return MAP[type] ?? '#4F46E5';
    }

    function getRelativeTime(dateStr) {
        if (!dateStr) return '';
        const diff = Math.floor((Date.now() - new Date(String(dateStr).replace(' ', 'T'))) / 1000);
        if (diff < 60)    return 'Baru saja';
        if (diff < 3600)  return Math.floor(diff / 60) + 'm lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + 'j lalu';
        return Math.floor(diff / 86400) + 'h lalu';
    }

    // ════════════════════════════════════════════════════════════
    // BADGE
    // ════════════════════════════════════════════════════════════
    function updateBadges(count) {
        widgets.forEach(w => {
            if (count > 0) {
                w.badge.classList.remove('hidden');
                w.badge.classList.add('flex');
                w.badge.textContent = count > 9 ? '9+' : count;
            } else {
                w.badge.classList.add('hidden');
                w.badge.classList.remove('flex');
                w.badge.textContent = '';
            }
        });
    }

    // ════════════════════════════════════════════════════════════
    // TOAST
    // ════════════════════════════════════════════════════════════
    function showToast(notif) {
        const color  = getTypeColor(notif.type);
        const icon   = getIcon(notif.type);
        const link   = resolveLink(notif.link);
        const title  = escapeHtml(notif.title);
        const msg    = escapeHtml(notif.message ?? '');

        const toast = document.createElement('div');
        toast.style.cssText = [
            'pointer-events:auto',
            'background:#fff',
            'border-radius:16px',
            'box-shadow:0 8px 32px rgba(0,0,0,0.12),0 2px 8px rgba(0,0,0,0.08)',
            'overflow:hidden',
            'transform:translateX(120%)',
            'transition:transform 0.35s cubic-bezier(.4,0,.2,1), opacity 0.35s ease',
            'opacity:0',
            'border:1px solid #e2e8f0',
        ].join(';');

        toast.innerHTML = `
            <div style="height:3px;background:${color};border-radius:16px 16px 0 0"></div>
            <a href="${escapeHtml(link)}"
               style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;text-decoration:none;color:inherit;"
               onclick="markToastRead('${escapeHtml(String(notif.id))}')">
                <div style="
                    width:38px;height:38px;border-radius:10px;flex-shrink:0;
                    display:flex;align-items:center;justify-content:center;
                    background:${color}18;color:${color};
                ">
                    <span class="material-symbols-outlined" style="font-size:20px">${icon}</span>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#1e293b;margin:0 0 2px;">${title}</p>
                    ${msg ? `<p style="font-size:12px;color:#64748b;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${msg}</p>` : ''}
                    <p style="font-size:10px;color:#94a3b8;margin:4px 0 0;font-weight:600;">${getRelativeTime(notif.created_at)}</p>
                </div>
                <button type="button"
                    style="background:none;border:none;cursor:pointer;padding:0;margin-left:4px;color:#94a3b8;flex-shrink:0;line-height:1;"
                    onclick="event.preventDefault();event.stopPropagation();this.closest('[data-toast]').remove();">
                    <span class="material-symbols-outlined" style="font-size:16px">close</span>
                </button>
            </a>
        `;
        toast.setAttribute('data-toast', notif.id);

        toastContainer.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity   = '1';
            });
        });

        // Auto dismiss setelah 5 detik
        const timer = setTimeout(() => dismissToast(toast), 5000);

        // Pause dismiss saat hover
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', () => {
            setTimeout(() => dismissToast(toast), 2000);
        });
    }

    function dismissToast(toast) {
        if (!toast || !toast.parentNode) return;
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity   = '0';
        setTimeout(() => toast.remove(), 350);
    }

    // Dipanggil saat toast diklik (mark as read via AJAX)
    window.markToastRead = function(id) {
        if (!id) return;
        fetch(`${BASE}/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).catch(() => {});
    };

    // ════════════════════════════════════════════════════════════
    // FETCH & DETECT BARU
    // ════════════════════════════════════════════════════════════
    async function fetchNotifications(silent = false) {
        try {
            const res  = await fetch(`${BASE}/notifications/fetch?limit=20`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.status !== 'success') return;

            const newCount  = parseInt(data.data.unread_count ?? 0, 10);
            const newNotifs = data.data.notifications ?? [];

            // ── Deteksi notifikasi baru untuk toast ──────────────
            if (!isFirstFetch) {
                newNotifs.forEach(n => {
                    const id  = String(n.id);
                    const isUnread = parseInt(n.is_read, 10) === 0;
                    if (isUnread && !seenIds.has(id)) {
                        showToast(n);
                    }
                });
            }

            // Tandai semua yang ada sekarang sebagai "sudah dilihat"
            newNotifs.forEach(n => seenIds.add(String(n.id)));
            isFirstFetch = false;

            // ── Update state & render ─────────────────────────────
            sharedUnreadCount   = newCount;
            sharedNotifications = newNotifs;
            updateBadges(newCount);

            if (!silent) {
                widgets.forEach(w => renderList(w.list, newNotifs));
            }

        } catch (err) {
            // Jaringan error — diam saja, jangan spam console saat offline
            if (!silent) console.warn('[Notif] fetch error:', err);
        }
    }

    // ════════════════════════════════════════════════════════════
    // RENDER LIST (dropdown)
    // ════════════════════════════════════════════════════════════
    function renderList(listEl, notifications) {
        if (!notifications || notifications.length === 0) {
            listEl.innerHTML = `
                <div class="px-6 py-8 text-center flex flex-col items-center">
                    <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">notifications_off</span>
                    <p class="text-slate-500 text-sm">Belum ada notifikasi.</p>
                </div>`;
            return;
        }

        listEl.innerHTML = notifications.map(notif => {
            const isRead = parseInt(notif.is_read, 10) === 1;
            const bg     = isRead ? 'bg-white' : 'bg-indigo-50/50';
            const icon   = getIcon(notif.type);
            const color  = getTypeColor(notif.type);
            const link   = escapeHtml(resolveLink(notif.link));
            const title  = escapeHtml(notif.title);
            const msg    = notif.message ? `<span class="text-slate-500"> — ${escapeHtml(notif.message)}</span>` : '';
            const time   = getRelativeTime(notif.created_at);

            return `
                <a href="${link}"
                   class="notification-item block px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition-colors ${bg}"
                   data-id="${escapeHtml(String(notif.id))}" data-read="${isRead ? 'true' : 'false'}">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                             style="background:${color}18;color:${color}">
                            <span class="material-symbols-outlined" style="font-size:18px">${icon}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 leading-snug font-semibold">${title}${msg}</p>
                            <span class="text-[10px] text-slate-400 font-medium mt-0.5 block">${time}</span>
                        </div>
                        ${!isRead ? '<div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>' : ''}
                    </div>
                </a>`;
        }).join('');

        // Mark as read saat diklik
        listEl.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                if (item.dataset.read === 'false') {
                    const id = item.dataset.id;
                    fetch(`${BASE}/notifications/${id}/read`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    }).then(() => {
                        item.dataset.read = 'true';
                        item.classList.remove('bg-indigo-50/50');
                        item.classList.add('bg-white');
                        const dot = item.querySelector('.w-2.h-2.rounded-full.bg-indigo-500');
                        if (dot) dot.remove();
                        sharedUnreadCount = Math.max(0, sharedUnreadCount - 1);
                        updateBadges(sharedUnreadCount);
                    }).catch(() => {});
                }
            });
        });
    }

    // ════════════════════════════════════════════════════════════
    // MARK ALL READ
    // ════════════════════════════════════════════════════════════
    async function markAllRead() {
        try {
            const res  = await fetch(`${BASE}/notifications/read-all`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await res.json();
            if (data.status === 'success') {
                sharedUnreadCount = 0;
                updateBadges(0);
                sharedNotifications = sharedNotifications.map(n => ({ ...n, is_read: 1 }));
                widgets.forEach(w => renderList(w.list, sharedNotifications));
            }
        } catch (err) {
            console.error('[Notif] markAllRead error:', err);
        }
    }

    // ════════════════════════════════════════════════════════════
    // DROPDOWN OPEN / CLOSE
    // ════════════════════════════════════════════════════════════
    function closeWidget(w) {
        w.isOpen = false;
        w.dropdown.classList.add('opacity-0', 'scale-95');
        w.dropdown.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => { if (!w.isOpen) w.dropdown.classList.add('hidden'); }, 200);
    }

    function openWidget(w) {
        widgets.forEach(other => { if (other !== w) closeWidget(other); });
        w.isOpen = true;
        w.dropdown.classList.remove('hidden');
        // Render segera pakai data yang sudah ada, lalu fetch terbaru
        renderList(w.list, sharedNotifications);
        setTimeout(() => {
            w.dropdown.classList.remove('opacity-0', 'scale-95');
            w.dropdown.classList.add('opacity-100', 'scale-100');
        }, 10);
        // Fetch fresh tanpa silent (update list juga)
        fetchNotifications(false);
    }

    // ════════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ════════════════════════════════════════════════════════════
    widgets.forEach(w => {
        w.bell.addEventListener('click', e => {
            e.preventDefault();
            e.stopPropagation();
            w.isOpen ? closeWidget(w) : openWidget(w);
        });

        if (w.markAll) {
            w.markAll.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                markAllRead();
            });
        }
    });

    document.addEventListener('click', e => {
        widgets.forEach(w => {
            if (w.isOpen && !w.dropdown.contains(e.target) && !w.bell.contains(e.target)) {
                closeWidget(w);
            }
        });
    });

    // ════════════════════════════════════════════════════════════
    // POLLING & BACKGROUND SYNC
    // ════════════════════════════════════════════════════════════

    // ── Background Sync listener: terima data dari Service Worker ──
    // pwa.js mem-broadcast event ini saat SW selesai sync notifikasi
    window.addEventListener('nusa:notif-sync', (e) => {
        const data = e.detail;
        if (!data) return;

        const newCount  = parseInt(data.unread_count ?? 0, 10);
        const newNotifs = data.notifications ?? [];

        // Deteksi notif baru untuk toast
        if (!isFirstFetch) {
            newNotifs.forEach(n => {
                const id = String(n.id);
                if (parseInt(n.is_read, 10) === 0 && !seenIds.has(id)) {
                    showToast(n);
                }
            });
        }

        newNotifs.forEach(n => seenIds.add(String(n.id)));
        isFirstFetch = false;
        sharedUnreadCount   = newCount;
        sharedNotifications = newNotifs;
        updateBadges(newCount);
    });

    // ── Fetch pertama saat halaman load ──────────────────────────
    fetchNotifications(false);

    // ── Polling fallback: aktif jika Background Sync tidak tersedia ──
    // Jika BG Sync tersedia, polling tetap berjalan tapi lebih jarang (60s)
    // agar data tetap fresh saat tab aktif
    const pollInterval = hasBgSync ? 60000 : 15000;
    setInterval(() => fetchNotifications(true), pollInterval);

    // ── Trigger Background Sync setiap kali halaman mendapat fokus ──
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && 'serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(reg => {
                if ('SyncManager' in window) {
                    reg.sync.register('sync-notifications').catch(() => {});
                }
            });
        }
    });
});
