<script setup>
import { ref, computed, watchEffect } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { PencilIcon, TrashIcon, CheckCircleIcon, XCircleIcon, UsersIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    kelas: Array,
    jurusan: Array,
    guru: Array,
    flash: Object,
});

const form = useForm({
    id: null,
    nama_kelas: '',
    jenjang: '',
    jurusan_id: '',
    nomor_urut: '1',
    wali_kelas_id: '',
});

const confirmingDeletion = ref(false);
const kelasToDelete = ref(null);

const formTitle = computed(() => (form.id ? '✏️ Edit Kelas' : '➕ Tambah Kelas Baru'));

watchEffect(() => {
    if (form.jenjang && form.jurusan_id && form.nomor_urut) {
        const selectedJurusan = props.jurusan.find(j => j.id === form.jurusan_id);
        if (selectedJurusan) {
            // Ubah baris ini: Hapus `|| selectedJurusan.nama` karena `nama_singkat` tidak ada lagi
            const namaJurusan = selectedJurusan.nama; 
            form.nama_kelas = `${form.jenjang} ${namaJurusan} ${form.nomor_urut}`;
        }
    } else if (!form.id) {
        form.nama_kelas = '';
    }
});

const editKelas = (k) => {
    form.id = k.id;
    form.nama_kelas = k.nama_kelas;
    form.jurusan_id = k.jurusan_id;
    form.wali_kelas_id = k.wali_kelas_id;
    form.jenjang = k.jenjang;
    const nameParts = k.nama_kelas.split(' ');
    form.nomor_urut = nameParts.length > 0 ? nameParts[nameParts.length - 1] : '1';
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const resetForm = () => {
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (!form.nama_kelas && form.jenjang && form.jurusan_id && form.nomor_urut) {
        const selectedJurusan = props.jurusan.find(j => j.id === form.jurusan_id);
        if (selectedJurusan) {
            // Ubah baris ini: Hapus `|| selectedJurusan.nama`
            const namaJurusan = selectedJurusan.nama; 
            form.nama_kelas = `${form.jenjang} ${namaJurusan} ${form.nomor_urut}`;
        }
    }
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

const confirmDelete = (k) => {
    kelasToDelete.value = k;
    confirmingDeletion.value = true;
};

const deleteKelas = () => {
    form.delete(route('admin.kelas.destroy', kelasToDelete.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true,
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
    kelasToDelete.value = null;
};
</script>

<template>
    <Head title="Manajemen Kelas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800">🏫 Manajemen Kelas</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola semua kelas, jurusan, dan wali kelas dengan mudah.</p>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1">
                    <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ formTitle }}</h3>

                        <div v-if="flash?.success" class="mb-4 flex items-center gap-2 rounded-md bg-green-50 border border-green-200 p-3 text-green-700">
                            <CheckCircleIcon class="h-5 w-5" />
                            <span>{{ flash.success }}</span>
                        </div>
                        <div v-if="flash?.error" class="mb-4 flex items-center gap-2 rounded-md bg-red-50 border border-red-200 p-3 text-red-700">
                            <XCircleIcon class="h-5 w-5" />
                            <span>{{ flash.error }}</span>
                        </div>

                        <form @submit.prevent="submit" class="space-y-5">
                            <div>
                                <InputLabel for="jenjang" value="Jenjang" />
                                <select id="jenjang" v-model="form.jenjang" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="" disabled>Pilih Jenjang</option>
                                    <option value="X">X (Sepuluh)</option>
                                    <option value="XI">XI (Sebelas)</option>
                                    <option value="XII">XII (Dua Belas)</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jenjang" />
                            </div>

                            <div>
                                <InputLabel for="jurusan_id" value="Jurusan" />
                                <select id="jurusan_id" v-model="form.jurusan_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="" disabled>Pilih Jurusan</option>
                                    <option v-for="j in jurusan" :key="j.id" :value="j.id">{{ j.nama }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.jurusan_id" />
                            </div>

                            <div>
                                <InputLabel for="nomor_urut" value="Nomor Urut Kelas" />
                                <TextInput id="nomor_urut" v-model="form.nomor_urut" type="number" min="1" class="mt-1 block w-full rounded-lg" required placeholder="cth: 1"/>
                                <InputError class="mt-2" :message="form.errors.nomor_urut" />
                            </div>

                            <div>
                                <InputLabel for="nama_kelas" value="Nama Kelas (Otomatis)" />
                                <TextInput id="nama_kelas" v-model="form.nama_kelas" type="text" class="mt-1 block w-full rounded-lg bg-gray-100 text-gray-700" readonly placeholder="Akan terisi otomatis"/>
                                <InputError class="mt-2" :message="form.errors.nama_kelas" />
                            </div>

                            <div>
                                <InputLabel for="wali_kelas_id" value="Wali Kelas" />
                                <select id="wali_kelas_id" v-model="form.wali_kelas_id" class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Guru (Opsional)</option>
                                    <option v-for="g in guru" :key="g.id" :value="g.id">{{ g.name }}</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.wali_kelas_id" />
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                <SecondaryButton @click="resetForm" type="button" v-if="form.isDirty">
                                    Batal
                                </SecondaryButton>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    {{ form.id ? 'Update Kelas' : 'Simpan Kelas' }}
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white shadow-md rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Nama Kelas</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Jenjang</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Jurusan</th>
                                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Wali Kelas</th>
                                        <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="k in kelas" :key="k.id" class="hover:bg-indigo-50/50 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ k.nama_kelas }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ k.jenjang }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ k.jurusan.nama }}</td>
                                        <td class="px-6 py-4 text-gray-600">{{ k.wali_kelas?.name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-right flex gap-2 justify-end">
                                            <Link :href="route('admin.kelas.manage-siswa', k.id)" classm="p-2 rounded-full text-blue-600 hover:bg-blue-100 transition" title="Kelola Siswa">
                                                <UsersIcon class="h-5 w-5"/>
                                            </Link>
                                            <button @click="editKelas(k)" class="p-2 rounded-full text-indigo-600 hover:bg-indigo-100 transition" title="Edit">
                                                <PencilIcon class="h-5 w-5"/>
                                            </button>
                                            <button @click="confirmDelete(k)" class="p-2 rounded-full text-red-600 hover:bg-red-100 transition" title="Hapus">
                                                <TrashIcon class="h-5 w-5"/>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="kelas.length === 0">
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
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

        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900">⚠️ Hapus Kelas?</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Apakah Anda yakin ingin menghapus kelas 
                    <span class="font-medium text-gray-900">"{{ kelasToDelete?.nama_kelas }}"</span>? 
                    Pastikan tidak ada siswa yang terdaftar di kelas ini. Aksi ini <span class="font-semibold text-red-600">tidak dapat dibatalkan</span>.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="closeModal"> Batal </SecondaryButton>
                    <DangerButton @click="deleteKelas" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Ya, Hapus
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>