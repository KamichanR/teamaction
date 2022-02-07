import Echo from 'laravel-echo';

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'cd64da0c5d36cb6ac219',
    cluster: 'ap3',
    forceTLS: true,
    encrypted: true,
});
