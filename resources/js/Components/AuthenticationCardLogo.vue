<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <div class="flex flex-col items-center">
            <!-- Pastikan logo Anda ada di public/images/logo.png -->
            <img src="/images/logo.png" alt="Logo SMK Al-Ikhlash" class="h-20 w-auto">
        </div>
    </Link>
</template>

