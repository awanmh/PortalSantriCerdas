<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { UserPlusIcon, PencilSquareIcon, TrashIcon, CheckCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

/**
 * Mendefinisikan props yang diterima dari UserController.
 */
const props = defineProps({
    /**
     * Objek paginasi yang berisi data pengguna.
     */
    users: {
        type: Object,
        required: true,
    },
    /**
     * Flash messages dari session Laravel.
     */
    flash: {
        type: Object,
        default: () => ({}),
    },
    // PROPS BARU UNTUK FILTER
    roles: { // Daftar semua peran dari backend
        type: Array,
        required: true,
    },
    currentRole: { // Peran yang sedang aktif difilter
        type: String,
        default: 'all', // Default 'all' jika tidak ada filter
    },
});

// State lokal untuk filter peran yang dipilih
const selectedRole = ref(props.currentRole);

/**
 * Mengamati perubahan pada `selectedRole` dan memicu filter.
 */
watch(selectedRole, (newRole) => {
    router.get(route('admin.users.index', { role: newRole }), {}, { preserveState: true, replace: true });
});

/**
 * Menghapus pengguna dengan dialog konfirmasi.
 * @param {object} user - Objek pengguna yang akan dihapus.
 */
const deleteUser = (user) => {
    // Peringatan: confirm() adalah cara yang simpel tapi bisa diblokir browser.
    // Untuk produksi, pertimbangkan menggunakan komponen modal custom.
    if (confirm(`Apakah Anda yakin ingin menghapus pengguna "${user.name}"? Aksi ini tidak dapat dibatalkan.`)) {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true, // Agar tidak scroll ke atas setelah aksi
        });
    }
};

/**
 * Memberikan warna badge yang berbeda berdasarkan peran pengguna.
 * @param {string} role - Nama peran.
 * @returns {string} - Kelas CSS Tailwind untuk warna badge.
 */
const roleColor = computed(() => {
    return (role) => {
        if (!role) return 'bg-gray-100 text-gray-800';
        switch (role.toLowerCase()) {
            case 'it':
                return 'bg-red-100 text-red-800';
            case 'guru':
                return 'bg-blue-100 text-blue-800';
            case 'bk':
                return 'bg-yellow-100 text-yellow-800';
            case 'siswa':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };
});
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengguna</h2>
                    <p class="text-sm text-gray-500 mt-1">Kelola semua akun yang terdaftar di dalam sistem.</p>
                </div>
                <Link :href="route('admin.users.create')" class="mt-4 sm:mt-0">
                    <PrimaryButton>
                        <UserPlusIcon class="h-5 w-5 mr-2" />
                        Tambah Pengguna
                    </PrimaryButton>
                </Link>
            </div>

            <div class="flex justify-end mt-4">
                <select v-model="selectedRole" class="block w-full sm:w-auto rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="all">Semua Peran</option>
                    <option v-for="role in roles" :key="role.id" :value="role.name">
                        {{ role.name }}
                    </option>
                </select>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <Transition enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95" class="transition-all duration-300 ease-out">
                    <div v-if="flash?.success" class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg relative flex items-center shadow-md" role="alert">
                        <CheckCircleIcon class="h-6 w-6 mr-3"/>
                        <span class="block sm:inline font-medium">{{ flash.success }}</span>
                    </div>
                </Transition>
                <Transition enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95" class="transition-all duration-300 ease-out">
                    <div v-if="flash?.error" class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg relative flex items-center shadow-md" role="alert">
                        <ExclamationTriangleIcon class="h-6 w-6 mr-3"/>
                        <span class="block sm:inline font-medium">{{ flash.error }}</span>
                    </div>
                </Transition>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                                    <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">{{ user.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="user.roles && user.roles.length > 0" :class="['px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full capitalize', roleColor(user.roles[0].name)]">
                                            {{ user.roles[0].name }}
                                        </span>
                                        <span v-else class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            -
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ new Date(user.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <Link :href="route('admin.users.edit', user.id)" class="text-indigo-600 hover:text-indigo-900 inline-block p-1 rounded-full hover:bg-indigo-100 transition-colors" title="Edit">
                                            <PencilSquareIcon class="h-5 w-5"/>
                                        </Link>
                                        <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900 inline-block p-1 rounded-full hover:bg-red-100 transition-colors" title="Hapus">
                                            <TrashIcon class="h-5 w-5"/>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                        Tidak ada data pengguna yang ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="users.links.length > 3" class="p-4 border-t border-gray-200 bg-gray-50">
                        <Pagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>