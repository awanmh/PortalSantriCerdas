<script setup>
import { ref, watch, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

// Menerima props dari LaporanAbsensiController
const props = defineProps({
    filters: Object,
    laporanData: Array, // Sesuai dengan controller
    kelasOptions: Array,
});

// State reaktif untuk form filter
const filtersForm = ref({
    tanggal: props.filters.tanggal || new Date().toISOString().split('T')[0],
    kelas_id: props.filters.kelas_id,
    type: props.filters.type || 'siswa',
});

// Computed property untuk menentukan apakah filter kelas_id harus ditampilkan
const showKelasFilter = computed(() => filtersForm.value.type === 'siswa');

// Fungsi untuk mengirim request filter
const applyFilters = () => {
    router.get(route(route().current()), filtersForm.value, {
        preserveState: true,
        replace: true,
    });
};

// Mengawasi perubahan pada form filter dan mengirim request baru ke server
watch(filtersForm, debounce((value) => {
    if (value.type === 'guru') {
        value.kelas_id = null;
    }
    applyFilters();
}, 300), { deep: true });

// Fungsi untuk memberikan warna badge berdasarkan status absensi
const statusColor = (status) => {
    if (!status) return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    const colors = {
        hadir: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        masuk: 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        pulang: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
        'hadir penuh': 'bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100',
        sakit: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100',
        izin: 'bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100',
        alfa: 'bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100',
    };
    return colors[status.toLowerCase()] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
};

// Computed properties untuk menentukan header tabel dinamis
const tableHeaders = computed(() => {
    if (filtersForm.value.type === 'siswa') {
        return [
            { label: 'Nama Siswa', key: 'nama' },
            { label: 'Status', key: 'status' },
            { label: 'Waktu Absen', key: 'waktu_absensi' },
            { label: 'Mata Pelajaran', key: 'mata_pelajaran' },
            { label: 'Keterangan', key: 'keterangan' },
        ];
    } else if (filtersForm.value.type === 'guru') {
        return [
            { label: 'Nama Guru', key: 'nama_guru' },
            { label: 'Tanggal', key: 'tanggal' },
            { label: 'Waktu Masuk', key: 'waktu_masuk' },
            { label: 'Waktu Pulang', key: 'waktu_pulang' },
            { label: 'Status', key: 'status' },
            { label: 'Keterangan', key: 'keterangan' },
        ];
    }
    return [];
});

// Computed property untuk teks placeholder jika tidak ada data
const noDataMessage = computed(() => {
    if (filtersForm.value.type === 'siswa' && !filtersForm.value.kelas_id) {
        return 'Silakan pilih kelas untuk menampilkan laporan absensi siswa.';
    }
    return `Tidak ada data absensi ${filtersForm.value.type} yang ditemukan untuk tanggal ini.`;
});
</script>

<template>
    <Head title="Laporan Absensi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Laporan Absensi</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 space-y-6">

                        
<div class="mb-6 flex justify-center space-x-2 border-b pb-4 dark:border-gray-700">
                            <button
                                @click="filtersForm.type = 'siswa'"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                                    filtersForm.type === 'siswa'
                                        ? 'bg-indigo-600 text-white shadow'
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Laporan Siswa
                            </button>
                            <button
                                @click="filtersForm.type = 'guru'"
                                :class="[
                                    'px-4 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                                    filtersForm.type === 'guru'
                                        ? 'bg-indigo-600 text-white shadow'
                                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
                                ]"
                            >
                                Laporan Guru
                            </button>
                        </div>

                        
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-700">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Pilih Tanggal</label>
                                <input
                                    id="tanggal"
                                    v-model="filtersForm.tanggal"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100"
                                />
                            </div>
                            <div v-if="showKelasFilter">
                                <label for="kelas" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Pilih Kelas</label>
                                <select
                                    id="kelas"
                                    v-model="filtersForm.kelas_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100"
                                >
                                    <option :value="null">-- Semua Kelas --</option>
                                    <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">
                                        {{ kelas.nama_kelas }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        
<div class="flex justify-end gap-2">
                            <SecondaryButton @click="() => console.log('Cetak laporan ini')" :disabled="!laporanData.length">
                                Cetak PDF
                            </SecondaryButton>
                            <PrimaryButton @click="() => console.log('Export laporan ini')" :disabled="!laporanData.length">
                                Export Excel
                            </PrimaryButton>
                        </div>

                        
<div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th 
                                            v-for="header in tableHeaders" 
                                            :key="header.key" 
                                            scope="col" 
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider"
                                        >
                                            {{ header.label }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <template v-if="laporanData.length > 0">
                                        <tr v-for="item in laporanData" :key="item.id">
                                            <td 
                                                v-for="header in tableHeaders" 
                                                :key="header.key" 
                                                class="px-6 py-4 whitespace-nowrap text-sm"
                                                :class="{ 'font-medium text-gray-900 dark:text-gray-100': header.key === 'nama' || header.key === 'nama_guru', 'text-gray-500 dark:text-gray-400': header.key !== 'status' }"
                                            >
                                                <span v-if="header.key === 'status'" :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize', statusColor(item.status)]">
                                                    {{ item.status }}
                                                </span>
                                                <span v-else>{{ item[header.key] }}</span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr v-else>
                                        <td :colspan="tableHeaders.length" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                            {{ noDataMessage }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>