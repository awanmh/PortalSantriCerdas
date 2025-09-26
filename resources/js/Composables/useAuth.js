// resources/js/composables/useAuth.js
import { ref } from "vue";
import axios from "axios";

const user = ref(null);
const isLoading = ref(false);
const error = ref(null);

export function useAuth() {
    const getUser = async () => {
        isLoading.value = true;
        try {
            const res = await axios.get("/api/user", { withCredentials: true });
            user.value = res.data;
        } catch (err) {
            error.value = err.response?.data?.message || "Gagal ambil user";
        } finally {
            isLoading.value = false;
        }
    };

    const login = async (email, password, remember = false) => {
        error.value = null;
        try {
            // 1. Ambil CSRF cookie
            await axios.get("/sanctum/csrf-cookie", { withCredentials: true });

            // 2. Login
            await axios.post(
                "/api/login",
                { email, password, remember },
                { withCredentials: true }
            );

            // 3. Ambil data user setelah login
            await getUser();

            return true;
        } catch (err) {
            error.value =
                err.response?.data?.message ||
                "Login gagal, cek email/password";
            return false;
        }
    };

    const logout = async () => {
        try {
            await axios.post("/api/logout", {}, { withCredentials: true });
            user.value = null;
        } catch (err) {
            error.value = err.response?.data?.message || "Logout gagal";
        }
    };

    return {
        user,
        isLoading,
        error,
        getUser,
        login,
        logout,
    };
}
