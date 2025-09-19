<script setup>
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { PencilIcon, TrashIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';

// Props yang diterima dari KelasController
const props = defineProps({
    kelas: Array,
    jurusan: Array,
    guru: Array,
    flash: Object,
});

// State untuk form tambah/edit kelas
const form = useForm({
    id: null,
    nama: '',
    jurusan_id: '',
    wali_kelas_id: '',
});

// State untuk modal konfirmasi hapus
const confirmingDeletion = ref(false);
const kelasToDelete = ref(null);

// Computed property untuk judul form
const formTitle = computed(() => (form.id ? 'Edit Kelas' : 'Tambah Kelas Baru'));

// Fungsi untuk mengisi form saat tombol edit ditekan
const editKelas = (k) => {
    form.id = k.id;
    form.nama = k.nama;
    form.jurusan_id = k.jurusan_id;
    form.wali_kelas_id = k.wali_kelas_id;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

// Fungsi untuk mereset form
const resetForm = () => {
    form.reset();
    form.clearErrors();
};

// Fungsi untuk submit form (create atau update)
const submit = () => {
    if (form.id) {
        form.put(route('admin.kelas.update', form.id), {
            onSuccess: () => resetForm(),
            preserveScroll: true,
        });
    } else {
        form.post(route('admin.kelas.store'), {
            onSuccess: () => resetForm(),
            preserveScroll: true,
        });
    }
};

// Fungsi untuk membuka modal konfirmasi hapus
const confirmDelete = (k) => {
    kelasToDelete.value = k;
    confirmingDeletion.value = true;
};

// Fungsi untuk menghapus data kelas
const deleteKelas = () => {
    form.delete(route('admin.kelas.destroy', kelasToDelete.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true,
    });
};

// Fungsi untuk menutup modal
const closeModal = () => {
    confirmingDeletion.value = false;
    kelasToDelete.value = null;
};
</script>

<template>
    <Head title="Manajemen Kelas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Kelas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua kelas, jurusan, dan wali kelas yang terdaftar.</p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Kolom Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ formTitle }}</h3>

                        <!-- Notifikasi Flash -->
                        <div v-if="flash?.success" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md flex items-center">
                            <CheckCircleIcon class="h-5 w-5 mr-2" />
                            <span>{{ flash.success }}</span>
                        </div>
                        <div v-if="flash?.error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md flex items-center">
                            <XCircleIcon class="h-5 w-5 mr-2" />
                            <span>{{ flash.error }}</span>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Nama Kelas -->
                            <div>
                                <InputLabel for="nama" value="Nama Kelas" />
                                <TextInput id="nama" v-model="form.nama" type="text" class="mt-1 block w-full" required placeholder="cth: XII RPL 1"/>
                                <InputError class="mt-2" :message="form.errors.nama" />
                            </div>

                            <!-- Pilihan Jurusan -->
                            <div class="mt-4">
                                <InputLabel for="jurusan_id" value="Jurusan" />
                                <select id="jurusan_id" v-model="form.jurusan_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled>Pilih Jurusan</option>
                                    <option v-for="j in jurusan" :key="j.id" :value="j.id">{{ j.nama }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jurusan_id" />
                            </div>

                            <!-- Pilihan Wali Kelas -->
                            <div class="mt-4">
                                <InputLabel for="wali_kelas_id" value="Wali Kelas" />
                                <select id="wali_kelas_id" v-model="form.wali_kelas_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="" disabled>Pilih Guru</option>
                                    <option v-for="g in guru" :key="g.id" :value="g.id">{{ g.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.wali_kelas_id" />
                            </div>

                            <div class="flex items-center justify-end mt-6 space-x-4">
                                 <SecondaryButton @click="resetForm" type="button" v-if="form.id">
                                    Batal
                                </SecondaryButton>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ form.id ? 'Update Kelas' : 'Simpan Kelas' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kolom Tabel -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Kelas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jurusan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wali Kelas</th>
                                        <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="k in kelas" :key="k.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ k.nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ k.jurusan.nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ k.wali_kelas?.name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button @click="editKelas(k)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-100" title="Edit">
                                                <PencilIcon class="h-5 w-5"/>
                                            </button>
                                            <button @click="confirmDelete(k)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100" title="Hapus">
                                                <TrashIcon class="h-5 w-5"/>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="kelas.length === 0">
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                            Belum ada data kelas yang ditambahkan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

         <!-- Modal Konfirmasi Hapus -->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Hapus Kelas?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus kelas "{{ kelasToDelete?.nama }}"? Pastikan tidak ada siswa yang terdaftar di kelas ini. Aksi ini tidak dapat dibatalkan.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Batal </SecondaryButton>
                    <DangerButton class="ml-3" @click="deleteKelas" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Ya, Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>