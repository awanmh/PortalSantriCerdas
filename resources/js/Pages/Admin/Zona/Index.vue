<script setup>
import { onMounted, ref, watch } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import CheckCircleIcon from '@heroicons/vue/24/solid/CheckCircleIcon';

// Impor Pustaka Peta
import "leaflet/dist/leaflet.css";
import L from "leaflet";

// Mendefinisikan props yang diterima dari controller (ZonaController.php)
const props = defineProps({
    /**
     * Objek yang berisi data zona utama sekolah.
     */
    zona: {
        type: Object,
        required: true,
    },
    /**
     * Menangkap pesan 'success' dari redirect controller untuk notifikasi.
     */
    flash: {
        type: Object,
        default: () => ({}),
    },
});

// Menginisialisasi form reaktif menggunakan useForm dari Inertia.
const form = useForm({
    nama_zona: props.zona.nama_zona,
    lat: props.zona.lat,
    lng: props.zona.lng,
    radius: props.zona.radius,
    jam_masuk: props.zona.jam_masuk ? props.zona.jam_masuk.substring(0, 5) : '07:00',
    jam_pulang: props.zona.jam_pulang ? props.zona.jam_pulang.substring(0, 5) : '15:00',
});

// Refs untuk menyimpan instance dari Leaflet agar bisa dimanipulasi
const mapElement = ref(null);
const mapInstance = ref(null);
const markerInstance = ref(null);
const circleInstance = ref(null);

// Lifecycle hook: Kode di dalamnya akan berjalan setelah komponen di-mount ke DOM.
onMounted(() => {
    if (!mapElement.value || mapInstance.value) return;

    // Inisialisasi Peta, Marker, dan Lingkaran Radius
    mapInstance.value = L.map(mapElement.value).setView([form.lat, form.lng], 17);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mapInstance.value);
    markerInstance.value = L.marker([form.lat, form.lng], { draggable: true }).addTo(mapInstance.value);
    circleInstance.value = L.circle([form.lat, form.lng], {
        color: '#3b82f6', fillColor: '#60a5fa', fillOpacity: 0.3, radius: form.radius
    }).addTo(mapInstance.value);

    // Event Listener: Update form saat marker digeser
    markerInstance.value.on('dragend', (event) => {
        const position = event.target.getLatLng();
        form.lat = parseFloat(position.lat.toFixed(7));
        form.lng = parseFloat(position.lng.toFixed(7));
    });
});

// Watcher: Update ukuran lingkaran saat radius di form berubah
watch(() => form.radius, (newRadius) => {
    if (circleInstance.value) {
        circleInstance.value.setRadius(newRadius);
    }
});

// Watcher: Update posisi peta saat latitude/longitude di form berubah
watch([() => form.lat, () => form.lng], ([newLat, newLng]) => {
     if (markerInstance.value && circleInstance.value) {
        const newPosition = L.latLng(newLat, newLng);
        markerInstance.value.setLatLng(newPosition);
        circleInstance.value.setLatLng(newPosition);
        mapInstance.value.panTo(newPosition);
    }
});

// Fungsi untuk submit form
const submit = () => {
    form.post(route('admin.zona.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Manajemen Zona" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Manajemen Zona Sekolah
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Notifikasi Sukses -->
                <Transition enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                    <div v-if="flash.success" class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative flex items-center shadow-md" role="alert">
                        <CheckCircleIcon class="h-6 w-6 mr-3"/>
                        <span class="block sm:inline font-medium">{{ flash.success }}</span>
                    </div>
                </Transition>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Pengaturan Zona Absensi</h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Konfigurasikan lokasi pusat, radius, dan jam operasional sekolah untuk sistem absensi.
                        </p>
                    </div>

                    <div class="p-6 md:p-8 grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">
                        <!-- Kolom Kiri: Peta Interaktif -->
                        <div class="lg:col-span-3 flex flex-col h-full">
                            <h3 class="text-lg font-medium text-gray-900">Peta Lokasi Interaktif</h3>
                            <p class="text-sm text-gray-500 mb-4">Klik dan geser pin untuk menentukan titik pusat absensi.</p>
                            <div ref="mapElement" class="w-full h-[400px] lg:h-full rounded-lg z-0 shadow-inner border border-gray-200 bg-gray-50"></div>
                        </div>

                        <!-- Kolom Kanan: Form Pengaturan -->
                        <div class="lg:col-span-2">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div>
                                    <InputLabel for="nama_zona" value="Nama Zona" />
                                    <TextInput id="nama_zona" type="text" class="mt-1 block w-full" v-model="form.nama_zona" required />
                                    <InputError class="mt-2" :message="form.errors.nama_zona" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="lat" value="Latitude" />
                                        <TextInput id="lat" type="number" step="any" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" v-model="form.lat" readonly />
                                        <InputError class="mt-2" :message="form.errors.lat" />
                                    </div>
                                    <div>
                                        <InputLabel for="lng" value="Longitude" />
                                        <TextInput id="lng" type="number" step="any" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" v-model="form.lng" readonly />
                                        <InputError class="mt-2" :message="form.errors.lng" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel for="radius" value="Radius Absensi (dalam meter)" />
                                    <TextInput id="radius" type="number" class="mt-1 block w-full" v-model="form.radius" required min="10" />
                                    <InputError class="mt-2" :message="form.errors.radius" />
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="jam_masuk" value="Jam Masuk" />
                                        <TextInput id="jam_masuk" type="time" class="mt-1 block w-full" v-model="form.jam_masuk" required />
                                        <InputError class="mt-2" :message="form.errors.jam_masuk" />
                                    </div>
                                    <div>
                                        <InputLabel for="jam_pulang" value="Jam Pulang" />
                                        <TextInput id="jam_pulang" type="time" class="mt-1 block w-full" v-model="form.jam_pulang" required />
                                        <InputError class="mt-2" :message="form.errors.jam_pulang" />
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 pt-2">
                                    <PrimaryButton :disabled="form.processing">
                                        Simpan Pengaturan
                                    </PrimaryButton>
                                    <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Tersimpan.</p>
                                    </Transition>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>