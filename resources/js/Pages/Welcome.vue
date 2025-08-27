<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Selamat Datang di Portal Santri Cerdas" />

    <div class="relative min-h-screen w-full bg-green-900">

        <div class="absolute top-0 left-0 p-6 z-20">
            <Link href="/">
                <img
                    src="/images/logo.png"
                    alt="Logo Pesantren"
                    class="h-14 w-auto"
                />
            </Link>
        </div>

        <nav v-if="canLogin" class="absolute top-0 right-0 p-6 z-20">
            <Link
                v-if="$page.props.auth && $page.props.auth.user"
                :href="route('dashboard')"
                class="rounded-md px-4 py-2 text-base font-semibold text-white ring-1 ring-transparent transition hover:opacity-80 focus:outline-none"
            >
                Dashboard
            </Link>

            <template v-else>
                <Link
                    :href="route('login')"
                    class="rounded-md px-4 py-2 text-base font-semibold text-white ring-1 ring-transparent transition hover:opacity-80 focus:outline-none"
                >
                    Log in
                </Link>
                <Link
                    v-if="canRegister"
                    :href="route('register')"
                    class="ms-4 rounded-md px-4 py-2 text-base font-semibold text-white ring-1 ring-transparent transition hover:opacity-80 focus:outline-none"
                >
                    Register
                </Link>
            </template>
        </nav>

        <div class="flex min-h-screen flex-col items-center justify-center p-6 text-white">
            <main class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                    Portal Santri Cerdas
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-300">
                    Membentuk Generasi Unggul Berakhlak Qur'ani.
                </p>
                
                <div class="mt-10">
                    <Link
                        :href="route('login')"
                        class="rounded-full bg-yellow-500 px-8 py-3 text-lg font-semibold text-green-900 shadow-sm transition hover:bg-yellow-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-500"
                    >
                        Masuk ke Portal
                    </Link>
                </div>
            </main>
        </div>

    </div>
</template>