<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
// --- PERBAIKAN: Tambahkan ExclamationCircleIcon ---
import { PencilIcon, TrashIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

// Props dari Controller
const props = defineProps({
    jadwal: { type: Array, required: true }, // Mengubah tipe menjadi Array agar lebih spesifik
    kelasOptions: { type: Array, required: true },
    guruOptions: { type: Array, required: true },
    flash: { type: Object, default: () => ({}) },
});

// State
const isEditMode = ref(false);
const showDeleteModal = ref(false);
const jadwalToDelete = ref(null);

// Form setup
const form = useForm({
    id: null,
    mata_pelajaran: '',
    deskripsi: '',
    hari: '',
    tanggal: '',
    jam_mulai: '',
    jam_selesai: '',
    tipe: 'pelajaran',
    kelas_id: '',
    guru_id: null, // Default ke null agar placeholder "Tidak ada guru" berfungsi
});

// Hari dalam minggu
const daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

// Computed property untuk judul form
const formTitle = computed(() => isEditMode.value ? '✏️ Edit Jadwal' : '➕ Tambah Jadwal Baru');

// --- Peningkatan Performa: Mengelompokkan Jadwal Sekali Saja ---
const groupedJadwalPelajaran = computed(() => {
    const grouped = {};
    const pelajaran = props.jadwal.filter(j => j.tipe === 'pelajaran');
    for (const item of pelajaran) {
        if (!grouped[item.kelas_id]) {
            grouped[item.kelas_id] = {};
        }
        if (!grouped[item.kelas_id][item.hari]) {
            grouped[item.kelas_id][item.hari] = [];
        }
        grouped[item.kelas_id][item.hari].push(item);
    }
    return grouped;
});

const jadwalAcara = computed(() => {
    return props.jadwal
        .filter(j => j.tipe === 'acara')
        .sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal));
});


// Watcher untuk tipe jadwal
watch(() => form.tipe, (newVal) => {
    if (newVal === 'pelajaran') {
        form.tanggal = '';
    } else { // tipe === 'acara'
        form.hari = '';
        form.kelas_id = '';
    }
}, { immediate: true });

// Edit
const editJadwal = (item) => {
    isEditMode.value = true;
    form.id = item.id;
    form.mata_pelajaran = item.mata_pelajaran;
    form.deskripsi = item.deskripsi;
    form.hari = item.hari;
    form.tanggal = item.tanggal;
    // Menggunakan slice untuk memastikan format HH:mm
    form.jam_mulai = item.jam_mulai.slice(0, 5);
    form.jam_selesai = item.jam_selesai.slice(0, 5);
    form.tipe = item.tipe;
    form.kelas_id = item.kelas_id;
    form.guru_id = item.guru_id;
    form.clearErrors();
    // Scroll ke form untuk UX yang lebih baik
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Reset form
const resetForm = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
};

// Submit form
const submit = () => {
    const options = {
        onSuccess: () => resetForm(),
        preserveScroll: true,
    };
    if (isEditMode.value) {
        form.put(route('admin.jadwal.update', form.id), options);
    } else {
        form.post(route('admin.jadwal.store'), options);
    }
};

// Hapus
const confirmDelete = (item) => {
    jadwalToDelete.value = item;
    showDeleteModal.value = true;
};
const closeModal = () => {
    showDeleteModal.value = false;
    jadwalToDelete.value = null;
};
const destroyJadwal = () => {
    form.delete(route('admin.jadwal.destroy', jadwalToDelete.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true,
    });
};

// Format jam untuk tampilan tabel
const formatTime = (time) => {
    if (!time) return '-';
    // String sudah dalam format HH:mm:ss, cukup ambil 5 karakter pertama
    return time.slice(0, 5);
};
</script>

