<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import PanelLink from '@/Components/PanelLink.vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    ShieldExclamationIcon,
    ClipboardDocumentListIcon,
    UsersIcon,
    DocumentChartBarIcon,
} from "@heroicons/vue/24/outline";

// Menerima props dari DashboardController
const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    pelanggaranTerbaru: {
        type: Array,
        required: true,
    },
    siswaBermasalah: {
        type: Array,
        required: true,
    },
});

</script>

<template>
    <Head title="Dashboard BK" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard Bimbingan Konseling
            </h2>
        </template>
        
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Bagian Statistik dan Aksi Cepat -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <StatCard label="Pelanggaran Hari Ini" :value="stats.pelanggaran_hari_ini" color="text-red-500" />
                    <StatCard label="Total Poin Hari Ini" :value="stats.total_poin_hari_ini" color="text-orange-500" />
                    
                    <PanelLink
                        :href="route('catatan-pelanggaran.index')"
                        title="Catat Pelanggaran"
                        description="Buka halaman untuk mencatat pelanggaran baru."
                        color="text-red-500"
                        class="lg:col-span-1"
                    >
                        <template #icon><ClipboardDocumentListIcon class="w-8 h-8" /></template>
                    </PanelLink>
                    <PanelLink
                        :href="route('laporan.absensi.index')"
                        title="Laporan Absensi"
                        description="Lihat rekapitulasi kehadiran semua siswa."
                        color="text-blue-500"
                        class="lg:col-span-1"
                    >
                        <template #icon><DocumentChartBarIcon class="w-8 h-8" /></template>
                    </PanelLink>
                </div>

                <!-- Bagian Daftar Pelanggaran & Siswa Bermasalah -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Kolom Pelanggaran Terbaru -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <ClipboardDocumentListIcon class="w-6 h-6 mr-2 text-yellow-500" />
                                Pelanggaran Terbaru
                            </h3>
                            <div v-if="pelanggaranTerbaru.length > 0" class="space-y-3">
                                <div v-for="item in pelanggaranTerbaru" :key="item.id" class="text-sm border-b dark:border-gray-700 pb-2 last:border-b-0">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ item.siswa.name }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ item.jenis }} ({{ item.poin }} poin)</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">Dilaporkan oleh {{ item.pelapor.name }}</p>
                                </div>
                            </div>
                            <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
                                <p>Tidak ada catatan pelanggaran baru.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Siswa Perlu Perhatian -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <UsersIcon class="w-6 h-6 mr-2 text-red-500" />
                                Siswa Perlu Perhatian (Poin Tertinggi)
                            </h3>
                            <div v-if="siswaBermasalah.length > 0" class="space-y-3">
                               <div v-for="siswa in siswaBermasalah" :key="siswa.id" class="flex justify-between items-center text-sm border-b dark:border-gray-700 pb-2 last:border-b-0">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ siswa.name }}</p>
                                    <span class="font-bold bg-red-100 text-red-800 px-2 py-1 rounded-md">
                                        {{ siswa.total_poin || 0 }} Poin
                                    </span>
                               </div>
                            </div>
                             <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
                                <p>Tidak ada siswa dengan poin pelanggaran yang signifikan.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>