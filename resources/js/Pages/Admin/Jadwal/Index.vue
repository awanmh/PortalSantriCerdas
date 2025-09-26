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
import { PencilIcon, TrashIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline';

// Props
const props = defineProps({
    jadwal: Array,
    kelasOptions: Array,
    guruOptions: Array,
    mataPelajaranOptions: Array,
    flash: { type: Object, default: () => ({}) },
});

// State
const isEditMode = ref(false);
const showDeleteModal = ref(false);
const jadwalToDelete = ref(null);
const selectedKelasId = ref('');
const kelasSearch = ref('');

// Form
const form = useForm({
    id: null,
    mata_pelajaran_id: null,
    deskripsi: '',
    hari: '',
    tanggal: '',
    jam_mulai: '',
    jam_selesai: '',
    tipe: 'pelajaran',
    kelas_id: '',
    guru_id: null,
});

// Hari dalam minggu
const daysOfWeek = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

// Judul form
const formTitle = computed(() => isEditMode.value ? '✏️ Edit Jadwal' : '➕ Tambah Jadwal Baru');

// Kelompokkan jadwal pelajaran per kelas per hari
const groupedJadwalPelajaran = computed(() => {
    const grouped = {};
    props.jadwal.filter(j => j.tipe === 'pelajaran').forEach(item => {
        if (!grouped[item.kelas_id]) grouped[item.kelas_id] = {};
        if (!grouped[item.kelas_id][item.hari]) grouped[item.kelas_id][item.hari] = [];
        grouped[item.kelas_id][item.hari].push(item);
    });
    return grouped;
});

// Filter kelas dengan search
const filteredKelasOptions = computed(() => {
    if (!kelasSearch.value) return props.kelasOptions;
    return props.kelasOptions.filter(k =>
        `${k.nama_kelas} ${k.jurusan || ''}`.toLowerCase().includes(kelasSearch.value.toLowerCase())
    );
});

// Filter jadwal sesuai kelas terpilih
const filteredJadwalPelajaran = computed(() => {
    if (!selectedKelasId.value) return groupedJadwalPelajaran.value;
    return { [selectedKelasId.value]: groupedJadwalPelajaran.value[selectedKelasId.value] || {} };
});

// Jadwal acara (sort by tanggal)
const jadwalAcara = computed(() =>
    props.jadwal.filter(j => j.tipe === 'acara').sort((a, b) => new Date(a.tanggal) - new Date(b.tanggal))
);

// Watch tipe jadwal
watch(() => form.tipe, val => {
    if (val === 'pelajaran') form.tanggal = '';
    else { form.hari = ''; form.kelas_id = ''; }
}, { immediate: true });

// Edit jadwal
const editJadwal = item => {
    isEditMode.value = true;
    Object.assign(form, {
        id: item.id,
        mata_pelajaran_id: item.mata_pelajaran_id,
        deskripsi: item.deskripsi,
        hari: item.hari,
        tanggal: item.tanggal,
        jam_mulai: item.jam_mulai.slice(0, 5),
        jam_selesai: item.jam_selesai.slice(0, 5),
        tipe: item.tipe,
        kelas_id: item.kelas_id,
        guru_id: item.guru_id,
    });
    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Reset form
const resetForm = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
};

// Submit
const submit = () => {
    const options = { onSuccess: resetForm, preserveScroll: true };
    if (isEditMode.value) form.put(route('admin.jadwal.update', form.id), options);
    else form.post(route('admin.jadwal.store'), options);
};

// Delete
const confirmDelete = item => { jadwalToDelete.value = item; showDeleteModal.value = true; };
const closeModal = () => { showDeleteModal.value = false; jadwalToDelete.value = null; };
const destroyJadwal = () => {
    form.delete(route('admin.jadwal.destroy', jadwalToDelete.value.id), { onSuccess: closeModal, preserveScroll: true });
};

// Format jam
const formatTime = t => t ? t.slice(0, 5) : '-';
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
                <CheckCircleIcon class="h-6 w-6 mr-3" /> <span class="font-medium">{{ flash.success }}</span>
            </div>
            <div v-if="flash.error" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg flex items-center shadow-md">
                <ExclamationCircleIcon class="h-6 w-6 mr-3" /> <span class="font-medium">{{ flash.error }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Form -->
                <div class="md:col-span-1 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ formTitle }}</h3>
                    <form @submit.prevent="submit" class="space-y-4">

                        <!-- Tipe -->
                        <div>
                            <InputLabel for="tipe" value="Tipe Jadwal" />
                            <select id="tipe" v-model="form.tipe" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" required>
                                <option value="pelajaran">Pelajaran (Mingguan)</option>
                                <option value="acara">Acara Sekolah (Tanggal Spesifik)</option>
                            </select>
                            <InputError :message="form.errors.tipe" class="mt-2" />
                        </div>

                        <!-- Hari -->
                        <div v-if="form.tipe==='pelajaran'">
                            <InputLabel for="hari" value="Hari" />
                            <select id="hari" v-model="form.hari" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option value="" disabled>Pilih Hari</option>
                                <option v-for="day in daysOfWeek" :key="day" :value="day">{{ day }}</option>
                            </select>
                            <InputError :message="form.errors.hari" class="mt-2" />
                        </div>

                        <!-- Tanggal -->
                        <div v-if="form.tipe==='acara'">
                            <InputLabel for="tanggal" value="Tanggal Acara" />
                            <TextInput id="tanggal" v-model="form.tanggal" type="date" class="mt-1 block w-full" />
                            <InputError :message="form.errors.tanggal" class="mt-2" />
                        </div>

                        <!-- Mata Pelajaran -->
                        <div>
                            <InputLabel for="mata_pelajaran_id" value="Mata Pelajaran / Acara" />
                            <select id="mata_pelajaran_id" v-model="form.mata_pelajaran_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" required>
                                <option value="" disabled>Pilih Mata Pelajaran / Acara</option>
                                <option v-for="mapel in mataPelajaranOptions" :key="mapel.id" :value="mapel.id">{{ mapel.nama }}</option>
                            </select>
                            <InputError :message="form.errors.mata_pelajaran_id" class="mt-2" />
                        </div>

                        <!-- Kelas -->
                        <div v-if="form.tipe==='pelajaran'">
                            <InputLabel for="kelas_id" value="Kelas" />
                            <select id="kelas_id" v-model="form.kelas_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option value="" disabled>Pilih Kelas</option>
                                <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">{{ kelas.nama_kelas }}</option>
                            </select>
                            <InputError :message="form.errors.kelas_id" class="mt-2" />
                        </div>

                        <!-- Guru -->
                        <div>
                            <InputLabel for="guru_id" value="Guru Pengajar (Opsional)" />
                            <select id="guru_id" v-model="form.guru_id" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option :value="null">Tidak ada guru spesifik</option>
                                <option v-for="guru in guruOptions" :key="guru.id" :value="guru.id">{{ guru.name }}</option>
                            </select>
                            <InputError :message="form.errors.guru_id" class="mt-2" />
                        </div>

                        <!-- Jam -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="jam_mulai" value="Jam Mulai" />
                                <TextInput id="jam_mulai" v-model="form.jam_mulai" type="time" class="mt-1 block w-full" />
                                <InputError :message="form.errors.jam_mulai" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="jam_selesai" value="Jam Selesai" />
                                <TextInput id="jam_selesai" v-model="form.jam_selesai" type="time" class="mt-1 block w-full" />
                                <InputError :message="form.errors.jam_selesai" class="mt-2" />
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <InputLabel for="deskripsi" value="Deskripsi (Opsional)" />
                            <textarea id="deskripsi" v-model="form.deskripsi" rows="3" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                            <InputError :message="form.errors.deskripsi" class="mt-2" />
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <SecondaryButton v-if="isEditMode" @click.prevent="resetForm" type="button">Batal</SecondaryButton>
                            <PrimaryButton :disabled="form.processing">{{ isEditMode ? 'Update Jadwal' : 'Simpan Jadwal' }}</PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Tabel Jadwal -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Filter Kelas -->
                    <div class="mb-4 flex items-center space-x-2">
                        <InputLabel for="kelas_filter" value="Pilih Kelas / Jurusan" />
                        <input type="text" placeholder="Cari kelas atau jurusan..." v-model="kelasSearch" class="ml-2 mt-1 block rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                        <select v-model="selectedKelasId" class="ml-2 mt-1 block rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">Semua Kelas</option>
                            <option v-for="kelas in filteredKelasOptions" :key="kelas.id" :value="kelas.id">{{ kelas.nama_kelas }}</option>
                        </select>
                    </div>

                    <!-- Pelajaran & Acara -->
                    <div v-for="kelas in kelasOptions.filter(k => !selectedKelasId || k.id===selectedKelasId)" :key="kelas.id">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-6">
                            <h4 class="font-bold text-lg mb-2 text-gray-700 dark:text-gray-300">{{ kelas.nama_kelas }}</h4>
                            <div v-for="hari in daysOfWeek" :key="hari" class="mb-4">
                                <h5 class="font-medium text-gray-600 dark:text-gray-400 border-b dark:border-gray-700 pb-1 mb-2">{{ hari }}</h5>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700 rounded-lg">
                                    <li v-for="item in filteredJadwalPelajaran[kelas.id]?.[hari]" :key="item.id" class="p-3 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div>
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ item.mata_pelajaran?.nama }}</span>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ formatTime(item.jam_mulai) }} - {{ formatTime(item.jam_selesai) }})</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="editJadwal(item)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-100 dark:hover:bg-gray-600"><PencilIcon class="h-5 w-5" /></button>
                                            <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 dark:hover:bg-gray-600"><TrashIcon class="h-5 w-5" /></button>
                                        </div>
                                    </li>
                                    <li v-if="!filteredJadwalPelajaran[kelas.id]?.[hari]" class="p-3 text-sm text-gray-400 dark:text-gray-500">Tidak ada jadwal pelajaran.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Acara -->
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md">
                        <h3 class="font-semibold text-xl mb-4 text-gray-900 dark:text-gray-100">Jadwal Acara Sekolah</h3>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700 border dark:border-gray-700 rounded-lg">
                            <li v-for="item in jadwalAcara" :key="'acara-'+item.id" class="p-3 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700">
                                <div>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ item.mata_pelajaran?.nama }}</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">({{ item.tanggal }} {{ formatTime(item.jam_mulai) }} - {{ formatTime(item.jam_selesai) }})</span>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ item.deskripsi }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Penyelenggara: {{ item.guru?.name ?? 'Umum' }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="editJadwal(item)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-100 dark:hover:bg-gray-600"><PencilIcon class="h-5 w-5" /></button>
                                    <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 dark:hover:bg-gray-600"><TrashIcon class="h-5 w-5" /></button>
                                </div>
                            </li>
                            <li v-if="jadwalAcara.length===0" class="p-3 text-sm text-gray-400 dark:text-gray-500">Tidak ada acara sekolah.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Hapus -->
            <Modal :show="showDeleteModal" @close="closeModal">
                <div class="p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Konfirmasi Hapus Jadwal</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Apakah Anda yakin ingin menghapus jadwal "{{ jadwalToDelete?.mata_pelajaran?.nama }}"? Aksi ini tidak dapat dibatalkan.</p>
                    <div class="mt-6 flex justify-end space-x-3">
                        <SecondaryButton @click="closeModal">Batal</SecondaryButton>
                        <DangerButton @click="destroyJadwal" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">Ya, Hapus</DangerButton>
                    </div>
                </div>
            </Modal>
        </div>
    </AuthenticatedLayout>
</template>
