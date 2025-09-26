<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3'; // Tambahkan usePage
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ClipboardDocumentListIcon, UserGroupIcon, BookOpenIcon, CheckCircleIcon, CheckIcon, XCircleIcon } from '@heroicons/vue/24/outline';
import axios from 'axios';

// --- PROPS ---
const props = defineProps({
    jadwalMengajarHariIni: {
        type: Array,
        default: () => [],
    },
    todayDate: String,
    errors: Object,
});

// --- STATE MANAGEMENT ---
const selectedJadwalId = ref(null);
const kelasData = ref(null);
const loadingData = ref(false);
const flash = usePage().props.flash; // Akses flash message dari Inertia

const form = useForm({
    jadwal_id: null,
    absensi_data: [],
});

// --- DATA & HELPERS ---
const statusOptions = [
    { value: 'hadir', label: 'Hadir', class: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100' },
    { value: 'sakit', label: 'Sakit', class: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100' },
    { value: 'izin', label: 'Izin', class: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100' },
    { value: 'alfa', label: 'Alfa', class: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100' },
];

const getStatusColor = (statusValue) => {
    const option = statusOptions.find(opt => opt.value === statusValue);
    return option ? option.class : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
};

// --- FUNGSI-FUNGSI ---
const fetchSiswaData = async () => {
    if (!selectedJadwalId.value) return;

    loadingData.value = true;
    kelasData.value = null;
    form.reset();

    try {
        const response = await axios.get(route('guru.absensi.siswa.data', { jadwal: selectedJadwalId.value }));
        kelasData.value = response.data;
        
        form.absensi_data = response.data.siswa.map(s => ({
            user_id: s.id,
            status: s.status,
            keterangan: s.keterangan,
        }));
        form.jadwal_id = selectedJadwalId.value;
    } catch (error) {
        console.error('Error fetching student data:', error);
        alert('Gagal mengambil data siswa. Silakan periksa konsol untuk detail.');
    } finally {
        loadingData.value = false;
    }
};

watch(selectedJadwalId, fetchSiswaData);

/**
 * Mengirim data absensi ke server.
 */
const submitAbsensi = () => {
    // Inertia akan menangani redirect dan flash message secara otomatis.
    form.post(route('guru.absensi.siswa.store'), {
        preserveScroll: true,
        // onSuccess tidak diperlukan lagi, flash message akan menangani notifikasi
    });
};
</script>

<template>
    <Head title="Cetak Absensi Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Cetak Absensi Siswa
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">

                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-bold flex items-center">
                                <ClipboardDocumentListIcon class="h-6 w-6 mr-2 text-indigo-500" />
                                Absensi Kelas Hari Ini ({{ todayDate }})
                            </h3>
                        </div>

                        <!-- Menampilkan Flash Message dari Controller -->
                        <div v-if="flash.success" class="mb-4 flex items-center gap-2 rounded-md bg-green-50 dark:bg-green-900 border border-green-200 p-3 text-green-700 dark:text-green-300">
                            <CheckCircleIcon class="h-5 w-5" />
                            <span>{{ flash.success }}</span>
                        </div>
                        <div v-if="flash.error" class="mb-4 flex items-center gap-2 rounded-md bg-red-50 dark:bg-red-900 border border-red-200 p-3 text-red-700 dark:text-red-300">
                            <XCircleIcon class="h-5 w-5" />
                            <span>{{ flash.error }}</span>
                        </div>

                        <div class="p-4 border dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <label for="jadwal_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Jadwal Mengajar:</label>
                            <select
                                id="jadwal_select"
                                v-model="selectedJadwalId"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:text-gray-200"
                            >
                                <option :value="null" disabled>-- Pilih Kelas dari Jadwal Anda --</option>
                                <option v-for="jadwal in jadwalMengajarHariIni" :key="jadwal.id" :value="jadwal.id">
                                    {{ jadwal.mata_pelajaran }} - {{ jadwal.kelas.nama_kelas }} ({{ jadwal.jam_mulai.slice(0,5) }} - {{ jadwal.jam_selesai.slice(0,5) }})
                                </option>
                            </select>
                            <p v-if="jadwalMengajarHariIni.length === 0" class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tidak ada jadwal mengajar untuk Anda hari ini.</p>
                        </div>

                        <div v-if="loadingData" class="text-center py-8 text-indigo-500">
                            <svg class="animate-spin h-8 w-8 text-indigo-500 mx-auto mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <p>Memuat data siswa...</p>
                        </div>

                        <div v-if="kelasData && !loadingData">
                            <div class="mb-4 text-lg font-semibold text-gray-800 dark:text-gray-200">
                                <p class="flex items-center"><UserGroupIcon class="h-5 w-5 mr-2 text-purple-500" /> Kelas: {{ kelasData.kelas_name }}</p>
                                <p class="flex items-center"><BookOpenIcon class="h-5 w-5 mr-2 text-cyan-500" /> Mata Pelajaran: {{ kelasData.mata_pelajaran }}</p>
                            </div>

                            <form @submit.prevent="submitAbsensi">
                                <div class="overflow-x-auto border dark:border-gray-700 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Siswa</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Absensi</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keterangan (Opsional)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            <tr v-for="(siswa, index) in form.absensi_data" :key="siswa.user_id">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ index + 1 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ kelasData.siswa[index].name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <select
                                                        v-model="siswa.status"
                                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200"
                                                        :class="getStatusColor(siswa.status)"
                                                    >
                                                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                                            {{ option.label }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <input
                                                        type="text"
                                                        v-model="siswa.keterangan"
                                                        placeholder="Contoh: Demam, Izin Keluarga"
                                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:text-gray-200"
                                                    />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-6 flex justify-end">
                                    <PrimaryButton
                                        type="submit"
                                        :disabled="form.processing || !selectedJadwalId"
                                        class="inline-flex items-center"
                                    >
                                        <CheckIcon class="h-5 w-5 mr-2" />
                                        Simpan Absensi
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        <div v-else-if="!selectedJadwalId && !loadingData && jadwalMengajarHariIni.length > 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>Silakan pilih jadwal mengajar untuk mencatat absensi siswa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>