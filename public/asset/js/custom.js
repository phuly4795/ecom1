import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
});

// Lắng nghe contact message
window.Echo.channel('contact-messages')
    .listen('NewContactMessage', (e) => {
        alert('Tin nhắn mới từ: ' + e.contact.name);
        // TODO: Update message dropdown here
    });
