import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import "../css/app.css";
import "./bootstrap";
import "leaflet/dist/leaflet.css";

// Gunakan nama app dari .env atau fallback "Laravel"
const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
  // Judul halaman otomatis
  title: (title) => `${title} - ${appName}`,

  // Auto-import semua halaman di resources/js/Pages
  resolve: (name) =>
    resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue")),

  // Setup Vue app
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue) // untuk helper route() di Vue
      .mount(el);
  },

  // Progress bar Inertia
  progress: {
    color: "#4B5563",
  },
});
