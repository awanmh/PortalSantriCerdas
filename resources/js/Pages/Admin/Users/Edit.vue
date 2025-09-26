<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

// Menerima props dari UserController@edit
const props = defineProps({
    user: Object,
    roles: Array,
    kelas: Array,
});

// Inisialisasi form dengan data user yang ada
const form = useForm({
    name: props.user.name,
    email: props.user.email,
    // Kita akan mengambil role pertama user sebagai default
    role: props.user.roles[0]?.name || null,
    // Kita akan mencari kelas user
    kelas_id: props.user.kelas[0]?.id || null,
});

// Computed property untuk mengecek apakah user adalah siswa
const isSiswa = computed(() => {
    // Cek baik dari role yang ada di form maupun data asli
    return form.role === 'siswa' || props.user.roles.some(r => r.name === 'siswa');
});


// Fungsi untuk mengirim data update
const submit = () => {
    form.patch(route('admin.users.update', props.user.id), {
        preserveScroll: true,
        // onSuccess: () => form.reset(), // Opsional: reset form setelah berhasil
    });
};
</script>

<template>
    <Head :title="'Edit Pengguna - ' + user.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Pengguna: {{ user.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit">
                            <!-- Input Nama -->
                            <div>
                                <InputLabel for="name" value="Nama" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Input Email -->
                            <div class="mt-4">
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                            
                            <!-- Pilihan Role -->
                             <div class="mt-4">
                                <InputLabel for="role" value="Role Pengguna" />
                                 <select v-model="form.role" id="role" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option :value="null" disabled>-- Pilih Role --</option>
                                     <option v-for="role in roles" :key="role.id" :value="role.name">
                                         {{ role.name }}
                                     </option>
                                 </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                             <!-- Pilihan Kelas (hanya jika siswa) -->
                            <div v-if="isSiswa" class="mt-4">
                                <InputLabel for="kelas" value="Kelas" />
                                 <select v-model="form.kelas_id" id="kelas" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                     <option :value="null">-- Tidak Masuk Kelas --</option>
                                     <option v-for="k in kelas" :key="k.id" :value="k.id">
                                         {{ k.nama_kelas }}
                                     </option>
                                 </select>
                                <InputError class="mt-2" :message="form.errors.kelas_id" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Simpan Perubahan
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
