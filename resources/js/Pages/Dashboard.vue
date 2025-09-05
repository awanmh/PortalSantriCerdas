<script setup>
// 1. IMPORT DEPENDENSI
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import SiswaDashboard from './Dashboard/Siswa.vue';
import GuruDashboard from './Dashboard/Guru.vue';
import BKDashboard from './Dashboard/BK.vue';
import ITDashboard from './Dashboard/IT.vue';

// 2. DEFINISIKAN PROPS
// Sekarang menerima 'auth' dan 'dashboardData' dari controller
const props = defineProps({
    auth: {
        type: Object,
        required: true,
    },
    // TAMBAHKAN PROP BARU UNTUK MENERIMA DATA DASHBOARD
    dashboardData: {
        type: Object,
        required: true,
    },
});

// 3. LOGIKA PEMILIHAN KOMPONEN (Tidak berubah)
const userRole = props.auth.user.roles[0];
const roleComponentMap = {
    siswa: SiswaDashboard,
    guru: GuruDashboard,
    bk: BKDashboard,
    it: ITDashboard,
};
const activeDashboardComponent = roleComponentMap[userRole];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout :auth="auth">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!--
                            5. TAMPILKAN DASHBOARD DINAMIS
                            - Meneruskan 'dashboardData' sebagai prop ke komponen anak
                              yang sedang aktif.
                        -->
                        <component
                            :is="activeDashboardComponent"
                            v-if="activeDashboardComponent"
                            :dashboardData="props.dashboardData"
                         />
                        <div v-else>
                            <p class="text-red-500">Dashboard untuk peran '{{ userRole }}' tidak ditemukan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
