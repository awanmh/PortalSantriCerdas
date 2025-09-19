<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";

// Ambil props dari Inertia
const { props } = usePage();
const users = props.users;

const deleteUser = (id) => {
    if (!confirm("Yakin ingin menghapus user ini?")) return;

    router.delete(route("users.destroy", id), {
        preserveScroll: true,
        onSuccess: () => {
            console.log("User berhasil dihapus");
        },
        onError: (err) => {
            alert(err.message || "Gagal menghapus user");
        },
    });
};
</script>

<template>
    <div class="p-6">
        <!-- Header + tombol tambah -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Manajemen User</h1>
            <Link
                :href="route('users.create')"
                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
            >
                + Tambah User
            </Link>
        </div>

        <!-- Table -->
        <table
            v-if="users.data.length > 0"
            class="min-w-full bg-white border rounded-lg shadow text-sm"
        >
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-3 border">ID</th>
                    <th class="p-3 border">Nama</th>
                    <th class="p-3 border">Email</th>
                    <th class="p-3 border">Role</th>
                    <th class="p-3 border text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="u in users.data"
                    :key="u.id"
                    class="hover:bg-gray-50 transition"
                >
                    <td class="p-3 border">{{ u.id }}</td>
                    <td class="p-3 border">{{ u.name }}</td>
                    <td class="p-3 border">{{ u.email }}</td>
                    <td class="p-3 border">{{ u.role }}</td>
                    <td class="p-3 border text-center space-x-2">
                        <Link
                            :href="route('users.edit', u.id)"
                            class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                        >
                            Edit
                        </Link>
                        <button
                            @click="deleteUser(u.id)"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                        >
                            Hapus
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Empty state -->
        <div v-else class="p-4 text-center text-gray-500">
            Tidak ada data user
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 space-x-2">
            <template v-for="link in users.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-1 border rounded"
                    :class="{
                        'bg-blue-500 text-white font-bold': link.active,
                        'hover:bg-gray-100': !link.active,
                    }"
                    v-html="link.label"
                />
                <span
                    v-else
                    class="px-3 py-1 border rounded text-gray-400"
                    v-html="link.label"
                />
            </template>
        </div>
    </div>
</template>
