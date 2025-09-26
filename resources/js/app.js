import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import "../css/app.css";
import "./bootstrap";

// === Leaflet fix ===
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Hapus default getter yang menyebabkan path salah
delete L.Icon.Default.prototype._getIconUrl;

// Override path ikon agar selalu ambil dari public/leaflet
L.Icon.Default.mergeOptions({
  iconRetinaUrl: "/leaflet/marker-icon-2x.png",
  iconUrl: "/leaflet/marker-icon.png",
  shadowUrl: "/leaflet/marker-shadow.png",
});

// === Aplikasi utama ===
const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
  // Judul halaman otomatis
  title: (title) => `${title} - ${appName}`,

  // Auto-import semua halaman di resources/js/Pages
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob("./Pages/**/*.vue")
    ),

  // Setup Vue app
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue) // helper route() di Vue
      .mount(el);
  },

  // Progress bar Inertia
  progress: {
    color: "#4B5563",
  },
});
