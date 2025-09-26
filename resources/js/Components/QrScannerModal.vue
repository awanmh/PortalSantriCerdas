<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import { QrcodeStream } from 'vue-qrcode-reader';
// Ikon yang akan kita gunakan untuk UI yang lebih baik
import { ArrowPathIcon, ExclamationTriangleIcon, MapPinIcon, QrCodeIcon } from '@heroicons/vue/24/outline';

// --- PROPS & EMITS ---
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    tipe: {
        type: String, // 'masuk' atau 'pulang'
        required: true,
    }
});
const emit = defineEmits(['close', 'scan-success']);

// --- STATE MANAGEMENT ---
const isProcessing = ref(false); // Mencegah pemindaian ganda saat sedang proses
const statusMessage = ref("Arahkan kamera ke QR Code Absensi.");
const errorMessage = ref("");

// --- STATE UNTUK FITUR KAMERA ---
const selectedDevice = ref(null); // Kamera yang sedang aktif
const devices = ref([]); // Daftar semua kamera yang tersedia

/**
 * Dipanggil saat kamera berhasil diinisialisasi.
 * Fungsi ini akan mendaftar semua kamera yang tersedia di perangkat.
 */
const onCameraReady = async () => {
    errorMessage.value = ''; // Hapus pesan error lama
    try {
        const allDevices = await navigator.mediaDevices.enumerateDevices();
        devices.value = allDevices.filter(({ kind }) => kind === 'videoinput');
        
        if (devices.value.length > 0 && !selectedDevice.value) {
            // Pilih kamera belakang (environment) sebagai default jika ada
            const rearCamera = devices.value.find(device => device.label.toLowerCase().includes('back') || device.label.toLowerCase().includes('environment'));
            selectedDevice.value = rearCamera || devices.value[0];
        }
    } catch (error) {
        console.error("Tidak bisa mengakses perangkat kamera:", error);
        errorMessage.value = "Tidak bisa mengakses daftar kamera. Pastikan izin telah diberikan.";
    }
};

/**
 * Beralih ke kamera berikutnya yang tersedia (depan/belakang).
 */
const switchCamera = () => {
    if (devices.value.length > 1) {
        const currentIndex = devices.value.findIndex(device => device.deviceId === selectedDevice.value.deviceId);
        const nextIndex = (currentIndex + 1) % devices.value.length;
        selectedDevice.value = devices.value[nextIndex];
    }
};

/**
 * Dipanggil saat QR code berhasil terdeteksi.
 */
const onDetect = (detectedCodes) => {
    if (isProcessing.value || detectedCodes.length === 0) return;

    isProcessing.value = true;
    statusMessage.value = "✅ QR Code terdeteksi. Mendapatkan lokasi GPS...";
    
    const qrData = detectedCodes[0].rawValue;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            statusMessage.value = "✅ Lokasi didapat. Mengirim data ke server...";
            const payload = {
                qr_data: qrData,
                tipe_absensi: props.tipe,
                location: {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                }
            };
            // Kirim data kembali ke Dashboard Guru untuk diproses
            emit('scan-success', payload);
        },
        (geoError) => {
            errorMessage.value = `Gagal mendapatkan lokasi GPS: ${geoError.message}`;
            isProcessing.value = false; // Izinkan scan ulang jika GPS gagal
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
};

/**
 * Menangani berbagai jenis error dari kamera.
 */
const onError = (error) => {
    if (error.name === 'NotAllowedError') {
        errorMessage.value = 'Izin akses kamera ditolak. Mohon izinkan akses kamera di pengaturan browser Anda.';
    } else if (error.name === 'NotFoundError') {
        errorMessage.value = 'Tidak ada kamera yang ditemukan di perangkat ini.';
    } else if (error.name === 'NotReadableError') {
        errorMessage.value = 'Kamera sedang digunakan oleh aplikasi lain.';
    } else {
        errorMessage.value = `Terjadi error pada kamera: ${error.name}`;
    }
};

const closeModal = () => {
    emit('close');
};

// Mereset state setiap kali modal dibuka
watch(() => props.show, (isVisible) => {
    if (isVisible) {
        isProcessing.value = false;
        statusMessage.value = "Arahkan kamera ke QR Code Absensi.";
        errorMessage.value = "";
    }
});
</script>

<template>
    <Modal :show="show" @close="closeModal" :max-width="'sm'">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                <QrCodeIcon class="w-6 h-6 mr-2 text-indigo-500" />
                Absensi {{ tipe === 'masuk' ? 'Masuk' : 'Pulang' }}
            </h2>

            <div class="mt-4 relative w-full aspect-square bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center">
                <!-- Tampilan Kamera -->
                <qrcode-stream 
                    :constraints="{ deviceId: selectedDevice?.deviceId }"
                    @detect="onDetect" 
                    @error="onError"
                    @camera-on="onCameraReady"
                    v-if="show"
                    class="transition-opacity duration-300"
                    :class="{ 'opacity-20': isProcessing }"
                />
                
                <!-- Overlay untuk Status & Error -->
                <div 
                    v-if="errorMessage || isProcessing" 
                    class="absolute inset-0 bg-black bg-opacity-75 flex flex-col items-center justify-center p-4 text-center"
                >
                    <ExclamationTriangleIcon v-if="errorMessage" class="w-12 h-12 text-red-400 mb-2" />
                    <MapPinIcon v-if="isProcessing && !errorMessage" class="w-12 h-12 text-blue-400 mb-2 animate-pulse" />
                    <p class="text-white font-semibold" :class="{ 'text-red-400': errorMessage }">
                        {{ errorMessage || statusMessage }}
                    </p>
                </div>
            </div>
            
            <div class="mt-4 flex justify-between items-center">
                <!-- Tombol Ganti Kamera (hanya muncul jika ada > 1 kamera) -->
                <button 
                    v-if="devices.length > 1"
                    @click="switchCamera"
                    :disabled="isProcessing"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 disabled:opacity-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    <ArrowPathIcon class="w-5 h-5" />
                    Ganti Kamera
                </button>

                <!-- Tombol Batal -->
                <button @click="closeModal" class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">
                    Tutup
                </button>
            </div>
        </div>
    </Modal>
</template>

