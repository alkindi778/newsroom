// Service Worker للإشعارات
const CACHE_VERSION = 'v1';
const CACHE_NAME = `newsroom-cache-${CACHE_VERSION}`;

// تثبيت Service Worker
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');
  self.skipWaiting();
});

// تفعيل Service Worker
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((cacheName) => cacheName !== CACHE_NAME)
          .map((cacheName) => caches.delete(cacheName))
      );
    }).then(() => self.clients.claim())
  );
});

// استقبال الإشعارات
self.addEventListener('push', (event) => {
  console.log('Service Worker: Push notification received');
  
  if (!event.data) {
    console.log('Push event but no data');
    return;
  }

  try {
    const data = event.data.json();
    console.log('Push notification data:', data);

    const options = {
      body: data.body || 'لديك إشعار جديد',
      icon: data.icon || '/icon-192x192.png',
      badge: data.badge || '/badge-72x72.png',
      image: data.image,
      tag: data.tag || 'notification-' + Date.now(),
      data: {
        url: data.url || '/',
        ...data.data
      },
      requireInteraction: false,
      vibrate: [200, 100, 200],
      actions: [
        {
          action: 'open',
          title: 'فتح',
          icon: '/icons/open.png'
        },
        {
          action: 'close',
          title: 'إغلاق',
          icon: '/icons/close.png'
        }
      ]
    };

    event.waitUntil(
      self.registration.showNotification(data.title || 'إشعار جديد', options)
    );
  } catch (error) {
    console.error('Error parsing push notification:', error);
    
    // عرض إشعار افتراضي في حالة حدوث خطأ
    event.waitUntil(
      self.registration.showNotification('إشعار جديد', {
        body: 'لديك إشعار جديد من موقع الأخبار',
        icon: '/icon-192x192.png',
        badge: '/badge-72x72.png'
      })
    );
  }
});

// التعامل مع النقر على الإشعار
self.addEventListener('notificationclick', (event) => {
  console.log('Notification clicked:', event);
  
  event.notification.close();

  if (event.action === 'close') {
    return;
  }

  const urlToOpen = event.notification.data?.url || '/';
  
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true })
      .then((clientList) => {
        // البحث عن نافذة مفتوحة
        for (let i = 0; i < clientList.length; i++) {
          const client = clientList[i];
          if (client.url === urlToOpen && 'focus' in client) {
            return client.focus();
          }
        }
        
        // فتح نافذة جديدة إذا لم توجد
        if (clients.openWindow) {
          return clients.openWindow(urlToOpen);
        }
      })
  );
});

// التعامل مع إغلاق الإشعار
self.addEventListener('notificationclose', (event) => {
  console.log('Notification closed:', event);
});

// Fetch Handler (اختياري - للتخزين المؤقت)
self.addEventListener('fetch', (event) => {
  // يمكن إضافة منطق التخزين المؤقت هنا إذا لزم الأمر
});
