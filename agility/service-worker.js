/**
 * Created by jantonio on 15/06/17.
 */
console.log("SW Startup!");
var CACHE_NAME = 'my-cache';
var urlsToCache = [
    '/agility/images/logos/agilitycontest.png'
];

// trick to activate sw without reloading page
// From: https://davidwalsh.name/service-worker-claim

// Install event - cache files (...or not)
// Be sure to call skipWaiting()!
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            // Important to `return` the promise here to have `skipWaiting()`
            // fire after the cache has been updated.
            return cache.addAll(urlsToCache);
        }).then(function() {
            // `skipWaiting()` forces the waiting ServiceWorker to become the
            // active ServiceWorker, triggering the `onactivate` event.
            // Together with `Clients.claim()` this allows a worker to take effect
            // immediately in the client(s).
            return self.skipWaiting();
        })
    );
});

// Activate event
// Be sure to call self.clients.claim()
self.addEventListener('activate', function(event) {
    // `claim()` sets this worker as the active worker for all clients that
    // match the workers scope and triggers an `oncontrollerchange` event for
    // the clients.
    return self.clients.claim();
});

self.addEventListener('message', function(event){
    console.log("SW Received Message: " + event.data);
    self.registration.showNotification(event.data, {
        icon: "/agility/images/logos/agilitycontest.png",
        vibrate: [200, 100, 200, 100, 200, 100, 200]
    }).then(function(NotificationEvent) { event.ports[0].postMessage("SW Says 'Hello back!'"); });

});
