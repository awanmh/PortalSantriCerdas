<script setup>
import { Link } from '@inertiajs/vue3';
import { ClockIcon, CheckCircleIcon, XCircleIcon, BookOpenIcon, ChartBarIcon } from '@heroicons/vue/24/outline';
import { useDashboard } from '@/Composables/useDashboard';

// Menggunakan Composable untuk semua logika data fetching. Jauh lebih bersih!
const { data: dashboardData, isLoading, error: errorMsg } = useDashboard();
</script>

<template>
    <div class="space-y-6">
        <!-- Tampilan Loading -->
        <div v-if="isLoading" class="text-center py-10">
            <p class="text-gray-500">Memuat data dashboard...</p>
        </div>

        <!-- Tampilan Error -->
        <div v-else-if="errorMsg" class="p-4 bg-red-100 text-red-700 rounded-lg text-center">
            {{ errorMsg }}
        </div>

        <!-- Konten Dashboard Utama -->
        <div v-else-if="dashboardData" class="space-y-8">
            <!-- Kartu Status Absensi Hari Ini -->
            <div class="p-5 rounded-xl shadow-md" :class="{
                'bg-green-50 border-l-4 border-green-500': dashboardData.absensi_hari_ini,
                'bg-yellow-50 border-l-4 border-yellow-500': !dashboardData.absensi_hari_ini
            }">
                <div class="flex items-center space-x-4">
                    <div v-if="dashboardData.absensi_hari_ini" class="flex-shrink-0">
                        <CheckCircleIcon class="h-8 w-8 text-green-500" />
                    </div>
                    <div v-else class="flex-shrink-0">
                        <XCircleIcon class="h-8 w-8 text-yellow-500" />
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">Status Absensi Hari Ini</h3>
                        <p v-if="dashboardData.absensi_hari_ini" class="text-gray-600">
                            Anda sudah absen pada pukul <strong>{{ new Date(dashboardData.absensi_hari_ini.waktu).toLocaleTimeString('id-ID') }}</strong>.
                        </p>
                        <p v-else class="text-gray-600">
                            Anda belum melakukan absensi hari ini.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi Absen -->
            <div v-if="!dashboardData.absensi_hari_ini" class="text-center">
                <Link
                    :href="route('absen.create')"
                    class="inline-flex items-center justify-center w-full sm:w-auto px-10 py-4 bg-blue-600 text-white font-bold text-lg rounded-lg shadow-lg hover:bg-blue-700 transition-transform transform hover:scale-105"
                >
                    <ClockIcon class="h-6 w-6 mr-3" />
                    Absen Sekarang
                </Link>
            </div>

            <!-- Rekap Kehadiran -->
            <div>
                <h4 class="font-semibold text-xl mb-4 text-gray-700 flex items-center">
                    <ChartBarIcon class="h-6 w-6 mr-2" />
                    Rekapitulasi Kehadiran
                </h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-green-600">{{ dashboardData.total_hadir }}</div>
                        <div class="text-sm font-medium text-gray-500">Hadir</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-blue-600">{{ dashboardData.total_izin }}</div>
                        <div class="text-sm font-medium text-gray-500">Izin</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-yellow-500">{{ dashboardData.total_sakit }}</div>
                        <div class="text-sm font-medium text-gray-500">Sakit</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                        <div class="text-3xl font-bold text-red-600">{{ dashboardData.total_alpha }}</div>
                        <div class="text-sm font-medium text-gray-500">Alpha</div>
                    </div>
                </div>
            </div>

            <!-- Jadwal Pelajaran Hari Ini (Placeholder) -->
            <div>
                <h4 class="font-semibold text-xl mb-4 text-gray-700 flex items-center">
                    <BookOpenIcon class="h-6 w-6 mr-2" />
                    Jadwal Hari Ini
                </h4>
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <p class="text-gray-500">Fitur jadwal pelajaran akan segera tersedia.</p>
                </div>
            </div>
        </div>
    </div>
</template>
