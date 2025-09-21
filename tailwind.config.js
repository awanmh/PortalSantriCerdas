import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans], // Body text default
                pesantren: ['"Scheherazade New"', 'serif'], // Heading / judul ala pesantren
                body: ['Nunito', 'sans-serif'], // alias untuk body
            },
        },
    },

    plugins: [forms],

    // 🔹 Tambahkan ini untuk mengaktifkan dark mode berbasis class
    darkMode: 'class',
}
