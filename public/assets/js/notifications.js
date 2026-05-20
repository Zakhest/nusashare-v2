document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if the notification container exists
    const bellBtn = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    const listContainer = document.getElementById('notification-list');
    const markAllBtn = document.getElementById('mark-all-read');

    if (!bellBtn || !dropdown) return;

    let isDropdownOpen = false;
    let fallbackBaseUrl = window.nusaAppData ? window.nusaAppData.baseUrl : '/';

    // Toggle dropdown
    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        isDropdownOpen = !isDropdownOpen;
        
        if (isDropdownOpen) {
            dropdown.classList.remove('hidden');
            setTimeout(() => {
                dropdown.classList.remove('opacity-0', 'scale-95');
                dropdown.classList.add('opacity-100', 'scale-100');
            }, 10);
            fetchNotifications(); // Fetch latest when opened
        } else {
            closeDropdown();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (isDropdownOpen && !dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
            closeDropdown();
        }
    });

    function closeDropdown() {
        isDropdownOpen = false;
        dropdown.classList.add('opacity-0', 'scale-95');
        dropdown.classList.remove('opacity-100', 'scale-100');
        setTimeout(() => {
            dropdown.classList.add('hidden');
        }, 200);
    }

    // Mark all as read
    if (markAllBtn) {
        markAllBtn.addEventListener('click', async (e) => {
            e.stopPropagation();
            try {
                const res = await fetch(`${fallbackBaseUrl}notifications/read-all`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (data.status === 'success') {
                    updateBadge(0);
                    fetchNotifications(); // Refresh list to show all as read
                }
            } catch (err) {
                console.error('Error marking all read:', err);
            }
        });
    }

    // Fetch Notifications
    async function fetchNotifications() {
        try {
            const res = await fetch(`${fallbackBaseUrl}notifications/fetch?limit=10`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            
            if (data.status === 'success') {
                updateBadge(data.data.unread_count);
                renderNotifications(data.data.notifications);
            }
        } catch (err) {
            console.error('Error fetching notifications:', err);
            listContainer.innerHTML = `<div class="p-6 text-center text-slate-400 text-sm">Gagal memuat notifikasi.</div>`;
        }
    }

    // Render standard notifications format
    function renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            listContainer.innerHTML = `
                <div class="px-6 py-8 text-center flex flex-col items-center">
                    <span class="material-symbols-outlined text-4xl text-slate-200 mb-2">notifications_off</span>
                    <p class="text-slate-500 text-sm">Belum ada notifikasi baru.</p>
                </div>`;
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const isRead = parseInt(notif.is_read) === 1;
            const bgClass = isRead ? 'bg-white' : 'bg-indigo-50/50';
            const iconStr = getIconForType(notif.type);
            const relativeTime = getRelativeTime(notif.created_at);

            html += `
                <a href="${notif.link ? notif.link : '#'}" 
                   class="notification-item block p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors ${bgClass}" 
                   data-id="${notif.id}" data-read="${isRead ? 'true' : 'false'}">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-full bg-white border border-slate-100 flex items-center justify-center shrink-0 shadow-sm text-indigo-500">
                            <span class="material-symbols-outlined">${iconStr}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-800 leading-tight">
                                <span class="font-bold">${notif.title}</span> 
                                <span class="text-slate-600">${notif.message ? '<br>'+notif.message : ''}</span>
                            </p>
                            <span class="text-[10px] text-slate-400 font-medium mt-1 block">${relativeTime}</span>
                        </div>
                        ${!isRead ? '<div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 shrink-0"></div>' : ''}
                    </div>
                </a>
            `;
        });

        listContainer.innerHTML = html;

        // Attach click listener for individual items to mark as read
        const items = listContainer.querySelectorAll('.notification-item');
        items.forEach(item => {
            item.addEventListener('click', async (e) => {
                if (item.dataset.read === 'false') {
                    // Make request to mark as read, but let default navigation happen
                    const id = item.dataset.id;
                    fetch(`${fallbackBaseUrl}notifications/${id}/read`, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).catch(err => console.error(err));
                }
            });
        });
    }

    function updateBadge(count) {
        if (count > 0) {
            badge.classList.remove('hidden');
            badge.textContent = count > 9 ? '9+' : count;
        } else {
            badge.classList.add('hidden');
            badge.textContent = '';
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
        const date = new Date(dateStr.replace(' ', 'T'));
        const now = new Date();
        const diffMs = now - date;
        const diffSec = Math.floor(diffMs / 1000);
        
        if (diffSec < 60) return 'Baru saja';
        if (diffSec < 3600) return Math.floor(diffSec / 60) + 'm lalu';
        if (diffSec < 86400) return Math.floor(diffSec / 3600) + 'j lalu';
        return Math.floor(diffSec / 86400) + 'h lalu';
    }

    // Poll occasionally (e.g. every 60 seconds)
    setInterval(fetchNotifications, 60000);
    // Initial fetch
    fetchNotifications();
});
