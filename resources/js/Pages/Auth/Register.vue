<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

// Props jurusan
defineProps({
    jurusan: Array,
});

// State form
const form = useForm({
    name: '',
    email: '',
    jurusan_id: '',
    angkatan: '',
    password: '',
    password_confirmation: '',
});

// Submit form
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Daftar Akun Siswa" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
            <div class="text-center mt-4">
                <h1
                    class="text-2xl font-bold text-gray-800 dark:text-gray-200 font-pesantren"
                >
                    Pendaftaran Akun Siswa
                </h1>
                <p
                    class="text-sm text-gray-600 dark:text-gray-400 font-body"
                >
                    Silakan isi data diri Anda dengan benar.
                </p>
            </div>
        </template>

        <form @submit.prevent="submit" class="space-y-6 font-body">
            <!-- Nama Lengkap -->
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Contoh: Muhammad Al-Fatih"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Alamat Email -->
            <div>
                <InputLabel for="email" value="Alamat Email Sekolah" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    required
                    autocomplete="username"
                    placeholder="namaanda@smkalikhlash.sch.id"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Jurusan -->
            <div>
                <InputLabel for="jurusan_id" value="Jurusan" />
                <select
                    id="jurusan_id"
                    v-model="form.jurusan_id"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm"
                    required
                >
                    <option value="" disabled>-- Pilih Jurusan --</option>
                    <option
                        v-for="j in jurusan"
                        :key="j.id"
                        :value="j.id"
                    >
                        {{ j.nama }}
                    </option>
                </select>
                <InputError class="mt-2" :message="form.errors.jurusan_id" />
            </div>

            <!-- Tahun Angkatan -->
            <div>
                <InputLabel for="angkatan" value="Tahun Angkatan" />
                <TextInput
                    id="angkatan"
                    v-model="form.angkatan"
                    type="number"
                    class="mt-1 block w-full placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    required
                    placeholder="Contoh: 2024"
                />
                <InputError class="mt-2" :message="form.errors.angkatan" />
            </div>

            <!-- Password -->
            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full placeholder:text-gray-400 dark:placeholder:text-gray-500"
                    required
                    autocomplete="new-password"
                    placeholder="Ketik ulang password Anda"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Aksi -->
            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-green-700 dark:hover:text-green-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800"
                >
                    Sudah punya akun?
                </Link>

                <PrimaryButton
                    class="ms-4 bg-gradient-to-r from-green-600 to-blue-600 hover:opacity-90 text-white"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Daftar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
