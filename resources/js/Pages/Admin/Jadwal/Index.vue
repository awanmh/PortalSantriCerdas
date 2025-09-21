<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { PencilIcon, TrashIcon, CheckCircleIcon } from '@heroicons/vue/24/outline';

// Props dari Controller
const props = defineProps({
    jadwal: { type: Object, required: true },
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
    jam_mulai: '',
    jam_selesai: '',
    tipe: 'pelajaran',
    kelas_id: '',
    guru_id: '',
});

// Hari dalam minggu
const daysOfWeek = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];

// Form title
const formTitle = computed(() => isEditMode.value ? 'Edit Jadwal' : 'Tambah Jadwal Baru');

// Edit
const editJadwal = (item) => {
    isEditMode.value = true;
    form.id = item.id;
    form.mata_pelajaran = item.mata_pelajaran;
    form.deskripsi = item.deskripsi;
    form.hari = item.hari;
    form.jam_mulai = item.jam_mulai;
    form.jam_selesai = item.jam_selesai;
    form.tipe = item.tipe;
    form.kelas_id = item.kelas_id;
    form.guru_id = item.guru_id;
    form.clearErrors();
};

// Reset form
const resetForm = () => {
    isEditMode.value = false;
    form.reset();
    form.clearErrors();
};

// Submit form
const submit = () => {
    if (isEditMode.value) {
        form.put(route('admin.jadwal.update', form.id), { onSuccess: resetForm });
    } else {
        form.post(route('admin.jadwal.store'), { onSuccess: resetForm });
    }
};

// Hapus
const confirmDelete = (item) => { jadwalToDelete.value = item; showDeleteModal.value = true; };
const closeModal = () => { showDeleteModal.value = false; jadwalToDelete.value = null; };
const destroyJadwal = () => {
    form.delete(route('admin.jadwal.destroy', jadwalToDelete.value.id), { onSuccess: closeModal });
};
</script>

<template>
<Head title="Manajemen Jadwal" />
<AuthenticatedLayout>
    <template #header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Jadwal Mingguan</h2>
    </template>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Flash -->
        <div v-if="flash.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg flex items-center shadow-md">
            <CheckCircleIcon class="h-6 w-6 mr-3"/>
            <span class="block sm:inline font-medium">{{ flash.success }}</span>
        </div>

        <!-- Layout Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form -->
            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ formTitle }}</h3>
                <form @submit.prevent="submit" class="space-y-4">

                    <div>
                        <InputLabel for="mata_pelajaran" value="Mata Pelajaran" />
                        <TextInput id="mata_pelajaran" v-model="form.mata_pelajaran" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.mata_pelajaran" class="mt-2"/>
                    </div>

                    <div>
                        <InputLabel for="kelas_id" value="Kelas" />
                        <select id="kelas_id" v-model="form.kelas_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled>Pilih Kelas</option>
                            <option v-for="kelas in kelasOptions" :key="kelas.id" :value="kelas.id">{{ kelas.nama_kelas }}</option>
                        </select>
                        <InputError :message="form.errors.kelas_id" class="mt-2"/>
                    </div>

                    <div>
                        <InputLabel for="guru_id" value="Guru Pengajar (Opsional)" />
                        <select id="guru_id" v-model="form.guru_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Tidak ada guru spesifik</option>
                            <option v-for="guru in guruOptions" :key="guru.id" :value="guru.id">{{ guru.name }}</option>
                        </select>
                        <InputError :message="form.errors.guru_id" class="mt-2"/>
                    </div>

                    <div>
                        <InputLabel for="hari" value="Hari" />
                        <select id="hari" v-model="form.hari" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="" disabled>Pilih Hari</option>
                            <option v-for="day in daysOfWeek" :key="day" :value="day">{{ day }}</option>
                        </select>
                        <InputError :message="form.errors.hari" class="mt-2"/>
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
                        <InputLabel for="tipe" value="Tipe Jadwal" />
                        <select id="tipe" v-model="form.tipe" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="pelajaran">Pelajaran</option>
                            <option value="acara">Acara Sekolah</option>
                        </select>
                        <InputError :message="form.errors.tipe" class="mt-2"/>
                    </div>

                    <div>
                        <InputLabel for="deskripsi" value="Deskripsi (Opsional)" />
                        <textarea id="deskripsi" v-model="form.deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.deskripsi" class="mt-2"/>
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-4">
                        <SecondaryButton v-if="isEditMode" @click.prevent="resetForm">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                            {{ isEditMode ? 'Update Jadwal' : 'Simpan Jadwal' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>

            <!-- Tabel Jadwal -->
            <div class="md:col-span-2 space-y-6">
                <div v-for="kelas in kelasOptions" :key="kelas.id" class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-semibold text-lg mb-2">{{ kelas.nama_kelas }}</h3>
                    <div v-for="hari in daysOfWeek" :key="hari" class="mb-4">
                        <h4 class="font-medium text-gray-600">{{ hari }}</h4>
                        <ul class="border rounded-lg divide-y divide-gray-200">
                            <li v-for="item in jadwal.filter(j => j.kelas_id === kelas.id && j.hari === hari)" :key="item.id" class="p-2 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold">{{ item.mata_pelajaran }}</span>
                                    ({{ item.jam_mulai }} - {{ item.jam_selesai }})
                                    <span class="text-gray-500 ml-2">{{ item.guru?.name ?? 'Umum' }}</span>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="editJadwal(item)" class="text-indigo-600 hover:text-indigo-900">
                                        <PencilIcon class="h-5 w-5"/>
                                    </button>
                                    <button @click="confirmDelete(item)" class="text-red-600 hover:text-red-900">
                                        <TrashIcon class="h-5 w-5"/>
                                    </button>
                                </div>
                            </li>
                            <li v-if="jadwal.filter(j => j.kelas_id === kelas.id && j.hari === hari).length === 0" class="p-2 text-sm text-gray-400">
                                Tidak ada jadwal
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Hapus -->
        <Modal :show="showDeleteModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Konfirmasi Hapus Jadwal</h2>
                <p class="mt-1 text-sm text-gray-600">
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
