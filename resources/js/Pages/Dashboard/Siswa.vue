<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onUnmounted } from 'vue';
import { ClockIcon, UserIcon, MapPinIcon, CheckCircleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';

// Menerima props dari DashboardController
const props = defineProps({
    jadwalHariIni: {
        type: Array,
        required: true,
    },
    kelasSiswa: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const flash = computed(() => page.props.flash);
const namaKelas = computed(() => props.kelasSiswa?.nama_kelas || 'Kelas tidak ditemukan');

// --- STATE UNTUK LIVE TRACKING ---
const isTracking = ref(false);
const trackingInterval = ref(null);
const trackingStatus = ref('Nonaktif');

// --- FUNGSI UNTUK LIVE TRACKING ---
const toggleTracking = () => {
    if (isTracking.value) {
        // Hentikan pelacakan
        clearInterval(trackingInterval.value);
        isTracking.value = false;
        trackingStatus.value = 'Sesi dihentikan.';
    } else {
        // Mulai pelacakan
        isTracking.value = true;
        trackingStatus.value = 'Mendapatkan lokasi awal...';
        sendLocationUpdate(); // Kirim lokasi pertama kali
        // Kirim lokasi setiap 30 detik
        trackingInterval.value = setInterval(sendLocationUpdate, 30000);
    }
};

const sendLocationUpdate = () => {
    if (!navigator.geolocation) {
        trackingStatus.value = 'GPS tidak didukung oleh browser ini.';
        isTracking.value = false;
        clearInterval(trackingInterval.value);
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            trackingStatus.value = `Lokasi diperbarui pada ${new Date().toLocaleTimeString('id-ID')}`;
            // Menggunakan Axios untuk mengirim data ke API endpoint
            axios.post(route('api.live.lokasi.update'), {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            }).catch(err => {
                trackingStatus.value = 'Gagal mengirim lokasi ke server.';
                console.error(err);
                // Hentikan jika ada error, misal token kedaluwarsa atau server mati
                clearInterval(trackingInterval.value);
                isTracking.value = false;
            });
        },
        (err) => {
            trackingStatus.value = 'Gagal mendapatkan lokasi GPS. Pastikan izin diberikan.';
            console.error(err);
            isTracking.value = false;
            clearInterval(trackingInterval.value);
        },
        { enableHighAccuracy: true }
    );
};

// Pastikan interval berhenti saat pengguna meninggalkan halaman untuk mencegah memory leak
onUnmounted(() => {
    if (trackingInterval.value) {
        clearInterval(trackingInterval.value);
    }
});
</script>

<template>
    <Head title="Dashboard Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard Siswa</h2>
                <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, {{ user.name }}!</p>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Notifikasi Flash Message -->
                <div v-if="flash?.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg flex items-center shadow-md" role="alert">
                    <CheckCircleIcon class="h-6 w-6 mr-3"/>
                    <span class="font-medium">{{ flash.success }}</span>
                </div>
                <div v-if="flash?.info" class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg flex items-center shadow-md" role="alert">
                    <InformationCircleIcon class="h-6 w-6 mr-3"/>
                    <span class="font-medium">{{ flash.info }}</span>
                </div>

                <!-- Kartu Live Tracking -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div>
                            <h3 class="font-bold text-lg flex items-center">
                                <MapPinIcon class="w-6 h-6 mr-2 text-red-500" />
                                Sesi Lacak Lokasi
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Aktifkan untuk membagikan lokasi Anda selama jam sekolah.</p>
                            <p v-if="isTracking" class="text-xs text-green-600 dark:text-green-400 mt-1 animate-pulse">{{ trackingStatus }}</p>
                            <p v-else class="text-xs text-gray-500 mt-1">{{ trackingStatus }}</p>
                        </div>
                        <button @click="toggleTracking" class="mt-4 sm:mt-0 px-6 py-2 font-semibold rounded-lg shadow transition-colors"
                            :class="isTracking ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white'">
                            {{ isTracking ? 'Hentikan Sesi' : 'Mulai Sesi Lacak' }}
                        </button>
                    </div>
                </div>

                <!-- Jadwal Pelajaran -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900">
                        <h3 class="text-2xl font-bold mb-1 dark:text-white">Jadwal Pelajaran Hari Ini</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">Kelas: {{ namaKelas }}</p>

                        <div v-if="jadwalHariIni.length > 0" class="space-y-4">
                            <div
                                v-for="jadwal in jadwalHariIni"
                                :key="jadwal.id"
                                class="p-4 border dark:border-gray-700 rounded-lg flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition hover:shadow-md hover:border-teal-500 dark:hover:border-teal-400"
                            >
                                <div class="flex-grow">
                                    <p class="font-bold text-lg text-teal-700 dark:text-teal-300">{{ jadwal.mata_pelajaran }}</p>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <ClockIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.jam_mulai.slice(0, 5) }} - {{ jadwal.jam_selesai.slice(0, 5) }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <UserIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.guru?.name || 'Guru tidak ditentukan' }}</span>
                                    </div>
                                </div>
                                <Link
                                    :href="route('absen.create', jadwal.id)"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-transform transform hover:scale-105"
                                >
                                    Absen Sekarang
                                </Link>
                            </div>
                        </div>
                        <div v-else class="text-center py-10 px-6 border-2 border-dashed rounded-lg dark:border-gray-700">
                            <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Tidak Ada Jadwal</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda tidak memiliki jadwal pelajaran untuk hari ini. Selamat beristirahat!</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

