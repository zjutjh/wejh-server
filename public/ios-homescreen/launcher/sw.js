// Names of the two caches used in this version of the service worker.
// Change to v2, etc. when you update any of the local resources, which will
// in turn trigger the install event again.
const PRECACHE = "precache-v1";
const RUNTIME = "runtime-v1";

// A list of local resources we always want to be cached.
const PRECACHE_URLS = [
  "index.html",
  "./", // Alias for index.html
];

// The install handler takes care of precaching the resources we always need.
self.addEventListener("install", (event) => {
  event.waitUntil(async () => {
    await (await caches.open()).addAll(PRECACHE_URLS);
  });
});

// The activate handler takes care of cleaning up old caches.
self.addEventListener("activate", (event) => {
  const currentCaches = [PRECACHE, RUNTIME];
  event.waitUntil(async () => {
    const cachesToDelete = await (await caches.keys()).filter(
      (cacheName) => !currentCaches.includes(cacheName)
    );
    await Promise.all(
      cachesToDelete.map((cacheToDelete) => {
        return caches.delete(cacheToDelete);
      })
    );
    await self.clients.claim();
  });
});

addEventListener("fetch", function (e) {
  e.respondWith(
    (async () => {
      const cachedResponse = await caches.match(e.request);
      if (cachedResponse) {
        return cachedResponse;
      }

      const networkResponse = await fetch(e.request);

      const hosts = ["https://assets.gettoset.cn"];

      const url = e.request.url;
      if (
        url.startsWith(self.location.origin) ||
        hosts.some((host) => url.startsWith(host))
      ) {
        // This clone() happens before `return networkResponse`
        const clonedResponse = networkResponse.clone();

        e.waitUntil(
          (async () => {
            const cache = await caches.open(RUNTIME);
            // This will be called after `return networkResponse`
            // so make sure you already have the clone!
            await cache.put(e.request, clonedResponse);
          })()
        );
      }

      return networkResponse;
    })()
  );
});
