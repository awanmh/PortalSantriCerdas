import axios from 'axios';

// Buat instance axios untuk API
const http = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});

// Interceptor request
http.interceptors.request.use(async (config) => {
    // AMBIL TOKEN DARI LOCALSTORAGE ATAU INERTIA
    let token = localStorage.getItem('sanctum_token');

if (!token && window?.$page?.props?.auth?.token) {
    token = window.$page.props.auth.token;
    localStorage.setItem('sanctum_token', token);
}


    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    // Pastikan CSRF token ada
    if (!document.cookie.includes('XSRF-TOKEN')) {
        await axios.get('/sanctum/csrf-cookie', { 
            withCredentials: true 
        });
    }

    return config;
}, (error) => {
    return Promise.reject(error);
});

// Interceptor response
http.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Hapus token dan redirect ke login
            localStorage.removeItem('sanctum_token');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default http;