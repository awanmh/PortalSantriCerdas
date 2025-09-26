<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ClockIcon,
    ExclamationTriangleIcon,
    QrCodeIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    DocumentTextIcon // Pastikan ini sudah diimpor jika digunakan
} from '@heroicons/vue/24/outline';

// Import komponen QrScannerModal yang hilang
import QrScannerModal from '@/Components/QrScannerModal.vue'; 

// --- PROPS ---
const props = defineProps({
    dashboardData: {
        type: Object,
        required: true,
    },
    // Jika Anda ingin menampilkan errors dari Inertia di komponen,
    // Anda bisa menambahkan ini, walaupun saat ini belum digunakan secara eksplisit
    errors: Object, 
});

// --- STATE MANAGEMENT ---
const showScannerModal = ref(false);
const absenTipe = ref(''); // 'masuk' atau 'pulang'

// --- COMPUTED PROPERTIES ---
/**
 * Helper untuk memformat waktu dari string datetime menjadi HH:MM.
 * @param {string} datetimeString - String datetime (contoh: "2023-10-26 08:00:00")
 * @returns {string} Waktu dalam format HH:MM atau '-' jika null/undefined
 */
const formatTime = (datetimeString) => {
    if (!datetimeString) return '-';
    // Menggunakan slice untuk memastikan format HH:MM, lebih andal
    return datetimeString.slice(11, 16);
};

const absensiHariIni = computed(() => props.dashboardData?.absensi_hari_ini);
const jadwalMengajar = computed(() => props.dashboardData?.jadwal_mengajar || []);

// Rekap absensi siswa untuk ditampilkan di dashboard guru (opsional)
const rekapSiswa = computed(() => props.dashboardData?.rekap_siswa || { hadir: 0, izin: 0, sakit: 0, alfa: 0 });

const sudahAbsenMasuk = computed(() => !!absensiHariIni.value?.waktu_masuk);
const sudahAbsenPulang = computed(() => !!absensiHariIni.value?.waktu_pulang);

// --- FUNGSI-FUNGSI ---
/**
 * Membuka modal QR Scanner.
 * @param {string} tipe - Tipe absensi ('masuk' atau 'pulang').
 */
const openScanner = (tipe) => {
    absenTipe.value = tipe;
    showScannerModal.value = true;
};

/**
 * Menutup modal QR Scanner.
 */
const closeScanner = () => {
    showScannerModal.value = false;
};

/**
 * Handle hasil scan QR Code dari QrScannerModal.
 * @param {object} payload - Data dari hasil scan, diharapkan berisi {qr_code: string}.
 */
const handleScanSuccess = (payload) => {
    // Menambahkan tipe absen ke payload untuk dikirim ke backend
    const dataToSend = { ...payload, absen_type: absenTipe.value };
    const form = useForm(dataToSend);
    
    form.post(route('api.absen.guru.store'), {
        onSuccess: () => {
            closeScanner();
            alert('Absensi berhasil!');
            // Hanya refresh data dashboard, tidak seluruh halaman untuk pengalaman yang lebih baik
            router.reload({ only: ['dashboardData'] }); 
        },
        onError: (errors) => {
            // Mengambil pesan error pertama atau gabungan dari semua error
            const errorMessage = Object.values(errors).flat().join(' ') || "Terjadi kesalahan yang tidak diketahui saat absensi.";
            alert(`Absensi Gagal: ${errorMessage}`);
            closeScanner();
        }
    });
};
</script>

