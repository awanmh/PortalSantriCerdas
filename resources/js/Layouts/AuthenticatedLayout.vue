<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);
const darkMode = ref(false);

const page = usePage();
const user = computed(() => page.props.auth.user);
const permissions = computed(() => user.value?.permissions ?? []);
const roles = computed(() => user.value?.roles ?? []);

const isIT = computed(() => roles.value.includes('it'));
const canManageUsers = computed(() => permissions.value.includes('manage users'));
const canManageJurusan = computed(() => permissions.value.includes('manage jurusan'));
const canManageKelas = computed(() => permissions.value.includes('manage kelas'));
const canManageJadwal = computed(() => permissions.value.includes('manage jadwal'));
const canManageZona = computed(() => permissions.value.includes('manage zona'));
const canViewLaporanAbsensi = computed(() => permissions.value.includes('view laporan absensi'));
const canViewCatatanPelanggaran = computed(() => permissions.value.includes('view catatan pelanggaran'));
const canViewLiveMap = computed(() => roles.value.some(r => ['guru', 'bk', 'it'].includes(r)));

// --- PERBAIKAN DI SINI ---
// Logika ini sekarang akan bekerja dengan benar karena nama rute di backend sudah unik.
const laporanAbsensiRouteName = computed(() => {
    return isIT.value ? 'admin.laporan.absensi.index' : 'laporan.absensi.index';
});
const isLaporanAbsensiActive = computed(() => {
    return route().current('admin.laporan.absensi.index') || route().current('laporan.absensi.index');
});
// --- END PERBAIKAN ---

const showManajemenDropdown = computed(() => canManageJurusan.value || canManageKelas.value || canManageJadwal.value || canManageUsers.value || canManageZona.value);

function toggleDarkMode() {
    darkMode.value = !darkMode.value;
    if (darkMode.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

if (typeof window !== 'undefined' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    toggleDarkMode();
}
</script>

<template>
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow-md transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <Link :href="route('dashboard')">
                        <img src="/images/logo.png" alt="Logo Sekolah" class="h-10 w-auto"/>
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden sm:flex space-x-6 ml-10">
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>
                    <NavLink v-if="canViewLaporanAbsensi" :href="route(laporanAbsensiRouteName)" :active="isLaporanAbsensiActive">Laporan Absensi</NavLink>
                    <NavLink v-if="canViewCatatanPelanggaran" :href="route('catatan-pelanggaran.index')" :active="route().current('catatan-pelanggaran.*')">Catatan Pelanggaran</NavLink>
                    <NavLink v-if="canViewLiveMap" :href="route('live-map.index')" :active="route().current('live-map.index')">Live Map</NavLink>

                    <!-- Dropdown Manajemen -->
                    <Dropdown v-if="showManajemenDropdown" align="left" width="48">
                        <template #trigger>
                            <button class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                Manajemen
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink v-if="canManageUsers" :href="route('admin.users.index')">Pengguna</DropdownLink>
                            <DropdownLink v-if="canManageJurusan" :href="route('admin.jurusan.index')">Jurusan</DropdownLink>
                            <DropdownLink v-if="canManageKelas" :href="route('admin.kelas.index')">Kelas</DropdownLink>
                            <DropdownLink v-if="canManageJadwal" :href="route('admin.jadwal.index')">Jadwal</DropdownLink>
                            <DropdownLink v-if="canManageZona" :href="route('admin.zona.index')">Zona</DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                <!-- User Dropdown + Darkmode Toggle -->
                <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                    <button @click="toggleDarkMode" class="px-3 py-2 rounded-md text-sm bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        {{ darkMode ? '🌙 Dark' : '☀️ Light' }}
                    </button>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                {{ user.name }}
                                <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                <!-- Hamburger Menu (Mobile) -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
                <ResponsiveNavLink v-if="canViewLaporanAbsensi" :href="route(laporanAbsensiRouteName)" :active="isLaporanAbsensiActive">Laporan Absensi</ResponsiveNavLink>
                <ResponsiveNavLink v-if="canViewCatatanPelanggaran" :href="route('catatan-pelanggaran.index')" :active="route().current('catatan-pelanggaran.*')">Catatan Pelanggaran</ResponsiveNavLink>
                <ResponsiveNavLink v-if="canViewLiveMap" :href="route('live-map.index')" :active="route().current('live-map.index')">Live Map</ResponsiveNavLink>
            </div>
        </div>
    </nav>

    <!-- Page Heading -->
    <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-300" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <slot name="header" />
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 transition-colors duration-300">
        <slot />
    </main>
</div>
</template>
