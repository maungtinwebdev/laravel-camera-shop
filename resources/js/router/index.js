import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import ProductDetail from '../views/ProductDetail.vue';
import Cart from '../views/Cart.vue';
import Checkout from '../views/Checkout.vue';
import AdminLayout from '../views/admin/AdminLayout.vue';
import AdminLogin from '../views/admin/Login.vue';
import AdminDashboard from '../views/admin/Dashboard.vue';
import AdminProducts from '../views/admin/Products.vue';
import AdminOrders from '../views/admin/Orders.vue';

const routes = [
    // Public Routes
    { path: '/', component: Home },
    { path: '/product/:slug', component: ProductDetail, props: true },
    { path: '/cart', component: Cart },
    { path: '/checkout', component: Checkout },

    // Admin Routes
    { 
        path: '/admin/login', 
        component: AdminLogin,
        meta: { guest: true }
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true, admin: true },
        children: [
            { path: '', redirect: '/admin/dashboard' },
            { path: 'dashboard', component: AdminDashboard },
            { path: 'products', component: AdminProducts },
            { path: 'orders', component: AdminOrders },
        ]
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');

    if (to.meta.requiresAuth) {
        if (!token) {
            return next('/admin/login');
        }
        
        if (to.meta.admin && !user.is_admin) {
            return next('/'); // Or 403 page
        }
    }

    if (to.meta.guest && token) {
        return next('/admin/dashboard');
    }

    next();
});

export default router;