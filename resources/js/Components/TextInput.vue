<script setup>
import { onMounted, ref } from 'vue';

// --- PERBAIKAN DI SINI ---
// Mengizinkan modelValue untuk menerima tipe String atau Number.
const model = defineModel({
    type: [String, Number],
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({
    focus: () => input.value.focus(),
});
</script>

<template>
    <input
        ref="input"
        v-model="model"
        class="rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm 
               focus:border-green-600 focus:ring-green-500 dark:focus:border-green-400 dark:focus:ring-green-400 
               placeholder:text-gray-400 dark:placeholder:text-gray-500 w-full"
    />
</template>