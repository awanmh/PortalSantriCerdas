<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ClockIcon, UserGroupIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';
import { useDashboard } from '@/Composables/useDashboard';
import axios from 'axios';

// Menggunakan Composable untuk data awal
const { data: dashboardData, isLoading, error: errorMsg, fetchData } = useDashboard();
const isSubmitting = ref(false); // State khusus untuk loading saat absen

// Fungsi untuk menangani absensi (masuk/pulang)
const handleAbsensi = (tipe) => {
    isSubmitting.value = true;
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            const { latitude, longitude } = position.coords;
            const endpoint = tipe === 'masuk' ? route('api.absen.guru.masuk') : route('api.absen.guru.pulang');
            try {
                await axios.post(endpoint, { latitude, longitude });
                alert(`Absen ${tipe} berhasil!`);
                await fetchData(); // Refresh data dashboard setelah berhasil
            } catch (error) {
                alert(`Gagal melakukan absen ${tipe}: ${error.response?.data?.message || error.message}`);
            } finally {
                isSubmitting.value = false;
            }
        },
        (error) => {
            alert(`Tidak bisa mendapatkan lokasi GPS: ${error.message}`);
            isSubmitting.value = false;
        },
        { enableHighAccuracy: true }
    );
};
</script>

<template>
    <div class="space-y-6">
        <div v-if="isLoading" class="text-center py-10 text-gray-500">Memuat data...</div>
        <div v-else-if="errorMsg" class="p-4 bg-red-100 text-red-700 rounded-lg text-center">{{ errorMsg }}</div>
        <div v-else-if="dashboardData" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Aksi Utama -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Kartu Absensi Guru -->
                <div class="bg-white p-5 rounded-xl shadow-md">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <ClockIcon class="h-6 w-6 mr-2 text-blue-500" />
                        Absensi Anda Hari Ini
                    </h3>
                    <div class="space-y-3">
                        <button
                            @click="handleAbsensi('masuk')"
                            :disabled="dashboardData.absensi_masuk_hari_ini || isSubmitting"
                            class="w-full px-4 py-3 font-semibold text-white bg-blue-600 rounded-lg shadow-sm hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition flex items-center justify-center"
                        >
                             <span v-if="isSubmitting && !dashboardData.absensi_masuk_hari_ini">Memproses...</span>
                             <span v-else>{{ dashboardData.absensi_masuk_hari_ini ? `Masuk: ${new Date(dashboardData.absensi_masuk_hari_ini.waktu).toLocaleTimeString('id-ID')}` : 'Absen Masuk' }}</span>
                        </button>
                        <button
                            @click="handleAbsensi('pulang')"
                            :disabled="!dashboardData.absensi_masuk_hari_ini || dashboardData.absensi_pulang_hari_ini || isSubmitting"
                            class="w-full px-4 py-3 font-semibold text-blue-700 bg-blue-100 rounded-lg shadow-sm hover:bg-blue-200 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed transition flex items-center justify-center"
                        >
                            <span v-if="isSubmitting && !dashboardData.absensi_pulang_hari_ini">Memproses...</span>
                            <span v-else>{{ dashboardData.absensi_pulang_hari_ini ? `Pulang: ${new Date(dashboardData.absensi_pulang_hari_ini.waktu).toLocaleTimeString('id-ID')}` : 'Absen Pulang' }}</span>
                        </button>
                    </div>
                </div>
                <!-- Kartu Aksi Cepat -->
                <div class="bg-white p-5 rounded-xl shadow-md">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <ExclamationTriangleIcon class="h-6 w-6 mr-2 text-red-500" />
                        Aksi Cepat
                    </h3>
                    <Link
                        :href="route('catatan-pelanggaran.index')"
                        class="w-full text-center block px-4 py-3 font-semibold text-white bg-red-600 rounded-lg shadow-sm hover:bg-red-700 transition"
                    >
                        Catat Pelanggaran Siswa
                    </Link>
                </div>
            </div>
            <!-- Kolom Kanan: Informasi -->
            <div class="lg:col-span-2">
                <div class="bg-white p-5 rounded-xl shadow-md">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center">
                        <UserGroupIcon class="h-6 w-6 mr-2 text-green-500" />
                        Status Kehadiran Siswa Hari Ini
                    </h3>
                    <div v-if="dashboardData.rekap_siswa" class="text-gray-600 space-y-2">
                        <p>Total Siswa: <strong>{{ dashboardData.rekap_siswa.total_siswa }}</strong></p>
                        <p>Sudah Absen: <strong class="text-green-600">{{ dashboardData.rekap_siswa.sudah_absen }}</strong></p>
                        <p>Belum Absen: <strong class="text-red-600">{{ dashboardData.rekap_siswa.belum_absen }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
