import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/js/**/*.vue',
  ],

  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        primary: '#1E40AF',       // biru nyaman
        secondary: '#FBBF24',     // kuning lembut
        santriGreen: '#16A34A',   // hijau ramah
        santriBg: '#F9FAFB',      // background netral
      },
    },
  },

  plugins: [forms],
};
