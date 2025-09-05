import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// ===============================
// Axios Global Setup
// ===============================
window.axios = axios;

// Default header supaya request dikenali Laravel
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Base URL backend
axios.defaults.baseURL = import.meta.env.VITE_API_URL ?? 'http://127.0.0.1:8000';

// Sertakan cookie Laravel session & XSRF-TOKEN otomatis
axios.defaults.withCredentials = true;

// // Ambil CSRF token dari meta (fallback)
// const token = document.head.querySelector('meta[name="csrf-token"]');
// if (token) {
//   axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
// } else {
//   console.warn(
//     '⚠️ CSRF token tidak ditemukan. Pastikan <meta name="csrf-token" content="{{ csrf_token() }}"> ada di Blade layout.'
//   );
// }

// ===============================
// Laravel Echo + Reverb Setup
// ===============================
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
  wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
  wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
  enabledTransports: ['ws', 'wss'],
});