<template>
    <Head title="Manajemen Jadwal" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Manajemen Jadwal</h2>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <div v-if="flash.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-md">
                <CheckCircleIcon class="h-6 w-6 mr-3"/>
                <span class="block sm:inline font-medium">{{ flash.success }}</span>
            </div>
            <div v-if="flash.error" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg flex items-center shadow-md">
                <ExclamationCircleIcon class="h-6 w-6 mr-3"/>
                <span class="block sm:inline font-medium">{{ flash.error }}</span>
            </div>

            <!-- Layout Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="md:col-span-1 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ formTitle }}</h3>
                    <form @submit.prevent="submit" class="space-y-4">

                        <div>
                            <InputLabel for="tipe" value="Tipe Jadwal" />
                            <select id="tipe" v-model="form.tipe" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm" required>
                                <option value="pelajaran">Pelajaran (Mingguan)</option>
                                <option value="acara">Acara Sekolah (Tanggal Spesifik)</option>
                            </select>
                            <InputError :message="form.errors.tipe" class="mt-2"/>
                        </div>

                        <div v-if="form.tipe === 'pelajaran'">
                            <InputLabel for="hari" value="Hari" />
                            <select id="hari" v-model="form.hari" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm" :required="form.tipe === 'pelajaran'">
                                <option value="" disabled>Pilih Hari</option>
                                <option v-for="day in daysOfWeek" :key="day" :value="day">{{ day }}</option>
                            </select>
                            <InputError :message="form.errors.hari" class="mt-2"/>
                        </div>

                        <div v-if="form.tipe === 'acara'">
                            <InputLabel for="tanggal" value="Tanggal Acara" />
                            <TextInput id="tanggal" v-model="form.tanggal" type="date" class="mt-1 block w-full" :required="form.tipe === 'acara'" />
                            <InputError :message="form.errors.tanggal" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="mata_pelajaran" value="Mata Pelajaran / Nama Acara" />
                            <TextInput id="mata_pelajaran" v-model="form.mata_pelajaran" type="text" class="mt-1 block w-full" required />
                            <InputError :message="form.errors.mata_pelajaran" class="mt-2"/>
                        </div>

                        <div v-if="form.tipe === 'pelajaran'">
                            <InputLabel for="kelas_id" value="Kelas" />
                            <select id="kelas_id" v-model="form.kelas_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm" :required="form.tipe === 'pelajaran'">
                                <option value="" disabled>Pilih Kelas</option>
                                <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">{{ kelas.nama_kelas }}</option>
                            </select>
                            <InputError :message="form.errors.kelas_id" class="mt-2"/>
                        </div>

                        <div>
                            <InputLabel for="guru_id" value="Guru Pengajar (Opsional)" />
                            <select id="guru_id" v-model="form.guru_id" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm">
                                <option :value="null">Tidak ada guru spesifik</option>
                                <option v-for="guru in guruOptions" :key="guru.id" :value="guru.id">{{ guru.name }}</option>
                            </select>
                            <InputError :message="form.errors.guru_id" class="mt-2"/>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="jam_mulai" value="Jam Mulai" />
                                <TextInput id="jam_mulai" v-model="form.jam_mulai" type="time" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.jam_mulai" class="mt-2"/>
                            </div>
                            <div>
                                <InputLabel for="jam_selesai" value="Jam Selesai" />
                                <TextInput id="jam_selesai" v-model="form.jam_selesai" type="time" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.jam_selesai" class="mt-2"/>
                            </div>
                        </div>

                        <div>
                            <InputLabel for="deskripsi" value="Deskripsi (Opsional)" />
                            <textarea id="deskripsi" v-model="form.deskripsi" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm"></textarea>
                            <InputError :message="form.errors.deskripsi" class="mt-2"/>
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <SecondaryButton v-if="isEditMode" @click.prevent="resetForm" type="button">Batal</SecondaryButton>
                            <PrimaryButton :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                                {{ isEditMode ? 'Update Jadwal' : 'Simpan Jadwal' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Tabel Jadwal -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                        <h3 class="font-semibold text-xl mb-4 text-gray-900 dark:text-gray-100">Jadwal Pelajaran Mingguan</h3>
                        <div v-for="kelas in kelasOptions" :key="'pelajaran-'+kelas.id" class="mb-6">
                            <h4 class="font-bold text-lg mb-2 text-gray-700 dark:text-gray-300">Kelas {{ kelas.nama_kelas }}</h4>
                            <div v-for="hari in daysOfWeek" :key="'pelajaran-'+kelas.id+'-'+hari" class="mb-4">
                                <h5 class="font-medium text-gray-600 dark:text-gray-400 border-b dark:border-gray-700 pb-1 mb-2">{{ hari }}</h5>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700 rounded-lg">
                                    <li v-for="item in groupedJadwalPelajaran[kelas.id]?.[hari]" 
                                        :key="item.id" class="p-3 flex justify-between items-center bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div>
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ item.mata_pelajaran }}</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ formatTime(item.jam_mulai) }} - {{ formatTime(item.jam_selesai) }})</span>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Pengajar: {{ item.guru?.name ?? 'Umum' }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="editJadwal(item)" class="text-indigo-600 hover:text-indigo-900 focus:outline-none p-1 rounded-full hover:bg-indigo-100 dark:hover:bg-gray-600 transition">
                                                <PencilIcon class="h-5 w-5"/>
                                            </button>
                                            <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 focus:outline-none p-1 rounded-full hover:bg-red-100 dark:hover:bg-gray-600 transition">
                                                <TrashIcon class="h-5 w-5"/>
                                            </button>
                                        </div>
                                    </li>
                                    <li v-if="!groupedJadwalPelajaran[kelas.id]?.[hari]" 
                                        class="p-3 text-sm text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800">
                                        Tidak ada jadwal pelajaran.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mt-6">
                        <h3 class="font-semibold text-xl mb-4 text-gray-900 dark:text-gray-100">Jadwal Acara Sekolah</h3>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700 rounded-lg">
                            <li v-for="item in jadwalAcara" 
                                :key="'acara-'+item.id" class="p-3 flex justify-between items-center bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ item.mata_pelajaran }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ item.tanggal }} {{ formatTime(item.jam_mulai) }} - {{ formatTime(item.jam_selesai) }})</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ item.deskripsi }}</p>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Penyelenggara: {{ item.guru?.name ?? 'Umum' }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="editJadwal(item)" class="text-indigo-600 hover:text-indigo-900 focus:outline-none p-1 rounded-full hover:bg-indigo-100 dark:hover:bg-gray-600 transition">
                                        <PencilIcon class="h-5 w-5"/>
                                    </button>
                                    <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 focus:outline-none p-1 rounded-full hover:bg-red-100 dark:hover:bg-gray-600 transition">
                                        <TrashIcon class="h-5 w-5"/>
                                    </button>
                                </div>
                            </li>
                            <li v-if="jadwalAcara.length === 0" class="p-3 text-sm text-gray-400 dark:text-gray-500 bg-white dark:bg-gray-800">
                                Tidak ada acara sekolah yang terjadwal.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Hapus -->
            <Modal :show="showDeleteModal" @close="closeModal">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Konfirmasi Hapus Jadwal</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                        Apakah Anda yakin ingin menghapus jadwal "{{ jadwalToDelete?.mata_pelajaran }}"? Aksi ini tidak dapat dibatalkan.
                    </p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <DangerButton @click="destroyJadwal" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">Ya, Hapus</DangerButton>
                    </div>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
