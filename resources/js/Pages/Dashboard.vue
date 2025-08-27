<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { UserGroupIcon, AcademicCapIcon, BookmarkSquareIcon, BellIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline';

// Menerima semua data yang dikirim dari DashboardController
defineProps({
    stats: Object,
    pengumuman: Array,
    jadwalHariIni: Array,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center space-x-4">
                        <div class="bg-green-100 p-3 rounded-full">
                            <UserGroupIcon class="h-8 w-8 text-green-700" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500">Jumlah Santri</h3>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.jumlahSantri }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center space-x-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <AcademicCapIcon class="h-8 w-8 text-blue-700" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500">Jumlah Guru</h3>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.jumlahGuru }}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-lg flex items-center space-x-4">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <BookmarkSquareIcon class="h-8 w-8 text-yellow-700" />
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-500">Kelas Aktif</h3>
                            <p class="text-3xl font-bold text-gray-800">{{ stats.kelasAktif }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex items-center space-x-3 mb-4">
                                <BellIcon class="h-6 w-6 text-gray-500" />
                                <h3 class="text-xl font-bold text-gray-800">Pengumuman Terbaru</h3>
                            </div>
                            <ul class="space-y-4">
                                <li v-for="item in pengumuman" :key="item.id" class="border-b pb-3">
                                    <p class="font-bold text-gray-700">{{ item.judul }} - <span class="font-normal text-sm text-gray-500">{{ item.tanggal }}</span></p>
                                    <p class="text-gray-600 mt-1">{{ item.isi }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="bg-white p-6 rounded-2xl shadow-lg">
                            <div class="flex items-center space-x-3 mb-4">
                                <CalendarDaysIcon class="h-6 w-6 text-gray-500" />
                                <h3 class="text-xl font-bold text-gray-800">Jadwal Hari Ini</h3>
                            </div>
                            <ul class="space-y-3">
                                <li v-for="item in jadwalHariIni" :key="item.id">
                                    <p class="font-semibold text-gray-800">{{ item.mapel }}</p>
                                    <p class="text-sm text-gray-500">{{ item.jam }} - {{ item.guru }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>