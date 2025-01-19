// var staticCacheName = "pwa-v" + new Date().getTime();
// var filesToCache = [
//     '/css/app.css',
//     '/js/app.js',
//     '/images/icons/icon-72x72.png',
//     '/images/icons/icon-96x96.png',
//     '/images/icons/icon-128x128.png',
//     '/images/icons/icon-144x144.png',
//     '/images/icons/icon-152x152.png',
//     '/images/icons/icon-192x192.png',
//     '/images/icons/icon-512x512.png',
// ];

// // Cache on install
// self.addEventListener("install", event => {
//     this.skipWaiting();
//     event.waitUntil(
//         caches.open(staticCacheName)
//             .then(cache => {
//                 return cache.addAll(filesToCache);
//             })
//     )
// });

// // Clear cache on activate
// self.addEventListener('activate', event => {
//     event.waitUntil(
//         caches.keys().then(cacheNames => {
//             return Promise.all(
//                 cacheNames
//                     .filter(cacheName => (cacheName.startsWith("pwa-")))
//                     .filter(cacheName => (cacheName !== staticCacheName))
//                     .map(cacheName => caches.delete(cacheName))
//             );
//         })
//     );
// });

// // Serve from Cache
// self.addEventListener("fetch", event => {
//     // if (url.origin === 'http://192.168.100.19:5173') {
//     //     return;
//     // }

//     event.respondWith(
//         (async () => {
//             try {
//                 const response = await fetch(event.request);
//                 return response || caches.match(event.request);
//             } catch (error) {
//                 console.error('Fetch failed:', error);
//                 return caches.match(event.request);
//             }
//         })()
//     );
// });
