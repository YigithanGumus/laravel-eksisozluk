import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import Login from '../components/Login.vue';
import Signup from '../components/Signup.vue';
import { useAuth } from '../composables/useAuth';

const routes = [
    { path: '/', component: Home },
    { path: '/home', component: Home },
    { path: '/login', component: Login, meta: { requiresGuest: true } },
    { path: '/signup', component: Signup, meta: { requiresGuest: true } }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Router guard - Giriş yapmış kullanıcılar login/register sayfalarına erişemez
router.beforeEach((to, from, next) => {
    const { isAuthenticated } = useAuth();
    
    // Token kontrolü (initAuth henüz çağrılmamış olabilir)
    const hasToken = localStorage.getItem('auth_token');
    const userIsAuthenticated = isAuthenticated.value || hasToken;
    
    // Eğer sayfa guest (misafir) gerektiriyorsa ve kullanıcı giriş yapmışsa
    if (to.meta.requiresGuest && userIsAuthenticated) {
        // Ana sayfaya yönlendir
        next('/');
    } else {
        next();
    }
});

export default router;
