<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    jurusan: '',
    angkatan: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <div class="login-container">
        
        <div class="login-card">

            <header>
                <h1>Portal Santri Cerdas</h1>
                <p>Daftarkan akun baru untuk memulai.</p>
            </header>

            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="name" value="Nama Lengkap" class="text-left"/>
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

                <div class="mt-4">
                    <InputLabel for="email" value="Email" class="text-left"/>
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

                <div class="mt-4">
                    <InputLabel for="jurusan" value="Jurusan" class="text-left"/>
                    <TextInput
                        id="jurusan"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.jurusan"
                        required
                        autocomplete="jurusan"
                    />
                    <InputError class="mt-2" :message="form.errors.jurusan" />
                </div>

                <div class="mt-4">
                    <InputLabel for="angkatan" value="Angkatan" class="text-left"/>
                    <TextInput
                        id="angkatan"
                        type="number"
                        class="mt-1 block w-full"
                        v-model="form.angkatan"
                        required
                        autocomplete="angkatan"
                    />
                    <InputError class="mt-2" :message="form.errors.angkatan" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" class="text-left"/>
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-left"/>
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Link
                        :href="route('login')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Sudah punya akun?
                    </Link>

                    <button class="btn ms-4" :disabled="form.processing">
                        Register
                    </button>
                </div>
            </form>
            
            <footer class="mt-8">
                &copy; {{ new Date().getFullYear() }} Portal Santri Cerdas
            </footer>
        </div>
    </div>
</template>