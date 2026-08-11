self.addEventListener('push', (event) => {
    if (! event.data) {
        return;
    }

    const payload = event.data.json();

    const title = payload.title || 'Ensemble pour la République';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/logo_fr.png',
        badge: '/logo_fr.png',
        data: {
            url: payload.data?.url || payload.url || '/',
        },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clients) => {
            for (const client of clients) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }

            if (self.clients.openWindow) {
                return self.clients.openWindow(url);
            }
        })
    );
});
