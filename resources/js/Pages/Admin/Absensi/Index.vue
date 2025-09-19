<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { CheckCircleIcon, CameraIcon } from '@heroicons/vue/24/outline';

// Menerima props dari AbsensiController
const props = defineProps({
    jadwal: {
        type: Object,
        required: true,
    },
    sudahAbsen: {
        type: Boolean,
        required: true,
    }
});

// Menggunakan useForm dari Inertia untuk state management
const form = useForm({
    status: '',
    keterangan: '',
    bukti_foto: null, // Untuk menyimpan file foto (Blob)
});

// --- State untuk Webcam ---
const video = ref(null);
const canvas = ref(null);
const snapshot = ref(null); // Untuk preview gambar (data URL)
const isCameraOn = ref(false);
const cameraError = ref('');

// --- Fungsi untuk Webcam ---
const startCamera = async () => {
    snapshot.value = null;
    form.bukti_foto = null;
    cameraError.value = '';

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.value.srcObject = stream;
            isCameraOn.value = true;
        } catch (err) {
            cameraError.value = "Gagal mengakses kamera. Pastikan Anda memberikan izin pada browser.";
            console.error(err);
        }
    } else {
        cameraError.value = "Browser Anda tidak mendukung akses kamera.";
    }
};

const takeSnapshot = () => {
    const context = canvas.value.getContext('2d');
    canvas.value.width = video.value.videoWidth;
    canvas.value.height = video.value.videoHeight;
    context.drawImage(video.value, 0, 0, canvas.value.width, canvas.value.height);
    
    // Konversi canvas ke Blob (format file) untuk di-upload
    canvas.value.toBlob(blob => {
        form.bukti_foto = new File([blob], "bukti_kehadiran.jpg", { type: "image/jpeg" });
    }, 'image/jpeg', 0.9); // Kualitas gambar 90%

    snapshot.value = canvas.value.toDataURL('image/jpeg');
    stopCamera();
};

const stopCamera = () => {
    const stream = video.value.srcObject;
    if (stream) {
        const tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
    }
    isCameraOn.value = false;
};

// --- Fungsi Submit ---
const submitAbsensi = (status) => {
    if (status === 'hadir' && !form.bukti_foto) {
        alert("Anda harus mengambil foto bukti untuk absensi 'Hadir'.");
        return;
    }
    form.status = status;
    form.post(route('absen.store', props.jadwal.id), {
        forceFormData: true, // WAJIB untuk upload file
    });
};
</script>

<template>
    <Head :title="'Absensi - ' + jadwal.mata_pelajaran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Formulir Absensi
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                        <!-- Detail Jadwal -->
                        <div class="pb-4 border-b dark:border-gray-700">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ jadwal.mata_pelajaran }}</h3>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Guru:</strong> {{ jadwal.guru.name }}</p>
                            <p class="text-gray-600 dark:text-gray-400"><strong>Waktu:</strong> {{ jadwal.jam_mulai.slice(0, 5) }} - {{ jadwal.jam_selesai.slice(0, 5) }}</p>
                        </div>

                        <!-- Status Jika Sudah Absen -->
                        <div v-if="sudahAbsen" class="p-4 bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 rounded-lg text-center flex items-center justify-center">
                            <CheckCircleIcon class="w-6 h-6 mr-2" />
                            Anda sudah melakukan absensi untuk jadwal ini.
                        </div>

                        <!-- Form Absensi -->
                        <div v-else class="space-y-6">
                             <!-- Area Webcam -->
                            <div class="border dark:border-gray-700 rounded-lg p-4 space-y-4">
                               <h4 class="font-semibold text-gray-800 dark:text-gray-200">Bukti Kehadiran (Wajib untuk status Hadir)</h4>
                               
                               <div v-if="cameraError" class="p-3 bg-red-100 text-red-700 rounded-md text-sm">
                                   {{ cameraError }}
                               </div>

                                <!-- Tombol Buka Kamera -->
                                <div v-if="!isCameraOn && !snapshot" class="text-center">
                                    <SecondaryButton @click="startCamera" class="inline-flex items-center">
                                        <CameraIcon class="w-5 h-5 mr-2" />
                                        Buka Kamera
                                    </SecondaryButton>
                                </div>

                                <!-- Tampilan Video & Tombol Ambil Foto -->
                                <div v-if="isCameraOn">
                                    <video ref="video" autoplay playsinline class="w-full rounded-md bg-gray-900"></video>
                                    <canvas ref="canvas" class="hidden"></canvas>
                                    <PrimaryButton @click="takeSnapshot" class="w-full mt-2">Ambil Foto</PrimaryButton>
                                </div>
                                
                                <!-- Pratinjau Foto -->
                                <div v-if="snapshot" class="mt-4 text-center">
                                    <p class="text-sm font-medium mb-2">Pratinjau Foto:</p>
                                    <img :src="snapshot" class="mx-auto border-2 dark:border-gray-600 rounded-md max-w-xs" />
                                    <button @click="startCamera" class="mt-2 text-sm text-blue-600 hover:underline dark:text-blue-400">
                                        Ambil Ulang Foto
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Opsi Izin/Sakit -->
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                               <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Keterangan (jika Izin atau Sakit)</h4>
                               <textarea v-model="form.keterangan" rows="3" placeholder="Contoh: Ada acara keluarga." class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"></textarea>
                               <InputError :message="form.errors.keterangan" class="mt-2" />
                            </div>
                            
                            <!-- Tombol Aksi -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t dark:border-gray-700">
                                <PrimaryButton @click="submitAbsensi('hadir')" :disabled="form.processing || !form.bukti_foto" class="bg-green-600 hover:bg-green-700 focus:ring-green-500">Hadir</PrimaryButton>
                                <PrimaryButton @click="submitAbsensi('izin')" :disabled="form.processing" class="bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-500">Izin</PrimaryButton>
                                <PrimaryButton @click="submitAbsensi('sakit')" :disabled="form.processing" class="bg-orange-500 hover:bg-orange-600 focus:ring-orange-500">Sakit</PrimaryButton>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>