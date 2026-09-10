/**
 * sw.js â€” NusaShare Service Worker
 * Fitur:
 *  1. Caching aset statis (Cache-First)
 *  2. Caching halaman dinamis (Network-First)
 *  3. Offline fallback ke /offline
 *  4. Background Sync untuk notifikasi (hemat baterai vs polling)
 *  5. IndexedDB untuk simpan notifikasi saat background sync
 */

const CACHE_VERSION = 'v1';
const CACHE_STATIC = `nusa-static-${CACHE_VERSION}`;
const CACHE_DYNAMIC = `nusa-dynamic-${CACHE_VERSION}`;
const CACHE_IMAGES = `nusa-images-${CACHE_VERSION}`;

const NOTIF_SYNC_TAG = 'sync-notifications';
const DB_NAME = 'nusaNotifDB';
const DB_VERSION = 1;
const STORE_NOTIF = 'notifications';

// Aset yang di-precache saat install
const STATIC_ASSETS = [
    '/nusa/',
    '/nusa/offline',
    '/nusa/manifest.json',
    '/nusa/assets/js/notifications.js',
    '/nusa/assets/js/main.js',
];

// Pola URL yang selalu Network-First (jangan cache)
const NETWORK_ONLY_PATTERNS = [
    /\/api\//,
    /\/notifications\//,
    /\/cart\//,
    /\/topup\//,
    /\/logout/,
];

// Pola URL yang di-cache sebagai aset statis (CDN)
const STATIC_CDN_PATTERNS = [
    /fonts\.googleapis\.com/,
    /fonts\.gstatic\.com/,
    /cdn\.tailwindcss\.com/,
];

// â”€â”€â”€ INSTALL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_STATIC).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(err => {
                // Abaikan error partial (halaman dinamis mungkin butuh auth)
                console.warn('[SW] Precache partial error (ok):', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// â”€â”€â”€ ACTIVATE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(k => k !== CACHE_STATIC && k !== CACHE_DYNAMIC && k !== CACHE_IMAGES)
                    .map(k => caches.delete(k))
            );
        }).then(() => self.clients.claim())
    );
});

// â”€â”€â”€ FETCH â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Abaikan non-GET, chrome-extension, dan socket
    if (request.method !== 'GET') return;
    if (!url.protocol.startsWith('http')) return;

    // Network-only untuk API & aksi
    if (NETWORK_ONLY_PATTERNS.some(p => p.test(url.pathname))) {
        return event.respondWith(fetch(request).catch(() => new Response('', { status: 503 })));
    }

    // CDN fonts & scripts â†’ Cache-First
    if (STATIC_CDN_PATTERNS.some(p => p.test(url.hostname))) {
        return event.respondWith(cacheFirst(request, CACHE_STATIC));
    }

    // Aset statis lokal (CSS, JS, gambar statis, icon)
    if (url.pathname.startsWith('assets/') || url.pathname === 'manifest.json') {
        return event.respondWith(cacheFirst(request, CACHE_STATIC));
    }

    // Gambar konten dinamis â†’ Stale-While-Revalidate
    if (url.pathname.startsWith('/image/')) {
        return event.respondWith(staleWhileRevalidate(request, CACHE_IMAGES));
    }

    // Semua halaman HTML â†’ Network-First dengan offline fallback
    if (request.headers.get('Accept')?.includes('text/html')) {
        return event.respondWith(networkFirstWithOffline(request));
    }

    // Default: Network-First
    event.respondWith(networkFirst(request, CACHE_DYNAMIC));
});

// â”€â”€â”€ STRATEGI CACHING â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

/** Cache-First: ambil dari cache, fallback ke network */
async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;
    const response = await fetch(request);
    if (response.ok) {
        const cache = await caches.open(cacheName);
        cache.put(request, response.clone());
    }
    return response;
}

/** Network-First: ambil dari network, fallback ke cache */
async function networkFirst(request, cacheName) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return caches.match(request) || new Response('Offline', { status: 503 });
    }
}

/** Network-First untuk HTML, fallback ke /offline */
async function networkFirstWithOffline(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(CACHE_DYNAMIC);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        if (cached) return cached;
        return caches.match('/nusa/offline') || new Response('<h1>Offline</h1>', {
            status: 503,
            headers: { 'Content-Type': 'text/html' },
        });
    }
}

/** Stale-While-Revalidate: tampilkan cache, update di background */
async function staleWhileRevalidate(request, cacheName) {
    const cache = await caches.open(cacheName);
    const cached = await cache.match(request);

    const fetchPromise = fetch(request).then(response => {
        if (response.ok) cache.put(request, response.clone());
        return response;
    }).catch(() => null);

    return cached || fetchPromise;
}

// â”€â”€â”€ BACKGROUND SYNC â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

self.addEventListener('sync', event => {
    if (event.tag === NOTIF_SYNC_TAG) {
        event.waitUntil(syncNotifications());
    }
});

/** Fetch notifikasi terbaru, simpan ke IndexedDB */
async function syncNotifications() {
    try {
        // Cari base URL dari clients yang aktif
        const clients = await self.clients.matchAll({ type: 'window' });
        const baseUrl = clients.length > 0
            ? new URL(clients[0].url).origin
            : self.location.origin;

        const res = await fetch(`${baseUrl}/notifications/fetch?limit=20`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'include',
        });
        if (!res.ok) return;

        const data = await res.json();
        if (data.status !== 'success') return;

        // Simpan ke IndexedDB
        await saveNotificationsToIDB(data.data);

        // Broadcast ke semua tab yang terbuka
        clients.forEach(client => {
            client.postMessage({
                type: 'NOTIF_SYNC_DONE',
                data: data.data,
            });
        });
    } catch (err) {
        console.warn('[SW] syncNotifications error:', err);
        throw err; // Re-throw agar browser retry
    }
}

// â”€â”€â”€ INDEXEDDB HELPERS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

function openIDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VERSION);
        req.onupgradeneeded = e => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains(STORE_NOTIF)) {
                db.createObjectStore(STORE_NOTIF, { keyPath: 'key' });
            }
        };
        req.onsuccess = e => resolve(e.target.result);
        req.onerror = e => reject(e.target.error);
    });
}

async function saveNotificationsToIDB(data) {
    const db = await openIDB();
    const tx = db.transaction(STORE_NOTIF, 'readwrite');
    const store = tx.objectStore(STORE_NOTIF);
    store.put({ key: 'latest', ...data, savedAt: Date.now() });
    return new Promise((resolve, reject) => {
        tx.oncomplete = resolve;
        tx.onerror = () => reject(tx.error);
    });
}

// â”€â”€â”€ PUSH (placeholder untuk future Web Push) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
self.addEventListener('push', event => {
    if (!event.data) return;
    const payload = event.data.json();
    event.waitUntil(
        self.registration.showNotification(payload.title || 'NusaShare', {
            body: payload.body || '',
            icon: 'assets/icon/logonus.png',
            badge: 'assets/icon/logonus.png',
            data: { url: payload.url || '/nusa/' },
            vibrate: [100, 50, 100],
            tag: payload.tag || 'nusa-notif',
            renotify: true,
        })
    );
});

self.addEventListener('notificationclick', event => {
    event.notification.close();
    const url = event.notification.data?.url || '/nusa/';
    event.waitUntil(
        self.clients.matchAll({ type: 'window' }).then(clients => {
            const existing = clients.find(c => c.url === url && 'focus' in c);
            if (existing) return existing.focus();
            return self.clients.openWindow(url);
        })
    );
});
