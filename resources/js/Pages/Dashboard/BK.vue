<script setup>
import { Link } from '@inertiajs/vue3';
import { ShieldExclamationIcon, DocumentTextIcon, ChartPieIcon } from '@heroicons/vue/24/outline';
import { useDashboard } from '@/Composables/useDashboard';

// Menggunakan Composable untuk semua logika data fetching
const { data: dashboardData, isLoading, error: errorMsg } = useDashboard();
</script>

<template>
    <div class="space-y-6">
        <!-- Tampilan Loading atau Error -->
        <div v-if="isLoading" class="text-center py-10 text-gray-500">Memuat data...</div>
        <div v-else-if="errorMsg" class="p-4 bg-red-100 text-red-700 rounded-lg text-center">{{ errorMsg }}</div>

        <!-- Konten Dashboard Utama -->
        <div v-else-if="dashboardData" class="space-y-8">
             <!-- Statistik Pelanggaran -->
            <div>
                <h3 class="font-semibold text-xl mb-4 text-gray-700 flex items-center">
                    <ChartPieIcon class="h-6 w-6 mr-2" />
                    Statistik Pelanggaran
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-red-600">{{ dashboardData.pelanggaran_hari_ini }}</div>
                        <div class="text-sm font-medium text-gray-500">Pelanggaran Hari Ini</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-yellow-500">{{ dashboardData.pelanggaran_bulan_ini }}</div>
                        <div class="text-sm font-medium text-gray-500">Pelanggaran Bulan Ini</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-gray-700">{{ dashboardData.total_poin_pelanggaran }}</div>
                        <div class="text-sm font-medium text-gray-500">Total Poin</div>
                    </div>
                </div>
            </div>

            <!-- Daftar Pelanggaran Terbaru -->
            <div class="bg-white p-5 rounded-xl shadow-md">
                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                    <DocumentTextIcon class="h-6 w-6 mr-2 text-gray-500" />
                    Catatan Pelanggaran Terbaru
                </h3>
                <div v-if="dashboardData.pelanggaran_terbaru && dashboardData.pelanggaran_terbaru.length > 0" class="space-y-4">
                    <div v-for="item in dashboardData.pelanggaran_terbaru" :key="item.id" class="border-b pb-3 last:border-b-0">
                        <p class="font-semibold text-gray-800">{{ item.siswa.name }} - <span class="font-normal text-gray-600">{{ item.jenis_pelanggaran }}</span></p>
                        <p class="text-sm text-gray-500 mt-1">{{ item.deskripsi }}</p>
                        <p class="text-xs text-gray-400 mt-1">Dicatat pada: {{ new Date(item.tanggal).toLocaleDateString('id-ID') }}</p>
                    </div>
                </div>
                <div v-else>
                    <p class="text-gray-500">Belum ada catatan pelanggaran baru.</p>
                </div>
            </div>

            <!-- Tombol Aksi Cepat -->
            <div class="text-center pt-4">
                 <Link
                    :href="route('catatan-pelanggaran.create')"
                    class="inline-flex items-center justify-center w-full sm:w-auto px-10 py-4 bg-red-600 text-white font-bold text-lg rounded-lg shadow-lg hover:bg-red-700 transition-transform transform hover:scale-105"
                >
                    <ShieldExclamationIcon class="h-6 w-6 mr-3" />
                    Catat Pelanggaran Baru
                </Link>
            </div>
        </div>
    </div>
</template>
