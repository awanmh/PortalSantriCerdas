<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const hasRole = (role) => user.value.roles.some(r => r.name === role);

const greeting = computed(() => page.props.greeting);

// Data untuk Admin/IT/BK
const totalUsers = computed(() => page.props.totalUsers);
const totalJadwalHariIni = computed(() => page.props.totalJadwalHariIni);
const jadwalHariIni = computed(() => page.props.jadwalHariIni || []);

// Data untuk Guru
const jadwalSayaHariIni = computed(() => page.props.jadwalSayaHariIni || []);
const kelasWali = computed(() => page.props.kelasWali);

// Data untuk Siswa
const jadwalKelasHariIni = computed(() => page.props.jadwalKelasHariIni || []);

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ greeting }}, {{ user.name }}!</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p>Anda login sebagai: <span v-for="role in user.roles" :key="role.id" class="font-bold">{{ role.name }} </span></p>

                        <div v-if="hasRole('it') || hasRole('admin') || hasRole('bk')">
                            <h3 class="text-lg font-semibold mt-4">Ringkasan Sistem</h3>
                            <p>Total Pengguna: {{ totalUsers }}</p>
                            <p>Jadwal Hari Ini (Semua): {{ totalJadwalHariIni }}</p>

                            <div v-if="jadwalHariIni.length" class="mt-4">
                                <h4 class="font-medium">Detail Jadwal Hari Ini (Semua):</h4>
                                <ul class="list-disc ml-5">
                                    <li v-for="jadwal in jadwalHariIni" :key="jadwal.id">
                                        {{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}:
                                        {{ jadwal.mata_pelajaran }} (Kelas: {{ jadwal.kelas?.nama_kelas || 'Umum' }}, Guru: {{ jadwal.guru?.name || '-' }})
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="mt-4">Tidak ada jadwal hari ini untuk seluruh sistem.</p>
                        </div>

                        <div v-else-if="hasRole('guru')">
                            <h3 class="text-lg font-semibold mt-4">Jadwal Mengajar Saya Hari Ini</h3>
                            <div v-if="jadwalSayaHariIni.length" class="mt-4">
                                <ul class="list-disc ml-5">
                                    <li v-for="jadwal in jadwalSayaHariIni" :key="jadwal.id">
                                        {{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}:
                                        <span class="font-medium">{{ jadwal.mata_pelajaran }}</span> di Kelas <span class="font-medium">{{ jadwal.kelas.nama_kelas }}</span>
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="mt-4">Tidak ada jadwal mengajar untuk Anda hari ini.</p>

                            <div v-if="kelasWali" class="mt-6">
                                <h3 class="text-lg font-semibold">Kelas Wali Saya</h3>
                                <p>Wali Kelas untuk: <span class="font-medium">{{ kelasWali.nama_kelas }}</span> (Jurusan: {{ kelasWali.jurusan.nama_jurusan }})</p>
                            </div>
                            <p v-else class="mt-6">Anda belum diatur sebagai wali kelas.</p>
                        </div>

                        <div v-else-if="hasRole('siswa')">
                            <h3 class="text-lg font-semibold mt-4">Jadwal Kelas Saya Hari Ini</h3>
                            <div v-if="jadwalKelasHariIni.length" class="mt-4">
                                <ul class="list-disc ml-5">
                                    <li v-for="jadwal in jadwalKelasHariIni" :key="jadwal.id">
                                        {{ jadwal.jam_mulai }} - {{ jadwal.jam_selesai }}:
                                        <span class="font-medium">{{ jadwal.mata_pelajaran }}</span> (Guru: {{ jadwal.guru?.name || '-' }})
                                    </li>
                                </ul>
                            </div>
                            <p v-else class="mt-4">Tidak ada jadwal kelas untuk Anda hari ini.</p>
                        </div>

                        <div v-else class="mt-4">
                            <p>Anda belum memiliki role spesifik atau dashboard Anda belum dikonfigurasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>