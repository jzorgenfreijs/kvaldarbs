import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore("auth", {
    state: () => ({
        authUser: null
    }),
    getters: {
        user: (state) => state.authUser
    },
    actions: {
        async getToken() {
            await axios.get('/sanctum/csrf-cookie');
        },
        async getUser() {
            this.getToken();
            const data = await axios.get('/api/user');
            this.authUser = data.data;
        },
        async handleLogin({ email, password }) {
            await this.getToken();
            await axios.post("/login", { email, password });
            this.router.push('/');
        },
        async handleRegister({ name, email, password, role, password_confirmation }) {
            await this.getToken();
            await axios.post("/register", { name, email, password, role, password_confirmation });
            this.router.push('/');
        },
        async handleLogout() {
            await this.getToken();
            await axios.post('/logout');
            this.authUser = null;
            this.router.push('/');
        },
    }
})