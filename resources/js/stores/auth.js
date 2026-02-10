import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';
import router from '../router';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(JSON.parse(localStorage.getItem('user')) || null);
    const token = ref(localStorage.getItem('token') || null);

    if (token.value) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
    }

    async function login(email, password) {
        try {
            const response = await axios.post('/api/login', { email, password });
            token.value = response.data.token;
            user.value = response.data.user;

            localStorage.setItem('token', token.value);
            localStorage.setItem('user', JSON.stringify(user.value));
            axios.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
            
            return true;
        } catch (error) {
            console.error('Login failed', error);
            throw error;
        }
    }

    async function logout() {
        try {
            await axios.post('/api/logout');
        } catch (e) {
            // Ignore error
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            delete axios.defaults.headers.common['Authorization'];
            router.push('/admin/login');
        }
    }

    return { user, token, login, logout };
});