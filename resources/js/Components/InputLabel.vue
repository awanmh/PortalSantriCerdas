<script setup>
defineProps({
    value: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <label
        class="block text-sm font-medium text-gray-700 dark:text-gray-300 font-pesantren"
    >
        <template v-if="value">
            {{ value }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </template>
        <template v-else>
            <slot />
        </template>
    </label>
</template>
