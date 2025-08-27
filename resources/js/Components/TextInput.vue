<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full border-gray-300 bg-white text-gray-800 focus:border-green-700 focus:ring-green-700 rounded-md shadow-sm"
        v-model="model"
        ref="input"
    />
</template>