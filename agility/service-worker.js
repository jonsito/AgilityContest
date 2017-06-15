/**
 * Created by jantonio on 15/06/17.
 */
console.log("SW Startup!");

// Install Service Worker
self.addEventListener('install', function(event){
    event.waitUntil(self.skipWaiting()); // Activate worker immediately
    console.log('installed!');
});

// Service Worker Active
self.addEventListener('activate', function(event){
    event.waitUntil(self.clients.claim()); // Become available to all pages
    console.log('activated!');
});

self.addEventListener('message', function(event){
    console.log("SW Received Message: " + event.data);
    self.registration.showNotification(event.data, {
        icon: "/agility/images/logos/agilitycontest.png",
        vibrate: [200, 100, 200, 100, 200, 100, 200]
    }).then(function(NotificationEvent) { event.ports[0].postMessage("SW Says 'Hello back!'"); });

});
