<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { ClockIcon, UserIcon, MapPinIcon, CheckCircleIcon, InformationCircleIcon } from '@heroicons/vue/24/outline';
import http from '@/requests';

// [FIXED] Memperbaiki sintaks 'defineProps' untuk mengatasi error kompilasi
const props = defineProps({
    jadwalHariIni: {
        type: Array,
        required: true,
    },
    kelasSiswa: {
        type: Object,
        default: null,
    },
    absensiHariIni: {
        type: Array,
        required: true,
    }
});


const page = usePage();
const auth = computed(() => page.props.auth);
const user = computed(() => auth.value?.user || {});
const token = computed(() => auth.value?.token);
const flash = computed(() => page.props.flash);
const namaKelas = computed(() => props.kelasSiswa?.nama_kelas || 'Kelas tidak ditemukan');

const currentTime = ref(new Date());
const timeInterval = ref(null);

const getJadwalStatus = (jadwal) => {
    const now = currentTime.value;
    const today = now.toISOString().slice(0, 10);

    const startTime = new Date(`${today}T${jadwal.jam_mulai}:00`);
    const endTime = new Date(`${today}T${jadwal.jam_selesai}:00`);

    let classState = 'Akan Datang';
    let classStyle = 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200';
    
    if (now >= startTime && now <= endTime) {
        classState = 'Sedang Berlangsung';
        classStyle = 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 animate-pulse';
    } else if (now > endTime) {
        classState = 'Selesai';
        classStyle = 'bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400';
    }

    const sudahAbsen = props.absensiHariIni.includes(jadwal.id);
    const attendanceState = sudahAbsen ? 'Sudah Absen' : 'Belum Absen';
    const attendanceStyle = sudahAbsen ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    
    return { classState, classStyle, attendanceState, attendanceStyle };
};

const trackingInterval = ref(null);
const trackingStatus = ref('Menginisialisasi...');
const trackingColor = ref('text-gray-500');

const startAutomaticTracking = () => {
    trackingStatus.value = 'Mencoba mendapatkan lokasi GPS...';
    trackingColor.value = 'text-yellow-500 animate-pulse';
    sendLocationUpdate();
    trackingInterval.value = setInterval(sendLocationUpdate, 30000);
};

const sendLocationUpdate = async () => {
    if (!navigator.geolocation) {
        trackingStatus.value = 'Error: GPS tidak didukung browser ini.';
        trackingColor.value = 'text-red-500';
        clearInterval(trackingInterval.value);
        return;
    }
    try {
        const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, {
                enableHighAccuracy: true, timeout: 10000, maximumAge: 0
            });
        });
        const currentToken = localStorage.getItem('sanctum_token');
        if (!currentToken) throw new Error('Token tidak tersedia');
        
        trackingStatus.value = `Aktif - Lokasi diperbarui pada ${new Date().toLocaleTimeString('id-ID')}`;
        trackingColor.value = 'text-green-500';
        
        await http.post(route('api.live.lokasi.update'), {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
        });
    } catch (err) {
        clearInterval(trackingInterval.value);
        if (err.code === 1) {
            trackingStatus.value = 'Error: Izin lokasi ditolak.';
            trackingColor.value = 'text-red-500';
        } else if (err.response?.status === 401) {
            trackingStatus.value = 'Error: Autentikasi gagal (401).';
            trackingColor.value = 'text-red-500';
            localStorage.removeItem('sanctum_token');
            setTimeout(() => { window.location.href = '/login'; }, 3000);
        } else {
            trackingStatus.value = 'Error: Gagal mengirim lokasi.';
            trackingColor.value = 'text-red-500';
        }
    }
};

onMounted(() => {
    timeInterval.value = setInterval(() => {
        currentTime.value = new Date();
    }, 60000);

    if (token.value) {
        localStorage.setItem('sanctum_token', token.value);
    }
    if (user.value.roles && user.value.roles.includes('siswa')) {
        startAutomaticTracking();
    } else {
        trackingStatus.value = 'Error: Hanya untuk siswa';
        trackingColor.value = 'text-red-500';
    }
});


onUnmounted(() => {
    if (trackingInterval.value) {
        clearInterval(trackingInterval.value);
    }
    if (timeInterval.value) {
        clearInterval(timeInterval.value);
    }
});
</script>

<template>
    <Head title="Dashboard Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard Siswa</h2>
                    <p class="text-sm text-gray-500 mt-1">Selamat datang kembali, {{ user?.name }}!</p>
                </div>
                <div class="mt-2 sm:mt-0 flex items-center p-2 rounded-lg bg-gray-200 dark:bg-gray-700">
                    <MapPinIcon class="w-5 h-5 mr-2" :class="trackingColor" />
                    <span class="text-xs font-semibold" :class="trackingColor">{{ trackingStatus }}</span>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="flash?.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg flex items-center shadow-md dark:bg-gray-900 dark:border-green-600 dark:text-green-300" role="alert">
                    <CheckCircleIcon class="h-6 w-6 mr-3"/>
                    <span class="font-medium">{{ flash.success }}</span>
                </div>
                <div v-if="flash?.info" class="mb-6 bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-4 rounded-lg flex items-center shadow-md dark:bg-gray-900 dark:border-blue-600 dark:text-blue-300" role="alert">
                    <InformationCircleIcon class="h-6 w-6 mr-3"/>
                    <span class="font-medium">{{ flash.info }}</span>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8">
                        <h3 class="text-2xl font-bold mb-1 text-gray-900 dark:text-gray-100">Jadwal Pelajaran Hari Ini</h3>
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
                                        <span>{{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <UserIcon class="w-4 h-4 mr-1.5" />
                                        <span>{{ jadwal.guru?.name || 'Guru tidak ditentukan' }}</span>
                                    </div>
                                </div>
                                <!-- [CHANGED] Tombol Absen Dihilangkan, hanya menampilkan status -->
                                <div class="w-full sm:w-auto flex flex-col sm:items-end gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getJadwalStatus(jadwal).classStyle">
                                            {{ getJadwalStatus(jadwal).classState }}
                                        </span>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getJadwalStatus(jadwal).attendanceStyle">
                                            {{ getJadwalStatus(jadwal).attendanceState }}
                                        </span>
                                    </div>
                                </div>
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
