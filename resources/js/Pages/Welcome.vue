<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Portal Santri Cerdas - SMK Al-Ikhlash" />

    <div
        class="relative min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 font-body"
    >
        <!-- Header -->
        <header class="bg-white/90 dark:bg-slate-800/90 shadow-sm backdrop-blur-md">
            <nav class="flex justify-between items-center max-w-7xl mx-auto px-6 py-4">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img
                        src="/images/logo.png"
                        alt="Logo SMK Al-Ikhlash"
                        class="h-10 w-auto"
                    />
                    <span
                        class="font-semibold text-gray-800 dark:text-white text-lg font-pesantren"
                    >
                        SMK Al-Ikhlash Dalegan
                    </span>
                </div>

                <!-- Aksi -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth && $page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-4 py-2 rounded-md text-sm font-medium text-white bg-gradient-to-r from-green-600 to-blue-600 hover:opacity-90 transition"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 rounded-md text-sm font-medium text-green-700 border border-green-600 bg-white hover:bg-green-50 dark:bg-slate-700 dark:text-green-400 dark:border-green-500 dark:hover:bg-slate-600 transition"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="hidden sm:inline-flex px-4 py-2 rounded-md text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-green-600 hover:opacity-90 transition"
                        >
                            Daftar
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Konten Utama -->
        <main
            class="flex items-center justify-center min-h-[calc(100vh-80px)] px-6"
        >
            <div
                class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 items-center gap-12"
            >
                <!-- Kiri -->
                <div class="space-y-6 text-center md:text-left">
                    <h1
                        class="text-4xl md:text-5xl font-bold text-gray-800 dark:text-white leading-tight font-pesantren"
                    >
                        Portal <span class="text-green-600 dark:text-green-400">Santri</span>
                        Cerdas
                    </h1>
                    <p
                        class="text-base text-gray-600 dark:text-gray-300 max-w-lg mx-auto md:mx-0"
                    >
                        Media informasi untuk memonitor absensi dan kegiatan santri
                        <span class="font-semibold text-blue-600 dark:text-blue-400"
                            >SMK Al-Ikhlash Dalegan</span
                        >. Dirancang sederhana, ramah, dan cocok untuk semua kalangan.
                    </p>

                    <!-- Tombol Aksi -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-2"
                    >
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-md text-base font-medium text-white bg-gradient-to-r from-green-600 to-blue-600 hover:opacity-90 transition"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="sm:hidden inline-flex items-center justify-center px-6 py-3 rounded-md text-base font-medium text-green-700 border border-green-600 bg-white hover:bg-green-50 dark:bg-slate-700 dark:text-green-400 dark:border-green-500 dark:hover:bg-slate-600 transition"
                        >
                            Daftar
                        </Link>
                    </div>
                </div>

                <!-- Kanan -->
                <div class="hidden md:block">
                    <div
                        class="p-4 bg-white dark:bg-slate-800 rounded-lg shadow-md border border-green-100 dark:border-slate-700"
                    >
                        <img
                            src="https://placehold.co/600x400/f0fdf4/166534?text=SMK+Al-Ikhlash%0ASantri+Berprestasi"
                            alt="Ilustrasi Sekolah"
                            class="rounded-md object-cover w-full h-full"
                        />
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer
            class="bg-white/90 dark:bg-slate-800/90 border-t dark:border-slate-700 py-4 mt-8"
        >
            <div class="text-center text-sm text-gray-600 dark:text-gray-400 font-pesantren">
                &copy; {{ new Date().getFullYear() }} SMK Al-Ikhlash Dalegan.
                Semua Hak Dilindungi.
            </div>
        </footer>
    </div>
</template>
