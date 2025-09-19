// requests.js
// Menginisialisasi Axios dan mengonfigurasi interceptor untuk CSRF token

import axios from 'axios';

// Buat instance Axios baru untuk request API
const http = axios.create({
    baseURL: '/api', // Ini digunakan untuk semua request API Anda
    withCredentials: true,
});

// Interceptor untuk mengambil CSRF token dari Sanctum
const getCsrfToken = () => {
    // Perbaikan: Gunakan axios standar (bukan instance http) untuk
    // menghindari awalan '/api' pada URL CSRF cookie.
    return axios.get('/sanctum/csrf-cookie').then(response => {
        // CSRF token akan disimpan secara otomatis di cookie browser
        // oleh Laravel dan akan dikirim pada setiap request berikutnya
        // dengan withCredentials: true
        return response;
    });
};

// Interceptor permintaan
// Setiap kali permintaan dikirim, interceptor ini akan memastikan
// cookie CSRF telah diambil terlebih dahulu.
http.interceptors.request.use(async (config) => {
    // Cek apakah CSRF token sudah ada di cookie
    // Jika tidak ada, ambil dari endpoint sanctum
    if (!document.cookie.includes('XSRF-TOKEN')) {
        await getCsrfToken();
    }

    // Set header X-XSRF-TOKEN dari cookie jika ada
    const token = document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='));
    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token.split('=')[1]);
    }

    return config;
}, (error) => {
    return Promise.reject(error);
});

export default http;
