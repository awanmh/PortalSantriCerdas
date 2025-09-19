<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() =>
    props.active
        ? "inline-flex items-center px-3 pt-1 border-b-2 border-green-600 text-sm font-medium leading-5 text-green-700 dark:text-green-400 focus:outline-none focus:border-green-700 transition duration-150 ease-in-out"
        : "inline-flex items-center px-3 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 dark:text-gray-300 hover:text-green-700 dark:hover:text-green-400 hover:border-green-300 focus:outline-none focus:text-green-700 dark:focus:text-green-400 focus:border-green-300 transition duration-150 ease-in-out"
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
