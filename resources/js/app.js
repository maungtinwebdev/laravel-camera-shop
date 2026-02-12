import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';
import router from './router';
import App from './App.vue';
import { useAuthStore } from './stores/auth';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Initialize auth store and handle 401s
const authStore = useAuthStore();

axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            authStore.clearAuth();
            if (!router.currentRoute.value.meta.guest) {
                router.push('/login');
            }
        }
        return Promise.reject(error);
    }
);

// Verify user if token exists
if (authStore.token) {
    authStore.fetchUser().catch(() => {
        // fetchUser already handles 401 by clearing auth
    });
}

app.mount('#app');
