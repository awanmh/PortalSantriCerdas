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
        // Default view ke lokasi sekolah atau Surabaya jika tidak ada zona
        const initialCoords = props.zona ? [props.zona.lat, props.zona.lng] : [-7.2575, 112.7521];

        map = L.map(mapContainer.value).setView(initialCoords, 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Gambar zona sekolah di peta jika ada
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

// Fungsi untuk mendengarkan broadcast lokasi
const listenToSiswaLocation = (siswaId) => {
    if (!siswaId) return;

    // Hentikan listener sebelumnya
    if (window.Echo.privateChannels['lokasi-siswa.' + selectedSiswaId.value]) {
         window.Echo.leave('lokasi-siswa.' + selectedSiswaId.value);
    }

    selectedSiswaId.value = siswaId;
    statusMessage.value = `Menunggu pembaruan lokasi dari siswa...`;

    window.Echo.private('lokasi-siswa.' + siswaId)
        .listen('LokasiSiswaDiperbarui', (e) => {
            const { lat, lng } = e.lokasi;
            const siswaName = e.siswa.name;
            statusMessage.value = `Lokasi ${siswaName} diperbarui!`;

            // Hapus marker lama jika ada
            if (siswaMarkers.value[siswaId]) {
                map.removeLayer(siswaMarkers.value[siswaId]);
            }
            
            // Buat marker baru dan tambahkan ke peta
            const newMarker = L.marker([lat, lng]).addTo(map)
                .bindPopup(`<b>${siswaName}</b><br>Posisi saat ini.`)
                .openPopup();

            siswaMarkers.value[siswaId] = newMarker;
            map.setView([lat, lng], 17);

            // Cek apakah di luar zona
            if (props.zona) {
                const distance = map.distance([props.zona.lat, props.zona.lng], [lat, lng]);
                if (distance > props.zona.radius) {
                    alert(`PERINGATAN: ${siswaName} berada di luar zona sekolah!`);
                    statusMessage.value = `PERINGATAN: ${siswaName} berada di luar zona sekolah!`;
                }
            }
        });
};

// Watcher untuk mengubah listener saat pilihan siswa berubah
watch(selectedSiswaId, (newId, oldId) => {
    if (oldId) {
        window.Echo.leave('lokasi-siswa.' + oldId);
        if (siswaMarkers.value[oldId]) {
            map.removeLayer(siswaMarkers.value[oldId]);
            delete siswaMarkers.value[oldId];
        }
    }
    listenToSiswaLocation(newId);
});


onMounted(() => {
    initMap();
});

onUnmounted(() => {
    // Pastikan kita berhenti mendengarkan saat komponen dihancurkan
    if (selectedSiswaId.value) {
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
