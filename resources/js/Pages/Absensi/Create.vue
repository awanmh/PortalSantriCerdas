<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

// Props yang dikirim dari AbsensiController (Web)
const props = defineProps({
    zonaAktif: Object,
});

// State reaktif
const status = ref('Mencari lokasi Anda...'); // Status awal
const errorMsg = ref(null);
const userCoords = ref(null);
const distance = ref(null);
const isWithinZone = ref(false);
const photoPreview = ref(null);
const photoFile = ref(null);
const isSubmitting = ref(false);

const videoRef = ref(null);
const canvasRef = ref(null);

let locationWatcher = null;

// Fungsi untuk menghitung jarak (Haversine formula)
const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3; // Radius bumi dalam meter
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c; // Hasil dalam meter
};

// Fungsi untuk memulai kamera
const startCamera = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        videoRef.value.srcObject = stream;
    } catch (err) {
        errorMsg.value = "Kamera tidak dapat diakses. Pastikan Anda memberikan izin.";
        console.error("Error accessing camera:", err);
    }
};

// Fungsi untuk mengambil foto
const takePhoto = () => {
    const video = videoRef.value;
    const canvas = canvasRef.value;
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    photoPreview.value = canvas.toDataURL('image/png');
    canvas.toBlob(blob => {
        photoFile.value = blob;
    }, 'image/png');

    // Hentikan stream kamera setelah foto diambil
    const stream = video.srcObject;
    const tracks = stream.getTracks();
    tracks.forEach(track => track.stop());
    video.srcObject = null;
};

// Fungsi untuk mengulang pengambilan foto
const retakePhoto = () => {
    photoPreview.value = null;
    photoFile.value = null;
    startCamera();
};

// Fungsi untuk memproses pembaruan lokasi
const handleLocationUpdate = (position) => {
    userCoords.value = {
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
    };

    if (props.zonaAktif) {
        const d = calculateDistance(
            userCoords.value.latitude,
            userCoords.value.longitude,
            props.zonaAktif.lat,
            props.zonaAktif.lng
        );
        distance.value = Math.round(d);
        isWithinZone.value = distance.value <= props.zonaAktif.radius;
        status.value = isWithinZone.value ? 'Anda berada di dalam zona' : 'Anda berada di luar zona';
    } else {
        status.value = 'Zona absensi tidak ditemukan';
        errorMsg.value = 'Tidak ada zona absensi yang aktif saat ini.';
    }
};

// Fungsi untuk menangani error lokasi
const handleLocationError = (error) => {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            errorMsg.value = "Izin akses lokasi ditolak. Aktifkan di pengaturan browser.";
            break;
        case error.POSITION_UNAVAILABLE:
            errorMsg.value = "Informasi lokasi tidak tersedia. Coba lagi.";
            break;
        case error.TIMEOUT:
            errorMsg.value = "Waktu permintaan lokasi habis.";
            break;
        default:
            errorMsg.value = "Terjadi kesalahan saat mengambil lokasi.";
            break;
    }
    status.value = "Gagal Mendapatkan Lokasi";
};

// Fungsi untuk mengirim data absensi ke backend
const submitAbsensi = async () => {
    if (!photoFile.value || !userCoords.value || !isWithinZone.value) {
        alert('Pastikan Anda berada di dalam zona dan sudah mengambil foto.');
        return;
    }

    isSubmitting.value = true;
    const formData = new FormData();
    formData.append('foto', photoFile.value, 'absensi.png');
    formData.append('lat', userCoords.value.latitude);
    formData.append('lng', userCoords.value.longitude);

    try {
        await axios.post(route('api.absen.siswa'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        // Jika berhasil, redirect ke dashboard dengan pesan sukses
        router.visit(route('dashboard'), {
            // Anda bisa menambahkan pesan flash di sini jika sudah disetup
        });
    } catch (error) {
        console.error('Gagal mengirim absensi:', error.response?.data);
        alert(`Gagal mengirim absensi: ${error.response?.data?.message || 'Error tidak diketahui'}`);
    } finally {
        isSubmitting.value = false;
    }
};

// Lifecycle hooks
onMounted(() => {
    if (!props.zonaAktif) {
        status.value = 'Gagal';
        errorMsg.value = 'Tidak ada zona absensi yang aktif yang dikonfigurasi oleh admin.';
        return;
    }
    
    startCamera();

    if (navigator.geolocation) {
        locationWatcher = navigator.geolocation.watchPosition(
            handleLocationUpdate,
            handleLocationError,
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    } else {
        status.value = 'Gagal';
        errorMsg.value = "Browser Anda tidak mendukung Geolocation.";
    }
});

onUnmounted(() => {
    if (locationWatcher) {
        navigator.geolocation.clearWatch(locationWatcher);
    }
    // Hentikan stream kamera jika komponen dilepas
    const video = videoRef.value;
    if (video && video.srcObject) {
        const stream = video.srcObject;
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }
});
</script>

<template>
    <Head title="Absensi GPS" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Absensi Berbasis Lokasi</h2>
        </template>
        
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
            <!-- Status Box -->
            <div class="mb-6 p-4 rounded-lg text-center" :class="{
                'bg-green-100 text-green-800': isWithinZone && !errorMsg,
                'bg-red-100 text-red-800': !isWithinZone || errorMsg,
                'bg-yellow-100 text-yellow-800': status.includes('Mencari')
            }">
                <h3 class="font-bold text-lg">{{ status }}</h3>
                <p v-if="distance !== null" class="text-sm">Jarak Anda dari pusat zona: {{ distance }} meter</p>
                <p v-if="errorMsg" class="mt-2 font-semibold">{{ errorMsg }}</p>
            </div>

            <!-- Kamera & Foto Preview -->
            <div class="bg-gray-200 rounded-lg overflow-hidden aspect-video mb-4 flex items-center justify-center">
                <div v-if="photoPreview">
                    <img :src="photoPreview" alt="Foto Absensi" class="w-full h-full object-cover">
                </div>
                <div v-else class="w-full h-full relative">
                    <video ref="videoRef" autoplay playsinline class="w-full h-full object-cover"></video>
                    <button @click="takePhoto" class="absolute bottom-4 left-1/2 -translate-x-1/2 w-16 h-16 bg-white rounded-full border-4 border-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"></button>
                </div>
            </div>
             <canvas ref="canvasRef" class="hidden"></canvas>


            <!-- Tombol Aksi -->
            <div class="flex flex-col items-center gap-4">
                 <button v-if="photoPreview && !isSubmitting" @click="retakePhoto" class="w-full px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg shadow-md hover:bg-gray-600">
                    Ambil Ulang Foto
                </button>
                <button 
                    @click="submitAbsensi" 
                    :disabled="!isWithinZone || !photoFile || isSubmitting"
                    class="w-full px-6 py-4 text-white font-bold rounded-lg shadow-lg transition-colors"
                    :class="{
                        'bg-blue-600 hover:bg-blue-700': isWithinZone && photoFile,
                        'bg-gray-400 cursor-not-allowed': !isWithinZone || !photoFile,
                        'bg-green-500': isSubmitting
                    }"
                >
                    {{ isSubmitting ? 'Mengirim...' : 'Kirim Absensi Sekarang' }}
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
