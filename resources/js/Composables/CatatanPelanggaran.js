import { ref } from 'vue';
import apiClient from '@/api'; // Gunakan instance apiClient kita

export function useCatatanPelanggaran() {
    const catatan = ref([]);
    const siswaList = ref([]);
    const isLoading = ref(false);
    const error = ref(null);

    /**
     * Mengambil daftar catatan pelanggaran dari API
     */
    const fetchCatatan = async () => {
        isLoading.value = true;
        error.value = null;
        try {
            const response = await apiClient.get('/api/catatan-pelanggaran');
            catatan.value = response.data.data; // Akses array dari properti 'data' paginasi
        } catch (err) {
            error.value = 'Gagal memuat data catatan pelanggaran.';
            console.error(err);
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Mengambil daftar siswa (untuk dropdown form)
     */
    const fetchSiswa = async () => {
        try {
            // Asumsi ada endpoint untuk mengambil semua user dengan role siswa
            const response = await apiClient.get('/api/users?role=siswa');
            siswaList.value = response.data;
        } catch (err) {
            console.error('Gagal memuat daftar siswa:', err);
        }
    };

    /**
     * Menambah catatan pelanggaran baru
     * @param {Object} formData Data dari form
     */
    const createCatatan = async (formData) => {
        isLoading.value = true;
        error.value = null;
        try {
            await apiClient.post('/api/catatan-pelanggaran', formData);
            await fetchCatatan(); // Muat ulang data setelah berhasil
            return true;
        } catch (err) {
            error.value = err.response?.data?.message || 'Gagal menambahkan catatan.';
            console.error(err);
            return false;
        } finally {
            isLoading.value = false;
        }
    };

    return {
        catatan,
        siswaList,
        isLoading,
        error,
        fetchCatatan,
        fetchSiswa,
        createCatatan,
    };
}
