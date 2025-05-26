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
        async handleForgotPass({ email }) {
            await this.getToken();
            await axios.post("/forgot-password", { email });
            this.router.push('/login');
        },
        async handleResetPassword({ password, password_confirmation, email, token}) {
            await this.getToken();
            await axios.post("/reset-password", { password, password_confirmation, email, token });
            this.router.push('/login');
        },
        async handleChangePassword({ current_password, new_password, new_password_confirmation}) {
            await this.getToken();
            await axios.post("/api/change-password", { current_password, new_password, new_password_confirmation });
            this.handleLogout();
        },
        async handleLogout() {
            await this.getToken();
            await axios.post('/logout');
            this.authUser = null;
            this.router.push('/');
        },
        async handleDeleteAccount() {
            await this.getToken();
            await axios.delete('/api/delete-account');
            this.authUser = null;
            this.router.push('/');
        },
    }
})