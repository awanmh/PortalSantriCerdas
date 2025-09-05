<script setup>
import { Link } from '@inertiajs/vue3';
import { UserCircleIcon, MapPinIcon, DocumentArrowDownIcon, Cog6ToothIcon, ServerIcon } from '@heroicons/vue/24/outline';
import { useDashboard } from '@/Composables/useDashboard';

// Menggunakan Composable untuk semua logika data fetching
const { data: dashboardData, isLoading, error: errorMsg } = useDashboard();
</script>

<template>
    <div class="space-y-6">
        <!-- Tampilan Loading atau Error -->
        <div v-if="isLoading" class="text-center py-10 text-gray-500">Memuat data...</div>
        <div v-else-if="errorMsg" class="p-4 bg-red-100 text-red-700 rounded-lg text-center">{{ errorMsg }}</div>

        <!-- Konten Dashboard Utama -->
        <div v-else-if="dashboardData" class="space-y-8">
            <!-- Statistik Sistem -->
            <div>
                <h3 class="font-semibold text-xl mb-4 text-gray-700 flex items-center">
                    <ServerIcon class="h-6 w-6 mr-2" />
                    Statistik Sistem
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-blue-600">{{ dashboardData.total_pengguna }}</div>
                        <div class="text-sm font-medium text-gray-500">Total Pengguna</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-green-600">{{ dashboardData.total_siswa }}</div>
                        <div class="text-sm font-medium text-gray-500">Siswa</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-yellow-500">{{ dashboardData.total_guru }}</div>
                        <div class="text-sm font-medium text-gray-500">Guru & BK</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-purple-600">{{ dashboardData.zona_aktif }}</div>
                        <div class="text-sm font-medium text-gray-500">Zona Aktif</div>
                    </div>
                </div>
            </div>

            <!-- Panel Manajemen -->
            <div>
                <h3 class="font-semibold text-xl mb-4 text-gray-700 flex items-center">
                    <Cog6ToothIcon class="h-6 w-6 mr-2" />
                    Panel Manajemen
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <Link :href="route('users.index')" class="flex items-center p-4 bg-white rounded-lg shadow-md hover:bg-gray-50 transition">
                        <UserCircleIcon class="h-8 w-8 text-blue-500 mr-4" />
                        <div>
                            <p class="font-semibold text-gray-800">Manajemen Pengguna</p>
                            <p class="text-sm text-gray-500">Kelola akun siswa, guru, dan IT.</p>
                        </div>
                    </Link>
                    <Link :href="route('zona.index')" class="flex items-center p-4 bg-white rounded-lg shadow-md hover:bg-gray-50 transition">
                        <MapPinIcon class="h-8 w-8 text-green-500 mr-4" />
                        <div>
                            <p class="font-semibold text-gray-800">Manajemen Zona</p>
                            <p class="text-sm text-gray-500">Atur zona absensi berbasis lokasi.</p>
                        </div>
                    </Link>
                    <Link :href="route('export.index')" class="flex items-center p-4 bg-white rounded-lg shadow-md hover:bg-gray-50 transition">
                        <DocumentArrowDownIcon class="h-8 w-8 text-yellow-500 mr-4" />
                        <div>
                            <p class="font-semibold text-gray-800">Import & Export Data</p>
                            <p class="text-sm text-gray-500">Kelola data massal via Excel.</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
