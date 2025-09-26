<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { XCircleIcon, CheckCircleIcon, UserPlusIcon, UserMinusIcon } from '@heroicons/vue/24/outline'; // Tambahkan icons
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'; // Untuk modal pencarian siswa

const props = defineProps({
    kelas: Object, // The current class object
    assignedStudents: Array, // Students already in this class
    unassignedStudents: Array, // Students not in this class, available to be added
    flash: Object,
});

// Form untuk menambahkan siswa
const addStudentForm = useForm({
    siswa_id: '',
});

// State untuk modal pencarian siswa
const showAddStudentModal = ref(false);
const searchKeyword = ref('');
const selectedStudentId = ref(null); // Untuk menyimpan ID siswa yang dipilih dari dropdown

// Computed property untuk memfilter siswa yang belum terdaftar berdasarkan kata kunci pencarian
const filteredUnassignedStudents = computed(() => {
    if (!searchKeyword.value) {
        return props.unassignedStudents;
    }
    const lowerCaseKeyword = searchKeyword.value.toLowerCase();
    return props.unassignedStudents.filter(student =>
        student.name.toLowerCase().includes(lowerCaseKeyword)
    );
});

// Fungsi untuk membuka modal penambahan siswa
const openAddStudentModal = () => {
    showAddStudentModal.value = true;
    searchKeyword.value = ''; // Reset pencarian
    selectedStudentId.value = null; // Reset pilihan
    addStudentForm.reset(); // Reset form
};

// Fungsi untuk menutup modal penambahan siswa
const closeAddStudentModal = () => {
    showAddStudentModal.value = false;
};

// Fungsi untuk submit penambahan siswa
const submitAddStudent = () => {
    if (selectedStudentId.value) {
        addStudentForm.siswa_id = selectedStudentId.value;
        addStudentForm.post(route('admin.kelas.assign-siswa', props.kelas.id), {
            onSuccess: () => {
                closeAddStudentModal();
                // Inertia secara otomatis akan me-render ulang halaman dengan props terbaru
            },
            onError: () => {
                // Handle error if any
            },
            preserveScroll: true,
        });
    }
};

// Fungsi untuk menghapus siswa dari kelas
const detachStudent = (studentId) => {
    if (confirm('Apakah Anda yakin ingin menghapus siswa ini dari kelas?')) {
        addStudentForm.delete(route('admin.kelas.detach-siswa', [props.kelas.id, studentId]), {
            onSuccess: () => {
                // Inertia secara otomatis akan me-render ulang halaman dengan props terbaru
            },
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="`Kelola Siswa Kelas ${kelas.nama_kelas}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800">
                👥 Kelola Siswa Kelas <span class="text-indigo-600">{{ kelas.nama_kelas }}</span>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Tambahkan atau hapus siswa dari kelas {{ kelas.jenjang }} {{ kelas.jurusan.nama }}.
            </p>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-md rounded-2xl p-6 border border-gray-100">

                    <div v-if="flash?.success" class="mb-4 flex items-center gap-2 rounded-md bg-green-50 border border-green-200 p-3 text-green-700">
                        <CheckCircleIcon class="h-5 w-5" />
                        <span>{{ flash.success }}</span>
                    </div>
                    <div v-if="flash?.error" class="mb-4 flex items-center gap-2 rounded-md bg-red-50 border border-red-200 p-3 text-red-700">
                        <XCircleIcon class="h-5 w-5" />
                        <span>{{ flash.error }}</span>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Siswa dalam Kelas</h3>
                        <PrimaryButton @click="openAddStudentModal">
                            <UserPlusIcon class="h-4 w-4 mr-2" /> Tambah Siswa
                        </PrimaryButton>
                    </div>

                    <div v-if="assignedStudents.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase">Nama Siswa</th>
                                    <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="student in assignedStudents" :key="student.id" class="hover:bg-indigo-50/50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ student.name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="detachStudent(student.id)" class="p-2 rounded-full text-red-600 hover:bg-red-100 transition" title="Hapus dari Kelas">
                                            <UserMinusIcon class="h-5 w-5"/>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center py-10 text-gray-500">
                        Belum ada siswa yang terdaftar di kelas ini.
                    </div>
                </div>
            </div>
        </div>

        <TransitionRoot as="template" :show="showAddStudentModal">
            <Dialog as="div" class="relative z-10" @close="closeAddStudentModal">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
                </TransitionChild>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                            <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                                <div>
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                        <UserPlusIcon class="h-6 w-6 text-blue-600" aria-hidden="true" />
                                    </div>
                                    <div class="mt-3 text-center sm:mt-5">
                                        <DialogTitle as="h3" class="text-base font-semibold leading-6 text-gray-900">Tambahkan Siswa ke Kelas</DialogTitle>
                                        <div class="mt-4">
                                            <form @submit.prevent="submitAddStudent">
                                                <div class="mb-4">
                                                    <InputLabel for="search-student" value="Cari & Pilih Siswa" />
                                                    <input 
                                                        type="text" 
                                                        id="search-student" 
                                                        v-model="searchKeyword" 
                                                        placeholder="Cari nama siswa..." 
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    />
                                                    <select 
                                                        v-model="selectedStudentId" 
                                                        class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                        size="5"
                                                        required
                                                    >
                                                        <option v-if="filteredUnassignedStudents.length === 0" disabled>Tidak ada siswa tersedia.</option>
                                                        <option 
                                                            v-for="student in filteredUnassignedStudents" 
                                                            :key="student.id" 
                                                            :value="student.id"
                                                        >
                                                            {{ student.name }}
                                                        </option>
                                                    </select>
                                                    <InputError class="mt-2" :message="addStudentForm.errors.siswa_id" />
                                                </div>
                                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                                    <PrimaryButton type="submit" :class="{ 'opacity-25': addStudentForm.processing }" :disabled="addStudentForm.processing || !selectedStudentId" class="sm:col-start-2">
                                                        Tambah
                                                    </PrimaryButton>
                                                    <SecondaryButton type="button" @click="closeAddStudentModal" class="mt-3 sm:col-start-1 sm:mt-0">
                                                        Batal
                                                    </SecondaryButton>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </AuthenticatedLayout>
</template>