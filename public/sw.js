// Service Worker for SmashZone PWA (network-first approach)
const STATIC_CACHE = 'smashzone-static-v2';
const RUNTIME_CACHE = 'smashzone-runtime-v2';

// Only precache truly static assets that rarely change
const PRECACHE_URLS = [
  '/',
  '/manifest.json'
];

// Install event – precache essential assets and activate immediately
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => cache.addAll(PRECACHE_URLS))
  );
  self.skipWaiting();
});

// Activate event – clean up old caches and take control of existing clients
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) =>
      Promise.all(
        cacheNames
          .filter((name) => ![STATIC_CACHE, RUNTIME_CACHE].includes(name))
          .map((name) => caches.delete(name))
      )
    )
  );
  self.clients.claim();
});

// Fetch event – network first, fallback to cache when offline
self.addEventListener('fetch', (event) => {
  const { request } = event;

  // Only handle GET requests for same-origin resources
  if (request.method !== 'GET') {
    return;
  }

  const requestUrl = new URL(request.url);
  if (requestUrl.origin !== self.location.origin) {
    return;
  }

  // Serve precached assets directly from cache
  if (PRECACHE_URLS.includes(requestUrl.pathname)) {
    event.respondWith(caches.match(request).then((response) => response || fetch(request)));
    return;
  }

  // Network first for everything else, fallback to cache if offline
  event.respondWith(
    fetch(request)
      .then((response) => {
        // Cache successful responses for later use
        if (response && response.status === 200 && response.type === 'basic') {
          const clonedResponse = response.clone();
          caches.open(RUNTIME_CACHE).then((cache) => cache.put(request, clonedResponse));
        }
        return response;
      })
      .catch(() => caches.match(request))
  );
});
