import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '3cd1ba9bc8746d6a85f0', // Thay thế bằng key thực tế
    cluster: 'ap1',
    forceTLS: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token') // Thay thế bằng token của bạn
        }
    }
});
