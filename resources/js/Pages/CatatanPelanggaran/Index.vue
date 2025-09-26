<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { PencilIcon, TrashIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

// Menerima props dari CatatanPelanggaranController
const props = defineProps({
    catatanPelanggaran: {
        type: Object,
        required: true,
    },
    siswas: {
        type: Array,
        required: true,
    }
});

const isEditing = ref(false);
const showConfirmDeleteModal = ref(false);
const itemToDelete = ref(null);
const flash = computed(() => usePage().props.flash);

// Menggunakan useForm dari Inertia untuk state management form
const form = useForm({
    id: null,
    user_id: '',
    jenis: '',
    deskripsi: '',
    poin: 1,
    tanggal: new Date().toISOString().slice(0, 10), // Default tanggal hari ini
});

// Fungsi untuk submit form (bisa untuk create atau update)
const submit = () => {
    if (isEditing.value) {
        form.put(route('catatan-pelanggaran.update', form.id), {
            onSuccess: () => clearForm(),
        });
    } else {
        form.post(route('catatan-pelanggaran.store'), {
            onSuccess: () => form.reset(),
        });
    }
};

// Fungsi untuk mengisi form saat tombol edit ditekan
const editItem = (item) => {
    isEditing.value = true;
    form.id = item.id;
    form.user_id = item.user_id;
    form.jenis = item.jenis;
    form.deskripsi = item.deskripsi;
    form.poin = item.poin;
    form.tanggal = item.tanggal;
};

// Fungsi untuk membersihkan form
const clearForm = () => {
    isEditing.value = false;
    form.reset();
};

// Fungsi untuk menampilkan modal konfirmasi hapus
const confirmDelete = (item) => {
    itemToDelete.value = item;
    showConfirmDeleteModal.value = true;
};

// Fungsi untuk menghapus item
const deleteItem = () => {
    form.delete(route('catatan-pelanggaran.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            showConfirmDeleteModal.value = false;
            itemToDelete.value = null;
        },
    });
};

</script>

<template>
    <Head title="Catat Pelanggaran Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Catatan Pelanggaran Siswa
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Notifikasi Flash Message -->
                <div v-if="flash?.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded-lg flex items-center shadow-md" role="alert">
                    <CheckCircleIcon class="h-6 w-6 mr-3"/>
                    <span class="font-medium">{{ flash.success }}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kolom Form -->
                    <div class="md:col-span-1">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                {{ isEditing ? 'Edit Catatan' : 'Tambah Catatan Baru' }}
                            </h3>
                            <form @submit.prevent="submit" class="space-y-4">
                                <div>
                                    <InputLabel for="siswa" value="Pilih Siswa" />
                                    <select id="siswa" v-model="form.user_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        <option value="" disabled>-- Pilih Siswa --</option>
                                        <option v-for="siswa in siswas" :key="siswa.id" :value="siswa.id">{{ siswa.name }}</option>
                                    </select>
                                    <InputError :message="form.errors.user_id" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="jenis" value="Jenis Pelanggaran" />
                                    <TextInput id="jenis" v-model="form.jenis" type="text" class="mt-1 block w-full" placeholder="Contoh: Terlambat" />
                                    <InputError :message="form.errors.jenis" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="deskripsi" value="Deskripsi Singkat" />
                                    <textarea id="deskripsi" v-model="form.deskripsi" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                                    <InputError :message="form.errors.deskripsi" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="poin" value="Poin Pelanggaran" />
                                    <TextInput id="poin" v-model="form.poin" type="number" min="1" class="mt-1 block w-full" />
                                    <InputError :message="form.errors.poin" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="tanggal" value="Tanggal Kejadian" />
                                    <TextInput id="tanggal" v-model="form.tanggal" type="date" class="mt-1 block w-full" />
                                    <InputError :message="form.errors.tanggal" class="mt-2" />
                                </div>
                                <div class="flex items-center justify-end space-x-4">
                                    <SecondaryButton v-if="isEditing" @click.prevent="clearForm">Batal</SecondaryButton>
                                    <PrimaryButton :disabled="form.processing">{{ isEditing ? 'Update' : 'Simpan' }}</PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Kolom Tabel -->
                    <div class="md:col-span-2">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Siswa</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pelanggaran</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Poin</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tanggal</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Pelapor</th>
                                            <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-if="catatanPelanggaran.data.length === 0">
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                                        </tr>
                                        <tr v-for="item in catatanPelanggaran.data" :key="item.id">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ item.siswa.name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.jenis }}</td>
                                            <td class="px-6 py-4 text-sm text-center font-bold text-red-600">{{ item.poin }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.tanggal_formatted }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ item.pelapor.name }}</td>
                                            <td class="px-6 py-4 text-right text-sm font-medium space-x-2">
                                                <button @click="editItem(item)" class="text-indigo-600 hover:text-indigo-900"><PencilIcon class="w-5 h-5"/></button>
                                                <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900"><TrashIcon class="w-5 h-5"/></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                             <div v-if="catatanPelanggaran.links.length > 3" class="p-4 border-t border-gray-200 dark:border-gray-700">
                                <Pagination :links="catatanPelanggaran.links" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Hapus -->
        <Modal :show="showConfirmDeleteModal" @close="showConfirmDeleteModal = false" max-width="md">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Hapus Catatan Pelanggaran</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus catatan ini? Aksi ini tidak dapat dibatalkan.
                </p>
                <div class="mt-6 flex justify-end space-x-4">
                    <SecondaryButton @click="showConfirmDeleteModal = false">Batal</SecondaryButton>
                    <DangerButton @click="deleteItem" :disabled="form.processing">Hapus</DangerButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>

