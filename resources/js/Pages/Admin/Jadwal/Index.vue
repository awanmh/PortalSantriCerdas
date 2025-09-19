<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { PencilIcon, TrashIcon, XMarkIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

// Mendefinisikan props yang diterima dari JadwalController
const props = defineProps({
    jadwal: {
        type: Object,
        required: true,
    },
    kelasOptions: {
        type: Array,
        required: true,
    },
    guruOptions: {
        type: Array,
        required: true,
    },
    flash: {
        type: Object,
        default: () => ({}),
    },
});

const isEditMode = ref(false);
const showDeleteModal = ref(false);
const jadwalToDelete = ref(null);

// Menggunakan useForm dari Inertia untuk menangani form state, validasi, dan submission
const form = useForm({
    id: null,
    mata_pelajaran: '',
    deskripsi: '',
    tanggal: '',
    jam_mulai: '',
    jam_selesai: '',
    tipe: 'pelajaran',
    kelas_id: '',
    guru_id: '',
});

// Fungsi untuk menyiapkan form dalam mode edit
const editJadwal = (item) => {
    isEditMode.value = true;
    form.id = item.id;
    form.mata_pelajaran = item.mata_pelajaran;
    form.deskripsi = item.deskripsi;
    form.tanggal = item.tanggal;
    form.jam_mulai = item.jam_mulai;
    form.jam_selesai = item.jam_selesai;
    form.tipe = item.tipe;
    form.kelas_id = item.kelas_id;
    form.guru_id = item.guru_id;
    form.clearErrors();
};

// Fungsi untuk mereset form ke state awal (mode tambah)
const resetForm = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
};

// Fungsi untuk submit form (bisa untuk create atau update)
const submit = () => {
    if (isEditMode.value) {
        form.put(route('admin.jadwal.update', form.id), {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post(route('admin.jadwal.store'), {
            onSuccess: () => resetForm(),
        });
    }
};

// Fungsi untuk membuka modal konfirmasi hapus
const confirmDelete = (item) => {
    jadwalToDelete.value = item;
    showDeleteModal.value = true;
};

// Fungsi untuk menutup modal
const closeModal = () => {
    showDeleteModal.value = false;
    jadwalToDelete.value = null;
};

// Fungsi untuk menghapus jadwal setelah konfirmasi
const destroyJadwal = () => {
    form.delete(route('admin.jadwal.destroy', jadwalToDelete.value.id), {
        onSuccess: () => closeModal(),
    });
};

const formTitle = computed(() => isEditMode.value ? 'Edit Jadwal' : 'Tambah Jadwal Baru');
</script>

<template>
    <Head title="Manajemen Jadwal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Jadwal Pelajaran</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Notifikasi Flash Message -->
                <div v-if="flash.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg relative flex items-center shadow-md" role="alert">
                    <CheckCircleIcon class="h-6 w-6 mr-3"/>
                    <span class="block sm:inline font-medium">{{ flash.success }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kolom Form -->
                    <div class="md:col-span-1">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ formTitle }}</h3>
                            <form @submit.prevent="submit">
                                <div class="space-y-4">
                                    <!-- Mata Pelajaran -->
                                    <div>
                                        <InputLabel for="mata_pelajaran" value="Mata Pelajaran" />
                                        <TextInput id="mata_pelajaran" v-model="form.mata_pelajaran" type="text" class="mt-1 block w-full" required />
                                        <InputError class="mt-2" :message="form.errors.mata_pelajaran" />
                                    </div>

                                    <!-- Tanggal -->
                                    <div>
                                        <InputLabel for="tanggal" value="Tanggal" />
                                        <TextInput id="tanggal" v-model="form.tanggal" type="date" class="mt-1 block w-full" required />
                                        <InputError class="mt-2" :message="form.errors.tanggal" />
                                    </div>

                                    <!-- Jam Mulai & Selesai -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="jam_mulai" value="Jam Mulai" />
                                            <TextInput id="jam_mulai" v-model="form.jam_mulai" type="time" class="mt-1 block w-full" required />
                                            <InputError class="mt-2" :message="form.errors.jam_mulai" />
                                        </div>
                                        <div>
                                            <InputLabel for="jam_selesai" value="Jam Selesai" />
                                            <TextInput id="jam_selesai" v-model="form.jam_selesai" type="time" class="mt-1 block w-full" required />
                                            <InputError class="mt-2" :message="form.errors.jam_selesai" />
                                        </div>
                                    </div>

                                    <!-- Kelas -->
                                    <div>
                                        <InputLabel for="kelas_id" value="Kelas" />
                                        <select id="kelas_id" v-model="form.kelas_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="" disabled>Pilih Kelas</option>
                                            <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">{{ kelas.nama }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.kelas_id" />
                                    </div>

                                    <!-- Guru Pengajar -->
                                    <div>
                                        <InputLabel for="guru_id" value="Guru Pengajar (Opsional)" />
                                        <select id="guru_id" v-model="form.guru_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                            <option value="">Tidak ada guru spesifik</option>
                                            <option v-for="guru in guruOptions" :key="guru.id" :value="guru.id">{{ guru.name }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.guru_id" />
                                    </div>

                                    <!-- Tipe -->
                                    <div>
                                        <InputLabel for="tipe" value="Tipe Jadwal" />
                                        <select id="tipe" v-model="form.tipe" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="pelajaran">Pelajaran</option>
                                            <option value="acara">Acara Sekolah</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.tipe" />
                                    </div>

                                    <!-- Deskripsi -->
                                    <div>
                                        <InputLabel for="deskripsi" value="Deskripsi (Opsional)" />
                                        <textarea id="deskripsi" v-model="form.deskripsi" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                        <InputError class="mt-2" :message="form.errors.deskripsi" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-end mt-6 space-x-4">
                                    <SecondaryButton v-if="isEditMode" @click.prevent="resetForm">
                                        Batal
                                    </SecondaryButton>
                                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        {{ isEditMode ? 'Update Jadwal' : 'Simpan Jadwal' }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Kolom Tabel -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                             <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel & Tanggal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas & Guru</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                            <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-if="jadwal.data.length === 0">
                                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                                Belum ada jadwal yang dibuat.
                                            </td>
                                        </tr>
                                        <tr v-for="item in jadwal.data" :key="item.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">{{ item.mata_pelajaran }}</div>
                                                <div class="text-sm text-gray-500">{{ new Date(item.tanggal).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ item.kelas?.nama ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ item.guru?.name ?? 'Umum' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ item.jam_mulai }} - {{ item.jam_selesai }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button @click="editJadwal(item)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <PencilIcon class="h-5 w-5"/>
                                                </button>
                                                <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900">
                                                    <TrashIcon class="h-5 w-5"/>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Konfirmasi Hapus Jadwal
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus jadwal untuk mata pelajaran "{{ jadwalToDelete?.mata_pelajaran }}"? Aksi ini tidak dapat dibatalkan.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Batal </SecondaryButton>
                    <DangerButton class="ms-3" @click="destroyJadwal" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Ya, Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

