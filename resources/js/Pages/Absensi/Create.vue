<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

// Menerima data jadwal spesifik dari AbsensiController
const props = defineProps({
    jadwal: {
        type: Object,
        required: true,
    },
});

const statusTerpilih = ref('hadir');
const keterangan = ref('');

// Menggunakan useForm dari Inertia untuk menangani form submission
const form = useForm({
    status: 'hadir',
    keterangan: '',
});

// Fungsi untuk mengirim data absensi ke server
const submitAbsensi = () => {
    form.status = statusTerpilih.value;
    form.keterangan = keterangan.value;

    form.post(route('absen.store', props.jadwal.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Form akan otomatis di-reset jika sukses
        },
    });
};

</script>

<template>
    <Head :title="`Absensi: ${jadwal.mata_pelajaran}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Formulir Kehadiran</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 space-y-6">
                        
                        <!-- Detail Jadwal Pelajaran -->
                        <section class="p-6 border rounded-lg bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">Detail Jadwal Pelajaran</h3>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-md">
                                <div>
                                    <p class="font-semibold">Mata Pelajaran:</p>
                                    <p>{{ jadwal.mata_pelajaran }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Guru Pengajar:</p>
                                    <p>{{ jadwal.guru?.name || 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Kelas:</p>
                                    <p>{{ jadwal.kelas.nama_kelas }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Waktu:</p>
                                    <p>{{ jadwal.jam_mulai.slice(0, 5) }} - {{ jadwal.jam_selesai.slice(0, 5) }}</p>
                                </div>
                            </div>
                        </section>

                        <!-- Pilihan Status Kehadiran -->
                        <section>
                             <h3 class="text-lg font-bold text-gray-800 mb-4">Konfirmasi Kehadiran Anda</h3>
                             <div class="flex flex-col sm:flex-row gap-4">
                                <button
                                    @click="statusTerpilih = 'hadir'"
                                    :class="[
                                        'w-full text-center px-6 py-3 border rounded-lg font-semibold transition',
                                        statusTerpilih === 'hadir' ? 'bg-green-600 text-white border-green-600 shadow-lg' : 'bg-white text-green-700 border-gray-300 hover:bg-green-50'
                                    ]">
                                    Hadir
                                </button>
                                <button
                                    @click="statusTerpilih = 'izin'"
                                     :class="[
                                        'w-full text-center px-6 py-3 border rounded-lg font-semibold transition',
                                        statusTerpilih === 'izin' ? 'bg-yellow-500 text-white border-yellow-500 shadow-lg' : 'bg-white text-yellow-700 border-gray-300 hover:bg-yellow-50'
                                    ]">
                                    Izin
                                </button>
                                <button
                                    @click="statusTerpilih = 'sakit'"
                                    :class="[
                                        'w-full text-center px-6 py-3 border rounded-lg font-semibold transition',
                                        statusTerpilih === 'sakit' ? 'bg-red-500 text-white border-red-500 shadow-lg' : 'bg-white text-red-700 border-gray-300 hover:bg-red-50'
                                    ]">
                                    Sakit
                                </button>
                             </div>
                        </section>

                        <!-- Form Keterangan (jika Izin atau Sakit) -->
                        <section v-if="statusTerpilih === 'izin' || statusTerpilih === 'sakit'">
                            <label for="keterangan" class="block font-medium text-md text-gray-700">
                                Alasan {{ statusTerpilih === 'izin' ? 'Izin' : 'Sakit' }}
                            </label>
                            <textarea
                                id="keterangan"
                                v-model="keterangan"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                                placeholder="Tuliskan alasan singkat di sini..."
                            ></textarea>
                            <p v-if="form.errors.keterangan" class="text-sm text-red-600 mt-1">{{ form.errors.keterangan }}</p>
                        </section>

                        <!-- Tombol Submit -->
                        <div class="flex justify-end">
                            <PrimaryButton @click="submitAbsensi" :disabled="form.processing">
                                {{ form.processing ? 'Menyimpan...' : 'Kirim Absensi' }}
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
