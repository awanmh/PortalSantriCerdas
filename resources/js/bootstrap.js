import axios from "axios";
import Echo from "laravel-echo";
import Pusher from "pusher-js";

// =====================================================
// Axios Global Setup
// =====================================================
window.axios = axios;

// Default header supaya request dikenali Laravel
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Sertakan cookie Laravel session & XSRF-TOKEN otomatis
axios.defaults.withCredentials = true;

// --- PENANGANAN ERROR 419 (PAGE EXPIRED) ---
// Kita menambahkan "interceptor" untuk memantau semua respons dari server.
axios.interceptors.response.use(
  (response) => response, // Jika respons sukses (kode 2xx), biarkan saja.
  (error) => {
    // Jika ada error, kita periksa kodenya.
    if (error.response?.status === 419) {
      // Jika kodenya 419, tampilkan konfirmasi ke pengguna.
      if (confirm("Sesi Anda telah berakhir. Halaman perlu dimuat ulang untuk melanjutkan. Muat ulang sekarang?")) {
        // Jika pengguna setuju, muat ulang halaman.
        window.location.reload();
      }
    }
    // Untuk error lain, biarkan ia ditangani seperti biasa.
    return Promise.reject(error);
  }
);


// =====================================================
// Laravel Echo + Reverb Setup
// =====================================================
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 6001,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
    enabledTransports: ["ws", "wss"],
});

