// ============================================
// GoalTicket — Service Worker
// ============================================
// This runs in the background and handles caching for offline support.

const CACHE_NAME = 'goalticket-v2';
const OFFLINE_URL = '/goalticket/offline.html';

// Files we want to cache immediately when the SW installs
const PRECACHE_ASSETS = [
    '/goalticket/offline.html',
    '/goalticket/manifest.json',
    '/goalticket/assets/css/style.css',
    '/goalticket/assets/css/admin.css',
    '/goalticket/assets/icons/icon-180.png',
    '/goalticket/assets/icons/icon-192.png',
    '/goalticket/assets/icons/icon-512.png'
];

// ============================================
// INSTALL — Triggered when the SW first registers
// ============================================
self.addEventListener('install', event => {
    console.log('[SW] Installing service worker...');

    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('[SW] Pre-caching essential assets');
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => {
            // Take control of pages immediately, no need to wait for reload
            return self.skipWaiting();
        })
    );
});

// ============================================
// ACTIVATE — Triggered after install, removes old caches
// ============================================
self.addEventListener('activate', event => {
    console.log('[SW] Activating service worker...');

    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => self.clients.claim())
    );
});

// ============================================
// FETCH — Intercepts every network request
// ============================================
self.addEventListener('fetch', event => {
    const request = event.request;
    const url = new URL(request.url);

    // Only handle requests within our own origin
    if (url.origin !== self.location.origin) return;

    // Skip non-GET requests (POST forms, etc.)
    if (request.method !== 'GET') return;

    // NEVER cache: admin pages, API, booking, login, sensitive data
    const noCachePatterns = [
        '/admin/',
        '/api/',
        '/book.php',
        '/login.php',
        '/register.php',
        '/logout.php',
        '/view_ticket.php',
        '/my_tickets.php'
    ];
    if (noCachePatterns.some(pattern => url.pathname.includes(pattern))) {
        // Network-only for sensitive pages, fall back to offline page
        event.respondWith(
            fetch(request).catch(() => caches.match(OFFLINE_URL))
        );
        return;
    }

    // Cache-first for static assets (CSS, JS, images, fonts, icons)
    const isStaticAsset = /\.(css|js|png|jpg|jpeg|svg|webp|woff2?|ttf|ico)$/i.test(url.pathname);
    if (isStaticAsset) {
        event.respondWith(
            caches.match(request).then(cached => {
                return cached || fetch(request).then(response => {
                    // Cache the response for next time
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });
                    return response;
                });
            })
        );
        return;
    }

        // Network-first for HTML pages (fixtures, home, etc.)
    event.respondWith(
        fetch(request)
            .then(response => {
                // If the server returned an error (404, 500), serve offline page
                if (!response.ok && (response.status === 404 || response.status >= 500)) {
                    return caches.match(OFFLINE_URL).then(offline => offline || response);
                }
                // Cache successful responses for offline access
                if (response.ok) {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(request, responseClone);
                    });
                }
                return response;
            })
            .catch(() => {
                // Network failed — try cache, then offline page
                return caches.match(request).then(cached => {
                    return cached || caches.match(OFFLINE_URL);
                });
            })
    );

});
