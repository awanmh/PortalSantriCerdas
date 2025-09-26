<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const photoInput = ref(null);
const photoPreview = ref(null);

const photoForm = useForm({
    photo: null,
});

const userRole = computed(() => {
    if (user && user.roles && user.roles.length > 0) {
        const roleName = user.roles[0];
        return roleName.charAt(0).toUpperCase() + roleName.slice(1);
    }
    return 'Tidak ada role';
});

const selectNewPhoto = () => {
    console.log('[DEBUG] Membuka dialog pemilihan file...');
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    console.log('[DEBUG] File foto dipilih:', photo);

    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
        console.log('[DEBUG] Pratinjau foto berhasil dibuat.');
    };
    reader.readAsDataURL(photo);

    photoForm.photo = photo;
};

const uploadPhoto = () => {
    console.log('[DEBUG] Memulai proses unggah foto...', photoForm.photo);
    const oldPhotoUrl = user.profile_photo_url; // Simpan URL lama untuk perbandingan

    photoForm.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('%c[DEBUG] SUKSES: Foto berhasil diunggah ke server.', 'color: green; font-weight: bold;');
            
            const newPhotoUrl = page.props.auth.user.profile_photo_url;
            console.log('[DEBUG] URL foto LAMA:', oldPhotoUrl);
            console.log('[DEBUG] URL foto BARU dari server:', newPhotoUrl);

            // Cek apakah URL benar-benar berubah
            if (newPhotoUrl !== oldPhotoUrl) {
                 console.log('%c[DEBUG] KONFIRMASI TERSIMPAN: URL foto telah berhasil diperbarui di server.', 'color: blue; font-weight: bold;');
            } else {
                 console.warn('[DEBUG] PERINGATAN: URL foto tidak berubah setelah diunggah.');
            }

            photoPreview.value = null;
            photoForm.reset();
            
            console.log('[DEBUG] Memuat ulang data halaman untuk menampilkan foto baru di UI...');
            // Memuat ulang props dari server untuk memperbarui `user.profile_photo_url` di seluruh halaman
            router.reload({ 
                preserveState: false, // false agar komponen dirender ulang dengan data baru
                onSuccess: () => {
                    console.log('%c[DEBUG] UI DIPERBARUI: Halaman selesai dimuat ulang. Foto profil seharusnya sudah tampil.', 'color: green; font-weight: bold;');
                }
             });
        },
        onError: (errors) => {
            console.error('%c[DEBUG] GAGAL: Terjadi error saat mengunggah foto.', 'color: red; font-weight: bold;');
            console.error('[DEBUG] Pesan error dari server:', errors);
            console.error('[DEBUG] Periksa aturan validasi di `app/Http/Requests/ProfileUpdateRequest.php`.');
        },
        onFinish: () => {
            console.log('[DEBUG] Proses unggah selesai (baik sukses maupun gagal).');
            if (photoInput.value) {
                photoInput.value.value = '';
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informasi Profil</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Perbarui informasi profil, alamat email, dan foto profil akun Anda.
            </p>
        </header>

        <!-- Form untuk Foto Profil -->
        <div class="mt-6 flex items-center gap-4">
            <img v-if="photoPreview" :src="photoPreview" :alt="user.name" class="h-20 w-20 rounded-full object-cover">
            <img v-else :src="user.profile_photo_url" :alt="user.name" class="h-20 w-20 rounded-full object-cover">

            <input
                ref="photoInput"
                type="file"
                class="hidden"
                @change="updatePhotoPreview"
            >

            <div class="flex flex-col gap-2">
                 <SecondaryButton @click.prevent="selectNewPhoto">
                    Pilih Foto Baru
                </SecondaryButton>

                <PrimaryButton
                    v-if="photoPreview"
                    @click="uploadPhoto"
                    :class="{ 'opacity-25': photoForm.processing }"
                    :disabled="photoForm.processing"
                >
                    Simpan Foto
                </PrimaryButton>
            </div>
            <InputError :message="photoForm.errors.photo" class="mt-2" />
        </div>


        <!-- Form untuk Informasi Profil (Nama, Email, dll.) -->
        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <!-- Menampilkan Role / Kedudukan -->
            <div>
                <InputLabel for="role" value="Kedudukan / Role" />
                <div
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 px-3 py-2 text-sm text-gray-500 shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400"
                >
                    {{ userRole }}
                </div>
            </div>

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
            <div>
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

                <div v-if="$page.props.mustVerifyEmail && user.email_verified_at === null">
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Alamat email Anda belum terverifikasi.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </Link>
                    </p>
                    <div
                        v-show="$page.props.status === 'verification-link-sent'"
                        class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                    >
                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">Tersimpan.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>

