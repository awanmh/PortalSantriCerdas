<script setup>
/**
 * Komponen StatCard.vue
 *
 * Komponen UI yang dapat digunakan kembali untuk menampilkan sebuah data statistik
 * tunggal, seperti jumlah pengguna atau total siswa, dalam format kartu.
 */

/**
 * Mendefinisikan properti (props) yang diterima oleh komponen ini.
 */
defineProps({
    /**
     * Teks warna untuk nilai statistik (angka).
     * Gunakan kelas warna dari Tailwind CSS.
     * @type {String}
     */
    color: {
        type: String,
        default: 'text-gray-900',
    },

    /**
     * Teks deskripsi untuk nilai statistik.
     * Contoh: "Total Pengguna"
     * @type {String}
     */
    label: {
        type: String,
        required: true,
    },

    /**
     * Nilai statistik yang akan ditampilkan.
     * Bisa berupa angka atau string.
     * @type {String|Number}
     */
    value: {
        type: [String, Number],
        required: true,
    },
});
</script>

<template>
    <div class="p-4 bg-white rounded-lg shadow-sm text-center border border-gray-200">
        <div class="text-3xl font-bold" :class="color">
            {{ value }}
        </div>
        <div class="text-sm font-medium text-gray-500 mt-1">
            {{ label }}
        </div>
    </div>
</template>

