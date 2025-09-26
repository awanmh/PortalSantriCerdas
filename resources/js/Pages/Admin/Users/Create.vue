<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

/**
 * Mendefinisikan props yang diterima dari UserController,
 * dalam hal ini adalah daftar semua role yang tersedia.
 */
const props = defineProps({
    roles: {
        type: Array,
        required: true,
    }
});

/**
 * Menginisialisasi form dengan state management dari Inertia.
 * Ini akan menangani data input, error validasi, dan status loading.
 */
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '', // Nilai awal untuk pilihan role
});

/**
 * Fungsi yang akan dipanggil saat form di-submit.
 */
const submit = () => {
    // Mengirim data form ke rute 'admin.users.store'
    form.post(route('admin.users.store'), {
        // Setelah submit selesai (baik sukses maupun gagal),
        // kosongkan kembali field password.
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <Head title="Tambah Pengguna Baru" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pengguna Baru</h2>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <!-- Nama -->
                            <div class="mb-6">
                                <InputLabel for="name" value="Nama Lengkap" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Email -->
                            <div class="mb-6">
                                <InputLabel for="email" value="Alamat Email" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>
                            
                            <!-- Pilihan Role -->
                            <div class="mb-6">
                                <InputLabel for="role" value="Peran (Role)" />
                                <select id="role" v-model="form.role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="" disabled>-- Pilih peran untuk pengguna --</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <!-- Password -->
                            <div class="mb-6">
                                <InputLabel for="password" value="Password" />
                                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Konfirmasi Password -->
                            <div class="mb-6">
                                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('admin.users.index')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                                    Batal
                                </Link>

                                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Simpan Pengguna
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
