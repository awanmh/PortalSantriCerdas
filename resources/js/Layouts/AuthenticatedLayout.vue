<script setup>
import { ref, computed, onMounted } from 'vue';
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

// Computed properties untuk pengecekan peran
const isIT = computed(() => roles.value.includes('it'));
const isGuru = computed(() => roles.value.includes('guru'));
const isBK = computed(() => roles.value.includes('bk'));

// Computed properties untuk pengecekan izin
const canManageUsers = computed(() => permissions.value.includes('manage users'));
const canManageJurusan = computed(() => permissions.value.includes('manage jurusan'));
const canManageKelas = computed(() => permissions.value.includes('manage kelas'));
const canManageJadwal = computed(() => permissions.value.includes('manage jadwal'));
const canManageZona = computed(() => permissions.value.includes('manage zona'));
const canViewLaporanAbsensi = computed(() => permissions.value.includes('view laporan absensi'));
const canManageCatatanPelanggaran = computed(() => permissions.value.includes('manage catatan pelanggaran'));
const canViewLiveMap = computed(() => roles.value.some(r => ['guru', 'bk', 'it'].includes(r)));

// Menentukan nama rute untuk Laporan Absensi berdasarkan peran pengguna
const laporanAbsensiRouteName = computed(() => {
    return isIT.value ? 'admin.laporan.absensi.index' : 'laporan.absensi.index';
});

// Mengecek apakah rute laporan absensi sedang aktif
const isLaporanAbsensiActive = computed(() => {
    const baseRouteActive = route().current('admin.laporan.absensi.index') || route().current('laporan.absensi.index');
    if (baseRouteActive) {
        // --- PERBAIKAN DI SINI ---
        // Menggunakan optional chaining (?.) untuk mencegah error jika 'query' tidak ada
        const currentType = page.props.ziggy?.location?.query?.type;
        return currentType === 'siswa' || currentType === 'guru';
    }
    return false;
});

const showManajemenDropdown = computed(() =>
    canManageJurusan.value ||
    canManageKelas.value ||
    canManageJadwal.value ||
    canManageUsers.value ||
    canManageZona.value
);

function toggleDarkMode() {
    darkMode.value = !darkMode.value;
    if (darkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    }
}

// Cek preferensi dark mode saat komponen di-mount
onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const storedDarkMode = localStorage.getItem('darkMode');

    if (storedDarkMode === 'true') {
        darkMode.value = true;
        document.documentElement.classList.add('dark');
    } else if (storedDarkMode === 'false') {
        darkMode.value = false;
        document.documentElement.classList.remove('dark');
    } else if (prefersDark) {
        darkMode.value = true;
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    }
});

// Fungsi debug
function debugLaporanAbsensi() {
    console.group('DEBUG: Klik Laporan Absensi');
    console.log('User Roles:', roles.value);
    console.log('User Permissions:', permissions.value);
    console.log(`isBK: ${isBK.value}, isIT: ${isIT.value}`);
    console.log('Route name yang dipakai:', laporanAbsensiRouteName.value);
    try {
        const targetUrl = route(laporanAbsensiRouteName.value, { type: 'siswa' });
        console.log('URL yang dituju:', targetUrl);
    } catch (e) {
        console.error("Error saat generate route:", e.message);
    }
    console.groupEnd();
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <nav class="bg-white dark:bg-gray-800 shadow-md transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">

                    <div class="flex-shrink-0 flex items-center">
                        <Link :href="route('dashboard')">
                            <img src="/images/logo.png" alt="Logo Sekolah" class="h-10 w-auto"/>
                        </Link>
                    </div>

                    <div class="hidden sm:flex space-x-6 ml-10">
                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>
                        
                        <NavLink v-if="isGuru" :href="route('guru.absensi.siswa.index')" :active="route().current('guru.absensi.siswa.*')">Cetak Absensi Siswa</NavLink>

                        <NavLink 
                            v-if="canViewLaporanAbsensi && (isBK || isIT)" 
                            :href="route(laporanAbsensiRouteName, { type: 'siswa' })" 
                            :active="isLaporanAbsensiActive"
                            @click="debugLaporanAbsensi"
                        >
                            Laporan Absensi
                        </NavLink> 
                        
                        <NavLink v-if="canManageCatatanPelanggaran" :href="route('catatan-pelanggaran.index')" :active="route().current('catatan-pelanggaran.*')">Catatan Pelanggaran</NavLink>
                        <NavLink v-if="canViewLiveMap" :href="route('live-map.index')" :active="route().current('live-map.index')">Live Map</NavLink>

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

                    <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                        <button @click="toggleDarkMode" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                            <span v-if="darkMode">☀️</span>
                            <span v-else>🌙</span>
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

            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
                    
                    <ResponsiveNavLink v-if="isGuru" :href="route('guru.absensi.siswa.index')" :active="route().current('guru.absensi.siswa.*')">Cetak Absensi Siswa</ResponsiveNavLink>
                    
                    <ResponsiveNavLink 
                        v-if="canViewLaporanAbsensi && (isBK || isIT)" 
                        :href="route(laporanAbsensiRouteName, { type: 'siswa' })" 
                        :active="isLaporanAbsensiActive"
                        @click="debugLaporanAbsensi"
                    >
                        Laporan Absensi
                    </ResponsiveNavLink>
                    
                    <ResponsiveNavLink v-if="canManageCatatanPelanggaran" :href="route('catatan-pelanggaran.index')" :active="route().current('catatan-pelanggaran.*')">Catatan Pelanggaran</ResponsiveNavLink>
                    <ResponsiveNavLink v-if="canViewLiveMap" :href="route('live-map.index')" :active="route().current('live-map.index')">Live Map</ResponsiveNavLink>

                    <div v-if="showManajemenDropdown" class="border-t border-gray-200 dark:border-gray-600 pt-2">
                        <div class="px-4 py-1 text-xs font-semibold text-gray-500 dark:text-gray-400">Manajemen</div>
                        <ResponsiveNavLink v-if="canManageUsers" :href="route('admin.users.index')">Pengguna</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="canManageJurusan" :href="route('admin.jurusan.index')">Jurusan</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="canManageKelas" :href="route('admin.kelas.index')">Kelas</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="canManageJadwal" :href="route('admin.jadwal.index')">Jadwal</ResponsiveNavLink>
                        <ResponsiveNavLink v-if="canManageZona" :href="route('admin.zona.index')">Zona</ResponsiveNavLink>
                    </div>
                </div>
                
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ user.name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <header class="bg-white dark:bg-gray-800 shadow transition-colors duration-300" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>