<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900"
    >
        <!-- LOGO SEKOLAH -->
        <div>
            <Link href="/">
                <img
                    src="/images/logo.png"
                    alt="Logo Sekolah"
                    class="h-20 w-auto"
                />
            </Link>
            <p class="mt-2 text-center text-lg font-pesantren text-gray-700 dark:text-gray-200">
                SMK Al-Ikhlash
            </p>
        </div>

        <!-- KONTEN UTAMA -->
        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg dark:bg-gray-800"
        >
            <slot />
        </div>
    </div>
</template>
