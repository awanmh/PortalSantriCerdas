<script setup>
import { ref, onMounted, watch } from "vue";
import { useDashboard } from "@/Composables/useDashboard";
import { useLiveGPS } from "@/Composables/useLiveGPS";
import L from "leaflet";

// Ambil dashboard utama
const { data: dashboardData, isLoading, error, fetchData } = useDashboard();

// Ambil lokasi siswa kelas pertama
const kelasId = ref(1); // default, nanti ambil dari dashboardData
const { siswaLokasi } = useLiveGPS(kelasId.value);

// State peta & markers
let map = null;
let markers = {};

// Inisialisasi Peta
onMounted(() => {
  map = L.map("map").setView([-7.2756, 112.6426], 15); // Default sekolah

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);
});

// Watch lokasi siswa, update marker real-time
watch(
  siswaLokasi,
  (newVal) => {
    newVal.forEach((item) => {
      const id = item.siswa.id;
      const { lat, lng } = item.lokasi;

      if (markers[id]) {
        markers[id].setLatLng([lat, lng]);
      } else {
        const marker = L.marker([lat, lng])
          .addTo(map)
          .bindPopup(`<b>${item.siswa.name}</b><br>${new Date(item.lokasi.waktu).toLocaleTimeString()}`);
        markers[id] = marker;
      }
    });
  },
  { deep: true }
);
</script>

<template>
  <div class="space-y-6">
    <div v-if="isLoading" class="text-center py-10 text-gray-500">Memuat data...</div>
    <div v-else-if="error" class="p-4 bg-red-100 text-red-700 rounded-lg text-center">{{ error }}</div>
    
    <div v-else-if="dashboardData">
      <h3 class="font-bold text-lg text-gray-800 mb-4">Peta Lokasi Siswa Real-Time</h3>
      <div id="map" class="w-full h-[500px] rounded-lg shadow-md"></div>
    </div>
  </div>
</template>

<style>
#map {
  width: 100%;
  height: 500px;
}
</style>
