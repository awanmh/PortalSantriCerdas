import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],

    // Tambahkan konfigurasi server di sini
    server: {
            host: '127.0.0.1',
            port: 5173,
            hmr: {
            host: '127.0.0.1',
            protocol: 'ws',
            port: 5173,
        },
    },
    // Konfigurasi filesystem, jika ada masalah path aset
    fs: {
        allow: [
            '.', // Mengizinkan akses ke direktori proyek saat ini
            // Tambahkan path lain jika ada resource di luar root proyek yang diakses
        ]
    }
});