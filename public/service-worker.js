// public/service-worker.js
self.addEventListener('install', (e) => {
  e.waitUntil(
    caches.open('idosoms-v1').then(cache => cache.addAll([
      '/', '/dashboard.php', '/assets/css/styles.css', '/assets/js/main.js'
    ]))
  );
});

self.addEventListener('fetch', (e) => {
  e.respondWith(
    caches.match(e.request).then(resp => resp || fetch(e.request))
  );
});
