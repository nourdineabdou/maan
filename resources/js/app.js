import $ from 'jquery';
import Swal from 'sweetalert2';

window.$ = window.jQuery = $;
window.Swal = Swal;

document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('app-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    document.querySelectorAll('[data-toggle="sidebar"]').forEach((button) => {
        button.addEventListener('click', () => {
            sidebar?.classList.toggle('sidebar-open');
            overlay?.classList.toggle('hidden');
        });
    });

    overlay?.addEventListener('click', () => {
        sidebar?.classList.remove('sidebar-open');
        overlay.classList.add('hidden');
    });

    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();

            Swal.fire({
                title: form.dataset.confirmTitle || 'Confirmer ?',
                text: form.dataset.confirm,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1b5e3a',
                cancelButtonColor: '#6b7280',
                confirmButtonText: form.dataset.confirmButton || 'Confirmer',
                cancelButtonText: form.dataset.cancelButton || 'Annuler',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // Le navigateur ne supporte pas les service workers ou l'enregistrement a échoué :
            // les notifications push resteront simplement indisponibles.
        });
    }
});

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);

    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

window.enablePushNotifications = async function () {
    if (! ('serviceWorker' in navigator) || ! ('PushManager' in window)) {
        Swal.fire({
            icon: 'error',
            title: 'Non disponible',
            text: 'Votre navigateur ne supporte pas les notifications push.',
        });
        return;
    }

    const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;

    if (! vapidPublicKey) {
        return;
    }

    try {
        const permission = await Notification.requestPermission();

        if (permission !== 'granted') {
            Swal.fire({
                icon: 'warning',
                title: 'Permission refusée',
                text: 'Vous avez refusé les notifications. Vous pouvez les autoriser depuis les paramètres de votre navigateur.',
            });
            return;
        }

        const registration = await navigator.serviceWorker.ready;

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
        });

        await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify(subscription),
        });

        Swal.fire({
            icon: 'success',
            title: 'Notifications activées',
            text: 'Vous recevrez désormais des notifications push sur cet appareil.',
        });

        document.dispatchEvent(new CustomEvent('push-subscribed'));
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Impossible d\'activer les notifications push.',
        });
    }
};

window.disablePushNotifications = async function () {
    if (! ('serviceWorker' in navigator)) {
        return;
    }

    const registration = await navigator.serviceWorker.ready;
    const subscription = await registration.pushManager.getSubscription();

    if (subscription) {
        await fetch('/push/unsubscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({ endpoint: subscription.endpoint }),
        });

        await subscription.unsubscribe();
    }

    document.dispatchEvent(new CustomEvent('push-unsubscribed'));
};
