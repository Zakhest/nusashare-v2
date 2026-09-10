/**
 * pwa.js â€” NusaShare PWA Manager
 *
 * 1. Service Worker registration
 * 2. Install banner (beforeinstallprompt)
 * 3. Background Sync â€” trigger sync notifikasi
 * 4. IndexedDB reader â€” baca notif hasil sync, kirim ke notifications.js via event
 * 5. Online/offline status indicator
 */

(function () {
    'use strict';

    const SW_PATH = '/nusa/sw.js';
    const SYNC_TAG   = 'sync-notifications';
    const DB_NAME    = 'nusaNotifDB';
    const DB_VERSION = 1;
    const STORE_NAME = 'notifications';

    // â”€â”€â”€ 1. SERVICE WORKER REGISTRATION â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register(SW_PATH, { scope: '/nusa/' });
                console.log('[PWA] Service Worker registered:', reg.scope);

                // â”€â”€ Background Sync: daftarkan sync saat halaman load â”€â”€â”€â”€â”€â”€
                if ('SyncManager' in window) {
                    try {
                        await reg.sync.register(SYNC_TAG);
                        console.log('[PWA] Background Sync registered');
                    } catch (e) {
                        console.warn('[PWA] Background Sync not available, fallback to polling');
                    }
                }

                // â”€â”€ Update SW saat ada versi baru â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showUpdateToast();
                        }
                    });
                });

            } catch (err) {
                console.error('[PWA] SW registration failed:', err);
            }
        });

        // â”€â”€ Terima pesan dari SW (Background Sync selesai) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        navigator.serviceWorker.addEventListener('message', event => {
            if (event.data?.type === 'NOTIF_SYNC_DONE') {
                // Dispatch custom event agar notifications.js bisa pick up
                window.dispatchEvent(new CustomEvent('nusa:notif-sync', {
                    detail: event.data.data
                }));
            }
        });
    }

    // â”€â”€â”€ 2. INSTALL BANNER â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    let deferredPrompt = null;

    window.addEventListener('beforeinstallprompt', e => {
        e.preventDefault();
        deferredPrompt = e;

        // Tampilkan banner hanya jika belum pernah ditolak
        const dismissed = localStorage.getItem('nusa_pwa_dismissed');
        if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) return;

        setTimeout(() => showInstallBanner(), 3000); // delay 3s biar halaman load dulu
    });

    function showInstallBanner() {
        if (document.getElementById('nusa-install-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'nusa-install-banner';
        banner.innerHTML = `
            <!-- Backdrop -->
            <div id="nusa-install-backdrop" style="
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.4);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                z-index: 99997;
                opacity: 0;
                transition: opacity 0.4s ease;
            "></div>
            <!-- Modal -->
            <div style="
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -40%);
                z-index: 99998;
                background: rgba(255,255,255,0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(79,70,229,0.15);
                border-radius: 24px;
                box-shadow: 0 25px 50px -12px rgba(79,70,229,0.25), 0 0 0 1px rgba(255,255,255,0.5) inset;
                padding: 32px 24px;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 16px;
                width: calc(100vw - 48px);
                max-width: 400px;
                transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease;
                opacity: 0;
                font-family: 'Inter', sans-serif;
                text-align: center;
            " id="nusa-install-banner-inner">
                <img src="/nusa/assets/pwa/icon-128.png"
                     style="width:80px;height:80px;border-radius:20px;flex-shrink:0;box-shadow:0 10px 25px rgba(79,70,229,0.3);margin-bottom:8px;"
                     alt="NusaShare">
                <div>
                    <h3 style="font-size:20px;font-weight:700;color:#0F172A;margin:0 0 8px">Install NusaShare</h3>
                    <p style="font-size:14px;color:#475569;margin:0;line-height:1.5">
                        Dapatkan pengalaman lebih cepat dan akses langsung dari Home Screen kamu seperti aplikasi native!
                    </p>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;width:100%;margin-top:12px;">
                    <button id="nusa-pwa-install-btn" style="
                        background: linear-gradient(135deg,#4F46E5,#22D3EE);
                        color: #fff;
                        border: none;
                        border-radius: 12px;
                        padding: 14px;
                        font-size: 15px;
                        font-weight: 600;
                        cursor: pointer;
                        font-family: inherit;
                        width: 100%;
                        box-shadow: 0 8px 20px rgba(79,70,229,0.3);
                        transition: transform 0.2s, box-shadow 0.2s;
                    ">Install Sekarang</button>
                    <button id="nusa-pwa-dismiss-btn" style="
                        background: rgba(79,70,229,0.05);
                        border: 1px solid rgba(79,70,229,0.1);
                        color: #4F46E5;
                        border-radius: 12px;
                        padding: 12px;
                        font-size: 14px;
                        font-weight: 600;
                        cursor: pointer;
                        font-family: inherit;
                        width: 100%;
                        transition: background 0.2s;
                    ">Mungkin Nanti</button>
                </div>
            </div>
        `;

        document.body.appendChild(banner);

        // Animate in
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                const backdrop = document.getElementById('nusa-install-backdrop');
                const inner = document.getElementById('nusa-install-banner-inner');
                if (backdrop) backdrop.style.opacity = '1';
                if (inner) {
                    inner.style.transform = 'translate(-50%, -50%)';
                    inner.style.opacity   = '1';
                }
            });
        });

        document.getElementById('nusa-pwa-install-btn').addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            console.log('[PWA] Install outcome:', outcome);
            deferredPrompt = null;
            dismissBanner();
        });

        document.getElementById('nusa-pwa-dismiss-btn').addEventListener('click', () => {
            localStorage.setItem('nusa_pwa_dismissed', Date.now());
            dismissBanner();
        });
    }

    function dismissBanner() {
        const backdrop = document.getElementById('nusa-install-backdrop');
        const inner = document.getElementById('nusa-install-banner-inner');
        if (backdrop) backdrop.style.opacity = '0';
        if (inner) {
            inner.style.transform = 'translate(-50%, -40%)';
            inner.style.opacity   = '0';
        }
        setTimeout(() => document.getElementById('nusa-install-banner')?.remove(), 400);
    }

    // â”€â”€â”€ 3. UPDATE TOAST â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function showUpdateToast() {
        const toast = document.createElement('div');
        toast.innerHTML = `
            <div style="
                position:fixed;top:24px;right:24px;z-index:99999;
                background:rgba(255,255,255,0.95);backdrop-filter:blur(16px);
                border:1px solid rgba(79,70,229,0.15);border-radius:16px;
                box-shadow:0 8px 32px rgba(79,70,229,0.15);
                padding:14px 18px;display:flex;align-items:center;gap:12px;
                font-family:'Inter',sans-serif;max-width:320px;
                animation: slideDown 0.4s cubic-bezier(0.34,1.56,0.64,1) forwards;
            ">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#4F46E5,#22D3EE);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M1 4V10H7" stroke="white" stroke-width="2" stroke-linecap="round"/><path d="M3.51 15A9 9 0 1 0 5.64 5.64L1 10" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                </div>
                <div style="flex:1">
                    <p style="font-size:13px;font-weight:700;color:#0F172A;margin:0 0 1px">Update tersedia</p>
                    <p style="font-size:12px;color:#64748b;margin:0">NusaShare baru saja diperbarui.</p>
                </div>
                <button onclick="window.location.reload()" style="background:linear-gradient(135deg,#4F46E5,#22D3EE);color:#fff;border:none;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap">Refresh</button>
            </div>
        `;
        document.body.appendChild(toast);

        // Auto dismiss setelah 10 detik
        setTimeout(() => toast.remove(), 10000);
    }

    // â”€â”€â”€ 4. ONLINE/OFFLINE INDICATOR â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    function showConnectionToast(isOnline) {
        const existing = document.getElementById('nusa-conn-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'nusa-conn-toast';

        const color = isOnline ? '#059669' : '#DC2626';
        const bg    = isOnline ? '#ECFDF5'  : '#FEF2F2';
        const icon  = isOnline
            ? `<path d="M5 12.55a10.94 10.94 0 0 1 14.08 0" stroke="${color}" stroke-width="2" stroke-linecap="round"/><path d="M1.42 9a15.91 15.91 0 0 1 21.16 0" stroke="${color}" stroke-width="2" stroke-linecap="round"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0" stroke="${color}" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="20" r="1" fill="${color}"/>`
            : `<path d="M1 1L23 23" stroke="${color}" stroke-width="2" stroke-linecap="round"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55" stroke="${color}" stroke-width="2" stroke-linecap="round"/><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39" stroke="${color}" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="20" r="1" fill="${color}"/>`;
        const text = isOnline ? 'Koneksi kembali' : 'Tidak ada koneksi';

        toast.innerHTML = `
            <div style="
                position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);
                z-index:99999;background:${bg};border:1px solid ${color}30;border-radius:999px;
                box-shadow:0 4px 20px ${color}20;padding:10px 20px;
                display:flex;align-items:center;gap:8px;font-family:'Inter',sans-serif;
                transition:transform 0.35s cubic-bezier(0.34,1.56,0.64,1),opacity 0.35s ease;opacity:0;
            " id="nusa-conn-inner">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">${icon}</svg>
                <span style="font-size:13px;font-weight:600;color:${color}">${text}</span>
            </div>
        `;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                const inner = document.getElementById('nusa-conn-inner');
                inner.style.transform = 'translateX(-50%) translateY(0)';
                inner.style.opacity   = '1';
            });
        });

        // Auto dismiss
        const delay = isOnline ? 2500 : 0; // offline â€” tetap tampil sampai online lagi
        if (isOnline) setTimeout(() => toast.remove(), delay);
    }

    window.addEventListener('online',  () => showConnectionToast(true));
    window.addEventListener('offline', () => showConnectionToast(false));

    // Tampilkan saat pertama kali offline
    if (!navigator.onLine) showConnectionToast(false);

})();
