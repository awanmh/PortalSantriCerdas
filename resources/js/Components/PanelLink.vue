<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    color: {
        type: String,
        default: 'text-gray-500',
    },
    // --- DITAMBAHKAN ---
    // Prop opsional untuk menambahkan label/badge
    label: {
        type: String,
        default: null, // Defaultnya null, jadi tidak akan tampil jika tidak diisi
    },
});
</script>

<template>
    <!-- Dibungkus dengan div relative untuk posisi label -->
    <div class="relative">
        <Link
            :href="href"
            class="group flex h-full items-start space-x-4 rounded-lg bg-gray-50 p-4 transition-all duration-300 hover:bg-white hover:shadow-lg hover:ring-1 hover:ring-gray-200 dark:bg-gray-900/50 dark:hover:bg-gray-700 dark:hover:ring-gray-600"
        >
            <!-- Ikon -->
            <div :class="[color, 'flex-shrink-0 rounded-lg bg-white dark:bg-gray-800 p-3 ring-1 ring-gray-200 dark:ring-gray-700 group-hover:bg-opacity-80']">
                <slot name="icon" />
            </div>

            <!-- Teks (Judul & Deskripsi) -->
            <div>
                <h4 class="font-bold text-gray-800 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    {{ title }}
                </h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ description }}
                </p>
            </div>
        </Link>

        <!-- --- LABEL / BADGE DITAMBAHKAN --- -->
        <!-- Tampil hanya jika prop 'label' diisi -->
        <span v-if="label" class="absolute top-3 right-3 bg-cyan-100 text-cyan-800 text-xs font-medium px-2.5 py-0.5 rounded-full dark:bg-cyan-900 dark:text-cyan-300">
            {{ label }}
        </span>
    </div>
</template>

