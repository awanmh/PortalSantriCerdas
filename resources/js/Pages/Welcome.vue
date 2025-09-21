<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});

// 🔹 Dark mode toggle
const isDark = ref(false);

onMounted(() => {
    const darkModeStored = localStorage.getItem("dark");
    if (darkModeStored === "true") {
        isDark.value = true;
        document.documentElement.classList.add("dark");
    }
});

function toggleDarkMode() {
    isDark.value = !isDark.value;
    if (isDark.value) document.documentElement.classList.add("dark");
    else document.documentElement.classList.remove("dark");
    localStorage.setItem("dark", isDark.value);
}
</script>

<template>
    <Head title="Portal Santri Cerdas - SMK Al-Ikhlash" />

    <div class="relative min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 font-body overflow-x-hidden">

        <!-- Header -->
        <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-md transition">
            <nav class="flex justify-between items-center max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" alt="Logo SMK Al-Ikhlash" class="h-12 w-auto" />
                    <span class="font-semibold text-gray-800 dark:text-white text-lg md:text-xl font-pesantren">
                        SMK Al-Ikhlash Dalegan
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Dark Mode Toggle -->
                    <button @click="toggleDarkMode"
                        class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium"
                    >
                        {{ isDark ? "Light Mode" : "Dark Mode" }}
                    </button>

                    <Link v-if="$page.props.auth && $page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-5 py-2 rounded-md text-sm md:text-base font-medium text-white bg-gradient-to-r from-green-600 to-blue-600 shadow-lg hover:opacity-90 transition"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link :href="route('login')"
                            class="px-5 py-2 rounded-md text-sm md:text-base font-medium text-green-700 border border-green-600 bg-white hover:bg-green-50 dark:bg-slate-700 dark:text-green-400 dark:border-green-500 dark:hover:bg-slate-600 transition shadow-sm"
                        >
                            Masuk
                        </Link>
                        <Link :href="route('register')"
                            class="hidden sm:inline-flex px-5 py-2 rounded-md text-sm md:text-base font-medium text-white bg-gradient-to-r from-blue-600 to-green-600 hover:opacity-90 transition shadow-lg"
                        >
                            Daftar
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex flex-col-reverse md:flex-row items-center justify-between min-h-[calc(100vh-100px)] max-w-7xl mx-auto px-6 gap-12 md:gap-20 py-12">
            
            <!-- Left: Info -->
            <div class="flex-1 text-center md:text-left space-y-6">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 dark:text-white leading-tight font-pesantren">
                    Portal <span class="text-green-600 dark:text-green-400">Santri</span> Cerdas
                </h1>
                <p class="text-base md:text-lg text-gray-600 dark:text-gray-300 max-w-lg mx-auto md:mx-0">
                    Media informasi untuk memonitor absensi dan kegiatan santri
                    <span class="font-semibold text-blue-600 dark:text-blue-400">SMK Al-Ikhlash Dalegan</span>.  
                    Modern, ramah, dan mudah digunakan di semua perangkat.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-2">
                    <Link :href="route('login')"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-md text-base md:text-lg font-medium text-white bg-gradient-to-r from-green-600 to-blue-600 shadow-lg hover:opacity-90 transition transform hover:-translate-y-1 hover:scale-105"
                    >
                        Masuk
                    </Link>
                    <Link :href="route('register')"
                        class="sm:hidden inline-flex items-center justify-center px-6 py-3 rounded-md text-base md:text-lg font-medium text-green-700 border border-green-600 bg-white hover:bg-green-50 dark:bg-slate-700 dark:text-green-400 dark:border-green-500 dark:hover:bg-slate-600 transition shadow-sm transform hover:-translate-y-1 hover:scale-105"
                    >
                        Daftar
                    </Link>
                </div>
            </div>

            <!-- Right: Illustration + Card -->
            <div class="flex-1 flex justify-center">
                <div class="relative w-full max-w-md md:max-w-lg">
                    <!-- Decorative pattern behind -->
                    <div class="absolute inset-0 rounded-xl bg-green-100 dark:bg-green-900/30 blur-3xl -z-10"></div>

                    <!-- Glass Card -->
                    <div class="p-4 bg-white/80 dark:bg-slate-800/80 rounded-xl shadow-2xl border border-green-100 dark:border-slate-700 transform hover:-translate-y-2 transition duration-500">
                        <img
                            src="https://placehold.co/600x400/f0fdf4/166534?text=SMK+Al-Ikhlash+Santri+Berprestasi"
                            alt="Ilustrasi Sekolah"
                            class="rounded-xl object-cover w-full h-full"
                        />
                    </div>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="bg-white/80 dark:bg-slate-800/80 border-t dark:border-slate-700 py-6 mt-12 transition">
            <div class="text-center text-sm md:text-base text-gray-600 dark:text-gray-400 font-pesantren">
                &copy; {{ new Date().getFullYear() }} SMK Al-Ikhlash Dalegan. Semua Hak Dilindungi.
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Animasi lembut pada hero card */
img {
    transition: transform 0.5s ease, box-shadow 0.3s ease;
}
img:hover {
    transform: scale(1.03);
}
</style>
