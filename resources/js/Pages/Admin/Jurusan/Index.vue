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

// Props yang diterima dari JurusanController
const props = defineProps({
    jurusan: Array,
    flash: Object,
});

// State untuk form tambah/edit
const form = useForm({
    id: null,
    nama: '',
});

// State untuk modal konfirmasi hapus
const confirmingDeletion = ref(false);
const jurusanToDelete = ref(null);

// Computed property untuk menentukan judul form
const formTitle = computed(() => (form.id ? 'Edit Jurusan' : 'Tambah Jurusan Baru'));

// Fungsi untuk menampilkan form edit
const editJurusan = (j) => {
    form.id = j.id;
    form.nama = j.nama;
    window.scrollTo({ top: 0, behavior: 'smooth' }); // Scroll ke atas untuk fokus ke form
};

// Fungsi untuk mereset form ke mode "Tambah Baru"
const resetForm = () => {
    form.reset();
    form.clearErrors();
};

// Fungsi untuk submit form (bisa create atau update)
const submit = () => {
    if (form.id) {
        // Mode Update
        form.put(route('admin.jurusan.update', form.id), {
            onSuccess: () => resetForm(),
        });
    } else {
        // Mode Create
        form.post(route('admin.jurusan.store'), {
            onSuccess: () => resetForm(),
        });
    }
};

// Fungsi untuk menampilkan modal konfirmasi hapus
const confirmDelete = (j) => {
    jurusanToDelete.value = j;
    confirmingDeletion.value = true;
};

// Fungsi untuk menjalankan penghapusan data
const deleteJurusan = () => {
    form.delete(route('admin.jurusan.destroy', jurusanToDelete.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true,
    });
};

// Fungsi untuk menutup modal
const closeModal = () => {
    confirmingDeletion.value = false;
    jurusanToDelete.value = null;
};
</script>

<template>
    <Head title="Manajemen Jurusan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Jurusan</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua jurusan yang tersedia di sekolah.</p>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Kolom Form Tambah/Edit -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ formTitle }}</h3>

                        <!-- Notifikasi Flash -->
                        <div v-if="flash.success" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md flex items-center">
                            <CheckCircleIcon class="h-5 w-5 mr-2" />
                            <span>{{ flash.success }}</span>
                        </div>
                        <div v-if="flash.error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md flex items-center">
                            <XCircleIcon class="h-5 w-5 mr-2" />
                            <span>{{ flash.error }}</span>
                        </div>

                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="nama" value="Nama Jurusan" />
                                <TextInput id="nama" v-model="form.nama" type="text" class="mt-1 block w-full" required autofocus autocomplete="off" placeholder="cth: Rekayasa Perangkat Lunak"/>
                                <InputError class="mt-2" :message="form.errors.nama" />
                            </div>

                            <div class="flex items-center justify-end mt-6 space-x-4">
                                <SecondaryButton @click="resetForm" type="button" v-if="form.id">
                                    Batal
                                </SecondaryButton>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ form.id ? 'Update Jurusan' : 'Simpan Jurusan' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kolom Tabel Daftar Jurusan -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jurusan</th>
                                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="j in jurusan" :key="j.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ j.nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <button @click="editJurusan(j)" class="text-indigo-600 hover:text-indigo-900 p-1 rounded-full hover:bg-indigo-100" title="Edit">
                                                <PencilIcon class="h-5 w-5"/>
                                            </button>
                                            <button @click="confirmDelete(j)" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100" title="Hapus">
                                                <TrashIcon class="h-5 w-5"/>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="jurusan.length === 0">
                                        <td colspan="2" class="px-6 py-10 text-center text-sm text-gray-500">
                                            Belum ada data jurusan. Silakan tambahkan melalui form di samping.
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
                    Apakah Anda yakin ingin menghapus jurusan ini?
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    Jurusan "{{ jurusanToDelete?.nama }}" akan dihapus secara permanen. Aksi ini tidak dapat dibatalkan. Pastikan tidak ada kelas yang masih terhubung dengan jurusan ini.
                </p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Batal </SecondaryButton>
                    <DangerButton class="ml-3" @click="deleteJurusan" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Ya, Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

