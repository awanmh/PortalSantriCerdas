<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { debounce } from 'lodash';

// Menerima props dari LaporanAbsensiController
const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    laporan: {
        type: Array,
        required: true,
    },
    kelasOptions: {
        type: Array,
        required: true,
    },
});

// State reaktif untuk form filter, diinisialisasi dengan nilai dari controller
const filtersForm = ref({
    tanggal: props.filters.tanggal,
    kelas_id: props.filters.kelas_id,
});

// Fungsi untuk memberikan warna badge berdasarkan status absensi
const statusColor = (status) => {
    const colors = {
        hadir: 'bg-green-100 text-green-800',
        sakit: 'bg-yellow-100 text-yellow-800',
        izin: 'bg-blue-100 text-blue-800',
        alfa: 'bg-red-100 text-red-800',
    };
    return colors[status.toLowerCase()] || 'bg-gray-100 text-gray-800';
};

// Mengawasi perubahan pada form filter dan mengirim request baru ke server
// debounce digunakan untuk menunda eksekusi agar tidak mengirim request di setiap ketikan,
// melainkan setelah pengguna berhenti mengetik selama 300ms.
watch(filtersForm, debounce((value) => {
    router.get(route('admin.laporan.absensi.index'), value, {
        preserveState: true,
        replace: true,
    });
}, 300), { deep: true });

</script>

<template>
    <Head title="Laporan Absensi Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Absensi Harian Siswa</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-6">

                        <!-- Panel Filter -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border rounded-lg bg-gray-50">
                            <div>
                                <label for="tanggal" class="block text-sm font-medium text-gray-700">Pilih Tanggal</label>
                                <input
                                    id="tanggal"
                                    v-model="filtersForm.tanggal"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                />
                            </div>
                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700">Pilih Kelas</label>
                                <select
                                    id="kelas"
                                    v-model="filtersForm.kelas_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                    <option :value="null">-- Semua Kelas --</option>
                                    <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">
                                        {{ kelas.nama_kelas }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Tabel Laporan -->
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Absen</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <template v-if="filters.kelas_id && laporan.length > 0">
                                        <tr v-for="item in laporan" :key="item.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.nama }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span :class="['px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize', statusColor(item.status)]">
                                                    {{ item.status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.waktu_absensi }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.mata_pelajaran }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.keterangan }}</td>
                                        </tr>
                                    </template>
                                    <tr v-else>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                            <span v-if="!filters.kelas_id">Silakan pilih kelas untuk menampilkan laporan.</span>
                                            <span v-else>Tidak ada data absensi yang ditemukan untuk tanggal dan kelas yang dipilih.</span>
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

