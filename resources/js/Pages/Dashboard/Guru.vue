<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { ClockIcon, ExclamationTriangleIcon, QrCodeIcon, CalendarDaysIcon, UserGroupIcon } from '@heroicons/vue/24/outline';
import Modal from '@/Components/Modal.vue';
import QrScannerModal from '@/Components/QrScannerModal.vue';

// --- PROPS ---
const props = defineProps({
    dashboardData: {
        type: Object,
        required: true,
    },
});

// --- STATE MANAGEMENT ---
const showScannerModal = ref(false);
const absenTipe = ref('');

// --- COMPUTED PROPERTIES ---
// Helper untuk memformat waktu dari string datetime
const formatTime = (datetimeString) => {
    if (!datetimeString) return '-';
    // Menggunakan slice untuk memastikan format HH:MM, lebih andal dari toLocaleTimeString
    return datetimeString.slice(11, 16);
};

const absensiHariIni = computed(() => props.dashboardData?.absensi_hari_ini);
const jadwalMengajar = computed(() => props.dashboardData?.jadwal_mengajar || []);

const sudahAbsenMasuk = computed(() => !!absensiHariIni.value?.waktu_masuk);
const sudahAbsenPulang = computed(() => !!absensiHariIni.value?.waktu_pulang);

// --- FUNGSI-FUNGSI ---
const openScanner = (tipe) => {
    absenTipe.value = tipe;
    showScannerModal.value = true;
};

const closeScanner = () => {
    showScannerModal.value = false;
};

// Fungsi ini dipanggil dari komponen QrScannerModal setelah scan berhasil
const handleScanSuccess = (payload) => {
    // Menggunakan Inertia form untuk menangani request, lebih baik daripada axios langsung
    const form = useForm(payload);
    
    form.post(route('api.absen.guru.store'), {
        onSuccess: () => {
            closeScanner();
            alert('Absensi berhasil!');
            // Hanya refresh data dashboard, bukan seluruh halaman
            router.reload({ only: ['dashboardData'] }); 
        },
        onError: (errors) => {
            // Menampilkan pesan error yang lebih jelas dari server
            const errorMessage = Object.values(errors).join(' ') || "Terjadi kesalahan saat absensi.";
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
                    <!-- Kolom Kiri: Absensi & Aksi Cepat -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <ClockIcon class="h-6 w-6 mr-2 text-blue-500" />
                                Absensi Anda Hari Ini
                            </h3>
                            <div class="space-y-3">
                                <button @click="openScanner('masuk')" v-if="!sudahAbsenMasuk"
                                    class="w-full flex items-center justify-center px-4 py-3 font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200 shadow">
                                    <QrCodeIcon class="h-5 w-5 mr-2" />
                                    Absen Masuk
                                </button>
                                <button @click="openScanner('pulang')" v-if="sudahAbsenMasuk && !sudahAbsenPulang"
                                    class="w-full flex items-center justify-center px-4 py-3 font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors duration-200 shadow">
                                    <QrCodeIcon class="h-5 w-5 mr-2" />
                                    Absen Pulang
                                </button>
                            </div>
                            <div class="mt-4 space-y-2 border-t dark:border-gray-700 pt-4">
                                <div class="flex justify-between items-center text-sm" :class="sudahAbsenMasuk ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                                    <span>Status Masuk:</span>
                                    <span v-if="sudahAbsenMasuk" class="font-bold bg-green-100 text-green-800 px-2 py-1 rounded-md">
                                        {{ formatTime(absensiHariIni.waktu_masuk) }}
                                    </span>
                                    <span v-else class="font-semibold">Belum Absen</span>
                                </div>
                                <div class="flex justify-between items-center text-sm" :class="sudahAbsenPulang ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500'">
                                    <span>Status Pulang:</span>
                                    <span v-if="sudahAbsenPulang" class="font-bold bg-blue-100 text-blue-800 px-2 py-1 rounded-md">
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
                            <Link
                                :href="route('catatan-pelanggaran.index')"
                                class="w-full text-center block px-4 py-3 font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow">
                                Catat Pelanggaran Siswa
                            </Link>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Jadwal Mengajar -->
                    <div class="lg:col-span-2">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm min-h-full">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                                <CalendarDaysIcon class="w-6 h-6 mr-2 text-indigo-500" />
                                Jadwal Mengajar Anda Hari Ini
                            </h3>
                            <div v-if="jadwalMengajar.length > 0" class="space-y-4">
                               <div v-for="jadwal in jadwalMengajar" :key="jadwal.id" class="p-4 border rounded-lg dark:border-gray-700">
                                    <p class="font-bold text-md text-indigo-700 dark:text-indigo-400">{{ jadwal.mata_pelajaran }}</p>
                                    <div class="flex items-center text-sm text-gray-500 mt-1">
                                        <ClockIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.jam_mulai.slice(0, 5) }} - {{ jadwal.jam_selesai.slice(0, 5) }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 mt-1">
                                        <UserGroupIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.kelas.nama_kelas }}</span>
                                    </div>
                               </div>
                            </div>
                            <div v-else class="text-center py-10 text-gray-500 dark:text-gray-400">
                                <p>Tidak ada jadwal mengajar untuk Anda hari ini.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal untuk Scanner QR -->
            <QrScannerModal
                :show="showScannerModal"
                :tipe="absenTipe"
                @close="closeScanner"
                @scan-success="handleScanSuccess"
            />
        </div>
    </AuthenticatedLayout>
</template>

