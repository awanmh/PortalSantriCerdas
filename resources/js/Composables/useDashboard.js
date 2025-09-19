import { ref, onMounted, onUnmounted } from "vue";
import axios from "axios";

export function useDashboard() {
    const data = ref(null);
    const isLoading = ref(true);
    const error = ref(null);

    let intervalId = null;

    const fetchData = async () => {
        isLoading.value = true;
        try {
            // ✅ cukup "/dashboard" karena baseURL sudah "/api"
            const response = await axios.get("/dashboard");
            data.value = response.data;
        } catch (err) {
            error.value = err.response?.data?.message || err.message;
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(() => {
        fetchData();
        // refresh otomatis tiap 5 menit
        intervalId = setInterval(fetchData, 300000);
    });

    onUnmounted(() => {
        // bersihkan interval biar ga ngebek
        if (intervalId) clearInterval(intervalId);
    });

    return { data, isLoading, error, fetchData };
}
