<script setup>
defineProps({ zonas: Array });
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Manajemen Zona</h1>
        <ul class="list-disc pl-6">
            <li v-for="z in zonas" :key="z.id">
                {{ z.nama }} - {{ z.lokasi }}
            </li>
        </ul>
    </div>
</template>
