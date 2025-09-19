<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
  user: Object,
});

const form = ref({
  name: props.user.name,
  email: props.user.email,
  role_keyword: props.user.role_keyword,
  password: "",
});

const save = async () => {
  try {
    await axios.put(`/api/users/${props.user.id}`, form.value);
    alert("User berhasil diperbarui");
    router.visit(route("users.index"));
  } catch (err) {
    alert(err.response?.data?.message || "Gagal update user");
  }
};
</script>

<template>
  <div class="p-6 max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

    <form @submit.prevent="save" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Nama</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border p-2 rounded"
        />
      </div>

      <div>
        <label class="block mb-1 font-medium">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border p-2 rounded"
        />
      </div>

      <div>
        <label class="block mb-1 font-medium">Role</label>
        <input
          v-model="form.role_keyword"
          type="text"
          class="w-full border p-2 rounded"
        />
      </div>

      <div>
        <label class="block mb-1 font-medium">Password (opsional)</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border p-2 rounded"
        />
      </div>

      <button
        type="submit"
        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
      >
        Simpan
      </button>
    </form>
  </div>
</template>
