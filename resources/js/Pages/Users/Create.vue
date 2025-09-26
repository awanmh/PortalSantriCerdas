<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const form = ref({
  name: "",
  email: "",
  role_keyword: "",
  password: "",
});

const save = async () => {
  try {
    await axios.post("/api/users", form.value);
    alert("User berhasil ditambahkan");
    router.visit(route("users.index"));
  } catch (err) {
    alert(err.response?.data?.message || "Gagal menambahkan user");
  }
};
</script>

<template>
  <div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Tambah User</h1>

    <form @submit.prevent="save" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Nama</label>
        <input v-model="form.name" type="text" class="w-full border p-2 rounded" required />
      </div>

      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input v-model="form.email" type="email" class="w-full border p-2 rounded" required />
      </div>

      <div>
        <label class="block mb-1 font-medium">Role</label>
        <input v-model="form.role_keyword" type="text" class="w-full border p-2 rounded" />
      </div>

      <div>
        <label class="block mb-1 font-medium">Password</label>
        <input v-model="form.password" type="password" class="w-full border p-2 rounded" required />
      </div>

      <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        Simpan
      </button>
    </form>
  </div>
</template>
