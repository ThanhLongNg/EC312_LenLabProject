// Service Worker for LENLAB
// This service worker handles caching while avoiding POST request caching issues

const CACHE_NAME = 'lenlab-cache-v1';
const urlsToCache = [
  '/',
  // Add other static assets as needed
];

// Install event - cache static assets
self.addEventListener('install', function(event) {
  console.log('Service Worker installing...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Opened cache');
        return cache.addAll(urlsToCache);
      })
  );
  // Force the waiting service worker to become the active service worker
  self.skipWaiting();
});

// Fetch event - handle requests
self.addEventListener('fetch', function(event) {
  // IMPORTANT: Skip all non-GET requests to avoid the cache.put error
  if (event.request.method !== 'GET') {
    console.log('Skipping non-GET request:', event.request.method, event.request.url);
    return;
  }
  
  // Skip caching for API endpoints that should always be fresh
  if (event.request.url.includes('/api/') || 
      event.request.url.includes('/admin/') ||
      event.request.url.includes('csrf-token') ||
      event.request.url.includes('_token')) {
    console.log('Skipping API/admin request:', event.request.url);
    return;
  }

  // Only cache static assets and pages
  if (event.request.url.match(/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/)) {
    event.respondWith(
      caches.match(event.request)
        .then(function(response) {
          // Return cached version or fetch from network
          if (response) {
            console.log('Serving from cache:', event.request.url);
            return response;
          }
          console.log('Fetching from network:', event.request.url);
          return fetch(event.request).then(function(response) {
            // Check if we received a valid response
            if (!response || response.status !== 200 || response.type !== 'basic') {
              return response;
            }

            // Clone the response
            var responseToCache = response.clone();

            caches.open(CACHE_NAME)
              .then(function(cache) {
                cache.put(event.request, responseToCache);
              });

            return response;
          });
        }
      )
    );
  }
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
  console.log('Service Worker activating...');
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  // Ensure the new service worker takes control immediately
  return self.clients.claim();
});