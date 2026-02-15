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
            setToken(response.data.token);
            user.value = response.data.user;
            localStorage.setItem('user', JSON.stringify(user.value));
            return true;
        } catch (error) {
            console.error('Login failed', error);
            throw error;
        }
    }

    function setToken(newToken) {
        token.value = newToken;
        localStorage.setItem('token', newToken);
        axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;
    }

    function clearAuth() {
        token.value = null;
        user.value = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
    }

    function logout() {
        const tokenValue = token.value;
        clearAuth();
        
        // Use a detached request to avoid waiting
        if (tokenValue) {
            axios.post('/api/logout', {}, {
                headers: { Authorization: `Bearer ${tokenValue}` }
            }).catch(() => {});
        }
        
        router.push('/login');
    }

    async function fetchUser() {
        if (!token.value) return;
        
        try {
            const response = await axios.get('/api/user');
            user.value = response.data;
            localStorage.setItem('user', JSON.stringify(user.value));
        } catch (error) {
            console.error('Fetch user failed', error);
            if (error.response?.status === 401) {
                clearAuth();
            }
            throw error;
        }
    }

    return { user, token, login, logout, fetchUser, clearAuth, setToken };
});