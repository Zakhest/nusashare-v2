document.addEventListener('DOMContentLoaded', () => {
    const fallbackBaseUrl = window.nusaAppData ? window.nusaAppData.baseUrl : '/';
    const containers = Array.from(document.querySelectorAll('.notification-widget, #notification-dropdown-container'));

    if (!containers.length) return;

    let sharedNotifications = [];
    let sharedUnreadCount = 0;

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getWidget(container) {
        return {
            container,
            bell: container.querySelector('[data-notification-bell], #notification-bell'),
            dropdown: container.querySelector('[data-notification-dropdown], #notification-dropdown'),
            badge: container.querySelector('[data-notification-badge], #notification-badge'),
            list: container.querySelector('[data-notification-list], #notification-list'),
            markAll: container.querySelector('[data-mark-all-read], #mark-all-read'),
            isOpen: false,
        };
    }

    const widgets = containers.map(getWidget).filter(widget => widget.bell && widget.dropdown && widget.badge && widget.list);
    if (!widgets.length) return;

    function closeWidget(widget) {
        widget.isOpen = false;
        widget.dropdown.classList.add('opacity-0', 'scale-95');
        widget.dropdown.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => {
            if (!widget.isOpen) widget.dropdown.classList.add('hidden');
        }, 200);
    }

    function openWidget(widget) {
        widgets.forEach(other => {
            if (other !== widget) closeWidget(other);
        });

        widget.isOpen = true;
        widget.dropdown.classList.remove('hidden');
        setTimeout(() => {
            widget.dropdown.classList.remove('opacity-0', 'scale-95');
            widget.dropdown.classList.add('opacity-100', 'scale-100');
        }, 10);
        fetchNotifications();
    }

    function updateBadges(count) {
        widgets.forEach(widget => {
            if (count > 0) {
                widget.badge.classList.remove('hidden');
                widget.badge.classList.add('flex');
                widget.badge.textContent = count > 9 ? '9+' : count;
            } else {
                widget.badge.classList.add('hidden');
                widget.badge.classList.remove('flex');
                widget.badge.textContent = '';
            }
        });
    }

    function renderAll() {
        updateBadges(sharedUnreadCount);
        widgets.forEach(widget => renderNotifications(widget.list, sharedNotifications));
    }

    async function fetchNotifications() {
        try {
            const res = await fetch(`${fallbackBaseUrl}notifications/fetch?limit=10`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (data.status === 'success') {
                sharedUnreadCount = parseInt(data.data.unread_count || 0, 10);
                sharedNotifications = data.data.notifications || [];
                renderAll();
            }
        } catch (err) {
            console.error('Error fetching notifications:', err);
            widgets.forEach(widget => {
                widget.list.innerHTML = `<div class="p-6 text-center text-slate-400 text-sm">Gagal memuat notifikasi.</div>`;
            });
        }
    }

    function renderNotifications(listContainer, notifications) {
        if (!notifications || notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="px-6 py-8 text-center flex flex-col items-center">
                    <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">notifications_off</span>
                    <p class="text-slate-500 text-sm">Belum ada notifikasi baru.</p>
                </div>`;
            return;
        }

        listContainer.innerHTML = notifications.map(notif => {
            const isRead = parseInt(notif.is_read, 10) === 1;
            const bgClass = isRead ? 'bg-white' : 'bg-indigo-50/50';
            const iconStr = getIconForType(notif.type);
            const relativeTime = getRelativeTime(notif.created_at);
            const title = escapeHtml(notif.title);
            const message = notif.message ? `<br>${escapeHtml(notif.message)}` : '';
            const link = escapeHtml(notif.link || '#');

            return `
                <a href="${link}"
                   class="notification-item block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors ${bgClass}"
                   data-id="${escapeHtml(notif.id)}" data-read="${isRead ? 'true' : 'false'}">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center shrink-0 shadow-sm text-indigo-500">
                            <span class="material-symbols-outlined">${iconStr}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 leading-tight">
                                <span class="font-bold">${title}</span>
                                <span class="text-slate-600">${message}</span>
                            </p>
                            <span class="text-[10px] text-slate-400 font-medium mt-1 block">${relativeTime}</span>
                        </div>
                        ${!isRead ? '<div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 shrink-0"></div>' : ''}
                    </div>
                </a>
            `;
        }).join('');

        listContainer.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                if (item.dataset.read === 'false') {
                    fetch(`${fallbackBaseUrl}notifications/${item.dataset.id}/read`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).catch(err => console.error(err));
                }
            });
        });
    }

    async function markAllRead() {
        try {
            const res = await fetch(`${fallbackBaseUrl}notifications/read-all`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.status === 'success') {
                sharedUnreadCount = 0;
                updateBadges(0);
                fetchNotifications();
            }
        } catch (err) {
            console.error('Error marking all read:', err);
        }
    }

    function getIconForType(type) {
        switch(type) {
            case 'system': return 'info';
            case 'follow': return 'person_add';
            case 'like': return 'favorite';
            case 'comment': return 'forum';
            case 'reward': return 'stars';
            case 'purchase': return 'shopping_cart';
            default: return 'notifications';
        }
    }

    function getRelativeTime(dateStr) {
        if (!dateStr) return '';
        const date = new Date(String(dateStr).replace(' ', 'T'));
        const now = new Date();
        const diffSec = Math.floor((now - date) / 1000);

        if (diffSec < 60) return 'Baru saja';
        if (diffSec < 3600) return Math.floor(diffSec / 60) + 'm lalu';
        if (diffSec < 86400) return Math.floor(diffSec / 3600) + 'j lalu';
        return Math.floor(diffSec / 86400) + 'h lalu';
    }

    widgets.forEach(widget => {
        widget.bell.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            widget.isOpen ? closeWidget(widget) : openWidget(widget);
        });

        if (widget.markAll) {
            widget.markAll.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                markAllRead();
            });
        }
    });

    document.addEventListener('click', (e) => {
        widgets.forEach(widget => {
            if (widget.isOpen && !widget.dropdown.contains(e.target) && !widget.bell.contains(e.target)) {
                closeWidget(widget);
            }
        });
    });

    setInterval(fetchNotifications, 60000);
    fetchNotifications();
});