<template>
    <Head title="Dashboard Guru" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard Guru
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <ClockIcon class="h-6 w-6 mr-2 text-blue-500" />
                                Absensi Anda Hari Ini
                            </h3>
                            <div class="space-y-3">
                                <button 
                                    @click="openScanner('masuk')" 
                                    v-if="!sudahAbsenMasuk"
                                    class="w-full flex items-center justify-center px-4 py-3 font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow"
                                >
                                    <QrCodeIcon class="h-5 w-5 mr-2" />
                                    Absen Masuk
                                </button>
                                <button 
                                    @click="openScanner('pulang')" 
                                    v-if="sudahAbsenMasuk && !sudahAbsenPulang"
                                    class="w-full flex items-center justify-center px-4 py-3 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200 shadow"
                                >
                                    <QrCodeIcon class="h-5 w-5 mr-2" />
                                    Absen Pulang
                                </button>
                                <div v-if="sudahAbsenMasuk && sudahAbsenPulang" class="text-center text-gray-500 dark:text-gray-400 py-2">
                                    <p>Anda sudah absen masuk dan pulang hari ini.</p>
                                </div>
                            </div>
                            <div class="mt-4 space-y-2 border-t dark:border-gray-700 pt-4">
                                <div class="flex justify-between items-center text-sm" 
                                    :class="sudahAbsenMasuk ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                                    <span>Status Masuk:</span>
                                    <span v-if="sudahAbsenMasuk" class="font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-md">
                                        {{ formatTime(absensiHariIni.waktu_masuk) }}
                                    </span>
                                    <span v-else class="font-semibold">Belum Absen</span>
                                </div>
                                <div class="flex justify-between items-center text-sm" 
                                    :class="sudahAbsenPulang ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                                    <span>Status Pulang:</span>
                                    <span v-if="sudahAbsenPulang" class="font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded-md">
                                       {{ formatTime(absensiHariIni.waktu_pulang) }}
                                    </span>
                                    <span v-else class="font-semibold">Belum Absen</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <ExclamationTriangleIcon class="h-6 w-6 mr-2 text-red-500" />
                                Aksi Cepat
                            </h3>
                            <div class="space-y-3">
                                <Link
                                    :href="route('guru.absensi.siswa.index')" 
                                    class="w-full text-center block px-4 py-3 font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow"
                                >
                                    <DocumentTextIcon class="h-5 w-5 mr-2 inline-block" /> Cetak Absensi Siswa
                                </Link>
                                <Link
                                    :href="route('catatan-pelanggaran.index')"
                                    class="w-full text-center block px-4 py-3 font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow"
                                >
                                    Catat Pelanggaran Siswa
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <CalendarDaysIcon class="w-6 h-6 mr-2 text-indigo-500" />
                                Jadwal Mengajar Anda Hari Ini
                            </h3>
                            <div v-if="jadwalMengajar.length > 0" class="space-y-4">
                               <div v-for="jadwal in jadwalMengajar" :key="jadwal.id" class="p-4 border rounded-lg dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                    <p class="font-bold text-md text-indigo-700 dark:text-indigo-400">{{ jadwal.mata_pelajaran }}</p>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <ClockIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.jam_mulai.slice(0, 5) }} - {{ jadwal.jam_selesai.slice(0, 5) }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <UserGroupIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.kelas.nama_kelas }}</span>
                                    </div>
                               </div>
                            </div>
                            <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
                                <p>Tidak ada jadwal mengajar untuk Anda hari ini.</p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <UserGroupIcon class="w-6 h-6 mr-2 text-green-500" />
                                Rekap Absensi Siswa Hari Ini
                            </h3>
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div class="p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                                    <p class="text-2xl font-bold text-green-700 dark:text-green-300">{{ rekapSiswa.hadir }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Hadir</p>
                                </div>
                                <div class="p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                                    <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ rekapSiswa.izin }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Izin</p>
                                </div>
                                <div class="p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                                    <p class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ rekapSiswa.sakit }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Sakit</p>
                                </div>
                                <div class="p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                                    <p class="text-2xl font-bold text-red-700 dark:text-red-300">{{ rekapSiswa.alfa }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Alfa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <QrScannerModal
                :show="showScannerModal"
                :tipe="absenTipe"
                @close="closeScanner"
                @scan-success="handleScanSuccess"
            />
        </div>
    </AuthenticatedLayout>
</template>