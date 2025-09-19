<script setup>
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import { QrcodeStream } from 'vue-qrcode-reader';
// --- PERBAIKAN DI SINI: QrCodeIcon ditambahkan ke dalam import ---
import { CameraIcon, ExclamationTriangleIcon, MapPinIcon, QrCodeIcon } from '@heroicons/vue/24/outline';

// --- PROPS & EMITS ---
// Menerima data dari komponen induk (Guru.vue)
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

// Mendefinisikan event yang akan dikirim kembali ke induk
const emit = defineEmits(['close', 'scan-success']);

// --- STATE MANAGEMENT ---
const isSubmitting = ref(false);
const statusMessage = ref("Arahkan kamera ke QR Code Absensi.");
const errorMessage = ref("");
const cameraError = ref(false);

// --- FUNGSI-FUNGSI ---
/**
 * Fungsi ini dipanggil secara otomatis oleh QrcodeStream saat QR code terdeteksi.
 * @param {Array} detectedCodes - Array berisi data dari QR code yang terdeteksi.
 */
const onDetect = (detectedCodes) => {
    if (isSubmitting.value) return; // Mencegah pemindaian ganda

    isSubmitting.value = true;
    errorMessage.value = "";
    statusMessage.value = "✅ QR Code terdeteksi. Mendapatkan lokasi GPS...";

    const qrData = detectedCodes[0].rawValue;

    // 1. Periksa dukungan Geolocation
    if (!navigator.geolocation) {
        errorMessage.value = "GPS tidak didukung oleh browser ini.";
        isSubmitting.value = false;
        return;
    }

    // 2. Ambil lokasi GPS saat ini
    navigator.geolocation.getCurrentPosition(
        (position) => {
            statusMessage.value = "✅ Lokasi berhasil didapat. Mengirim data...";
            
            // 3. Siapkan payload untuk dikirim ke API
            const payload = {
                qr_data: qrData,
                tipe: props.tipe,
                location: {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                }
            };

            // 4. Kirim data kembali ke komponen induk (Guru.vue)
            emit('scan-success', payload);
        },
        (geoError) => {
            errorMessage.value = `Gagal mendapatkan lokasi: ${geoError.message}`;
            isSubmitting.value = false;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
};

/**
 * Menangani error saat kamera gagal diinisialisasi.
 */
const onCameraError = (error) => {
    cameraError.value = true;
    if (error.name === 'NotAllowedError') {
        errorMessage.value = "Izin akses kamera ditolak. Silakan izinkan di pengaturan browser Anda.";
    } else {
        errorMessage.value = "Kamera tidak dapat diakses atau tidak ditemukan.";
    }
};

/**
 * Mereset state saat modal ditutup.
 */
const closeModal = () => {
    emit('close');
};

// Mengawasi prop 'show' untuk mereset state setiap kali modal dibuka
watch(() => props.show, (newVal) => {
    if (newVal) {
        isSubmitting.value = false;
        statusMessage.value = "Arahkan kamera ke QR Code Absensi.";
        errorMessage.value = "";
        cameraError.value = false;
    }
});

</script>

<template>
    <Modal :show="show" @close="closeModal" :max-width="'sm'">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                <QrCodeIcon class="w-6 h-6 mr-2" />
                Pindai QR Code Absensi ({{ tipe === 'masuk' ? 'Masuk' : 'Pulang' }})
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Pastikan kamera memiliki izin, cahaya cukup, dan Anda berada di dalam zona.
            </p>

            <!-- Area Scanner & Status -->
            <div class="mt-4 p-4 border-2 border-dashed rounded-lg text-center relative">
                <!-- Tampilan Kamera -->
                <QrcodeStream
                    v-if="!cameraError && !isSubmitting"
                    @detect="onDetect"
                    @error="onCameraError"
                />

                <!-- Indikator Loading & Error -->
                <div v-if="cameraError || isSubmitting" class="flex flex-col items-center justify-center h-48">
                    <ExclamationTriangleIcon v-if="cameraError" class="w-12 h-12 text-red-400 mb-2" />
                    <MapPinIcon v-if="isSubmitting" class="w-12 h-12 text-blue-400 mb-2 animate-pulse" />
                    <p class="text-sm font-semibold" :class="errorMessage ? 'text-red-600' : 'text-blue-600'">
                        {{ errorMessage || statusMessage }}
                    </p>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-6 flex justify-end">
                <button
                    @click="closeModal"
                    type="button"
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Tutup
                </button>
            </div>
        </div>
    </Modal>
</template>

