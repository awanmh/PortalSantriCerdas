<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import PanelLink from '@/Components/PanelLink.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    UserGroupIcon,
    MapPinIcon,
    ServerIcon,
    Cog6ToothIcon,
    AcademicCapIcon,
    BuildingOffice2Icon,
    CalendarDaysIcon,
    DocumentChartBarIcon,
} from "@heroicons/vue/24/outline";

// Menerima props dari DashboardController
const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
});

// Mengambil data user dari props global Inertia
const user = computed(() => usePage().props.auth.user);
const userName = computed(() => user.value?.name || 'Admin');
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <!-- Header Halaman -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard Admin IT
            </h2>
        </template>
        
        <!-- Konten Utama -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 space-y-8">
                        
                        <!-- Welcome Message -->
                        <section>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                Selamat Datang, {{ userName }}!
                            </h1>
                            <p class="mt-1 text-md text-gray-600 dark:text-gray-400">
                                Ini adalah pusat kendali sistem Anda. Kelola semua data master dari sini.
                            </p>
                        </section>

                        <!-- Statistik Sistem -->
                        <section>
                            <h3 class="flex items-center mb-4 text-xl font-semibold text-gray-700 dark:text-gray-300">
                                <ServerIcon class="w-6 h-6 mr-3" />
                                Statistik Sistem
                            </h3>
                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                                <StatCard label="Total Pengguna" :value="stats.total_pengguna" color="text-blue-500" />
                                <StatCard label="Siswa" :value="stats.total_siswa" color="text-green-500" />
                                <!-- PERBAIKAN: Menggunakan 'total_guru_bk' -->
                                <StatCard label="Guru & BK" :value="stats.total_guru_bk" color="text-yellow-500" />
                                <StatCard label="Jurusan" :value="stats.total_jurusan" color="text-indigo-500" />
                                <StatCard label="Kelas" :value="stats.total_kelas" color="text-sky-500" />
                                <!-- PERBAIKAN: Menggunakan 'total_jadwal_rutin' -->
                                <StatCard label="Jadwal Pelajaran" :value="stats.total_jadwal_rutin" color="text-rose-500" />
                                <!-- PERBAIKAN: Menggunakan 'total_zona_aktif' -->
                                <StatCard label="Zona Aktif" :value="stats.total_zona_aktif" color="text-purple-500" />
                            </div>
                        </section>

                        <!-- Panel Manajemen -->
                        <section>
                            <h3 class="flex items-center mb-4 text-xl font-semibold text-gray-700 dark:text-gray-300">
                                <Cog6ToothIcon class="w-6 h-6 mr-3" />
                                Panel Manajemen
                            </h3>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <PanelLink
                                    :href="route('admin.users.index')"
                                    title="Manajemen Pengguna"
                                    description="Kelola akun siswa, guru, dan staf."
                                    color="text-blue-500"
                                >
                                    <template #icon><UserGroupIcon class="w-8 h-8" /></template>
                                </PanelLink>

                                <PanelLink
                                    :href="route('admin.jurusan.index')"
                                    title="Manajemen Jurusan"
                                    description="Atur semua program keahlian."
                                    color="text-indigo-500"
                                >
                                    <template #icon><AcademicCapIcon class="w-8 h-8" /></template>
                                </PanelLink>

                                <PanelLink
                                    :href="route('admin.kelas.index')"
                                    title="Manajemen Kelas"
                                    description="Buat dan kelola semua ruang kelas."
                                    color="text-sky-500"
                                >
                                    <template #icon><BuildingOffice2Icon class="w-8 h-8" /></template>
                                </PanelLink>

                                <PanelLink
                                    :href="route('admin.jadwal.index')"
                                    title="Manajemen Jadwal"
                                    description="Susun jadwal pelajaran per kelas."
                                    color="text-rose-500"
                                >
                                    <template #icon><CalendarDaysIcon class="w-8 h-8" /></template>
                                </PanelLink>

                                <PanelLink
                                    :href="route('admin.laporan.absensi.index', { type: 'siswa' })"
                                    title="Laporan Absensi"
                                    description="Lihat dan filter rekap kehadiran."
                                    color="text-orange-500"
                                >
                                    <template #icon><DocumentChartBarIcon class="w-8 h-8" /></template>
                                </PanelLink>

                                <PanelLink
                                    :href="route('admin.zona.index')"
                                    title="Manajemen Zona"
                                    description="Atur zona absensi berbasis lokasi."
                                    color="text-green-500"
                                >
                                    <template #icon><MapPinIcon class="w-8 h-8" /></template>
                                </PanelLink>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
