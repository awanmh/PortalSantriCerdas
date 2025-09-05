import { ref, onMounted } from 'vue';
import axios from 'axios';

/**
 * Vue Composable untuk mengambil data dashboard dari API.
 * Mengelola state loading, error, dan data secara terpusat.
 */
export function useDashboard() {
    // State reaktif untuk data, status loading, dan pesan error
    const data = ref(null);
    const isLoading = ref(true);
    const error = ref(null);

    /**
     * Fungsi untuk mengambil data dari endpoint 'api.dashboard'.
     * Endpoint ini sama untuk semua peran; backend yang akan menentukan
     * data apa yang harus dikembalikan berdasarkan user yang login.
     */
    const fetchData = async () => {
        isLoading.value = true;
        error.value = null; // Reset error setiap kali fetch baru
        try {
            // Memanggil API menggunakan Axios. Asumsi Ziggy `route()` tersedia.
            const response = await axios.get(route('api.dashboard'));
            data.value = response.data;
        } catch (err) {
            console.error('Gagal mengambil data dashboard:', err);
            // Memberikan pesan error yang lebih deskriptif
            if (err.response?.status === 401) {
                error.value = 'Sesi Anda telah berakhir. Silakan muat ulang halaman atau login kembali.';
            } else {
                error.value = 'Tidak dapat memuat data. Periksa koneksi Anda dan coba lagi.';
            }
        } finally {
            isLoading.value = false;
        }
    };

    // Secara otomatis memanggil fetchData saat komponen yang menggunakan composable ini dimuat
    onMounted(fetchData);

    // Memberikan state dan fungsi ke komponen agar bisa digunakan
    return { data, isLoading, error, fetchData };
}

