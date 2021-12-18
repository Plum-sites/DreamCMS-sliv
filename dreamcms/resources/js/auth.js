window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

token = localStorage.getItem('api-token');
if (token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}else {
    console.error('API access token not found!');
}
