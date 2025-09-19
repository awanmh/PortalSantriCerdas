<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { InformationCircleIcon } from '@heroicons/vue/24/outline';

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

const user = usePage().props.auth.user;
const flash = usePage().props.flash;

const form = useForm({
    name: user.name,
    email: user.email,
    photo: null,
});

const photoPreview = ref(null);
const photoInput = ref(null);

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];
    if (!photo) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
    form.photo = photo;
};

const submit = () => {
    console.log("[DEBUG] 5. Tombol 'Simpan' diklik, fungsi submit dijalankan.");
    console.log("[DEBUG] 6. Data yang akan dikirim:", form.data());

    if (form.processing) {
        console.warn("[DEBUG] Submit dicegah karena form sedang diproses.");
        return;
    }

    // --- PERBAIKAN UTAMA UNTUK TES ---
    // Menggunakan URL hardcoded untuk melewati Ziggy.
    const submitUrl = '/profile';
    console.log(`[DEBUG] 7. Mengirim data ke URL statis: ${submitUrl}`);

    form.post(submitUrl, {
        preserveScroll: true,
        onStart: () => console.log("🚀 [INERTIA START] Memulai pengiriman..."),
        onSuccess: () => console.log("✅ [INERTIA SUCCESS] Server merespons dengan sukses!"),
        onError: (errors) => console.error("❌ [INERTIA ERROR] Server mengembalikan error:", errors),
        onFinish: () => console.log("🏁 [INERTIA FINISH] Proses permintaan selesai."),
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Informasi Profil
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Perbarui informasi profil dan alamat email akun Anda.
            </p>
        </header>

        <div v-if="flash.info" class="mt-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-800 rounded-lg flex items-center shadow">
             <InformationCircleIcon class="h-6 w-6 mr-3"/>
             <span class="font-medium">{{ flash.info }}</span>
        </div>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
            <!-- Bagian Foto Profil -->
            <div>
                <InputLabel for="photo" value="Foto Profil (Wajib untuk Siswa)" />
                <div class="mt-2 flex items-center gap-4">
                    <img v-if="photoPreview" :src="photoPreview" class="rounded-full h-20 w-20 object-cover">
                    <img v-else :src="user.profile_photo_url" :alt="user.name" class="rounded-full h-20 w-20 object-cover">
                    <PrimaryButton type="button" @click="selectNewPhoto">
                        Pilih Foto Baru
                    </PrimaryButton>
                </div>
                <input ref="photoInput"
                       type="file"
                       class="hidden"
                       @change="updatePhotoPreview"
                       accept="image/*">
                <InputError class="mt-2" :message="form.errors.photo" />
            </div>

            <!-- Nama & Email -->
            <div>
                <InputLabel for="name" value="Nama" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                        Tersimpan.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

