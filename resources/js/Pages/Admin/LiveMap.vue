<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Hapus dependensi pada icon default Leaflet yang sering error
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
});

const props = defineProps({
    siswas: Array,
    zona: Object,
});

const mapContainer = ref(null);
let map = null;
const selectedSiswaId = ref(null);
const siswaMarkers = ref({});
const statusMessage = ref('Pilih siswa untuk memulai pemantauan.');

// Fungsi untuk inisialisasi peta
const initMap = () => {
    if (mapContainer.value && !map) {
        const initialCoords = props.zona ? [props.zona.lat, props.zona.lng] : [-7.2575, 112.7521];
        map = L.map(mapContainer.value).setView(initialCoords, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        if (props.zona) {
            L.circle([props.zona.lat, props.zona.lng], {
                color: 'green',
                fillColor: '#0f0',
                fillOpacity: 0.2,
                radius: props.zona.radius,
            }).addTo(map).bindPopup('Zona Sekolah');
        }
    }
};

// --- PERBAIKAN UTAMA DI SINI ---
// Fungsi untuk menunggu Echo siap, lalu memasang listener
const waitForEchoAndListen = () => {
    // Cek apakah Echo sudah siap
    if (typeof window.Echo !== 'undefined') {
        // Echo sudah siap, pasang watcher sekarang
        watch(selectedSiswaId, (newSiswaId, oldSiswaId) => {
            // 1. Tinggalkan channel lama jika ada
            if (oldSiswaId) {
                window.Echo.leave('lokasi-siswa.' + oldSiswaId);
                if (siswaMarkers.value[oldSiswaId]) {
                    map.removeLayer(siswaMarkers.value[oldSiswaId]);
                    delete siswaMarkers.value[oldSiswaId];
                }
            }

            // 2. Dengarkan channel baru jika ada ID siswa yang dipilih
            if (newSiswaId) {
                statusMessage.value = `Menunggu pembaruan lokasi dari siswa...`;
                window.Echo.private('lokasi-siswa.' + newSiswaId)
                    .listen('LokasiSiswaDiperbarui', (e) => {
                        const { lat, lng } = e.lokasi;
                        const siswaName = e.siswa.name;
                        statusMessage.value = `Lokasi ${siswaName} diperbarui!`;

                        if (siswaMarkers.value[newSiswaId]) {
                            map.removeLayer(siswaMarkers.value[newSiswaId]);
                        }
                        
                        const newMarker = L.marker([lat, lng]).addTo(map)
                            .bindPopup(`<b>${siswaName}</b><br>Posisi saat ini.`)
                            .openPopup();

                        siswaMarkers.value[newSiswaId] = newMarker;
                        map.setView([lat, lng], 17);

                        if (props.zona) {
                            const distance = map.distance([props.zona.lat, props.zona.lng], [lat, lng]);
                            if (distance > props.zona.radius) {
                                statusMessage.value = `PERINGATAN: ${siswaName} berada di luar zona sekolah!`;
                                // Ganti alert dengan pesan yang tidak mengganggu
                                console.warn(`PERINGATAN: ${siswaName} berada di luar zona sekolah!`);
                            }
                        }
                    });
            }
        });
    } else {
        // Echo belum siap, coba lagi dalam 100 milidetik
        setTimeout(waitForEchoAndListen, 100);
    }
};

onMounted(() => {
    initMap();
    // Mulai proses menunggu Echo
    waitForEchoAndListen();
});

onUnmounted(() => {
    // Pastikan kita berhenti mendengarkan saat komponen dihancurkan
    if (selectedSiswaId.value && typeof window.Echo !== 'undefined') {
        window.Echo.leave('lokasi-siswa.' + selectedSiswaId.value);
    }
});

</script>

<template>
    <Head title="Peta Pemantauan Langsung" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Peta Pemantauan Langsung
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col sm:flex-row gap-4 mb-4">
                            <select v-model="selectedSiswaId" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option :value="null" disabled>-- Pilih Siswa --</option>
                                <option v-for="siswa in siswas" :key="siswa.id" :value="siswa.id">
                                    {{ siswa.name }}
                                </option>
                            </select>
                            <div class="flex-grow p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-sm text-center">
                                Status: <span class="font-semibold">{{ statusMessage }}</span>
                            </div>
                        </div>
                        <div ref="mapContainer" class="h-[60vh] w-full rounded-lg"></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
