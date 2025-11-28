import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import Login from '../components/Login.vue';
import Signup from '../components/Signup.vue';
import TitleDetail from '../components/TitleDetail.vue';
import Profile from '../components/Profile.vue';
import Feed from '../components/Feed.vue';
import Messages from '../components/Messages.vue';
import Notifications from '../components/Notifications.vue';
import Reports from '../components/Reports.vue';
import { useAuth } from '../composables/useAuth';

const routes = [
    { path: '/', component: Home },
    { path: '/home', component: Home },
    { path: '/title/:slug', component: TitleDetail, props: true },
    { path: '/user/:username', component: Profile, props: true },
    { path: '/feed', component: Feed, meta: { requiresAuth: true } },
    { path: '/messages', component: Messages, meta: { requiresAuth: true } },
    { path: '/notifications', component: Notifications, meta: { requiresAuth: true } },
    { path: '/reports', component: Reports, meta: { requiresAuth: true } },
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
    const hasToken = localStorage.getItem('auth_token');
    const userIsAuthenticated = isAuthenticated.value || hasToken;

    if (to.meta.requiresGuest && userIsAuthenticated) {
        next('/');
    } else if (to.meta.requiresAuth && !userIsAuthenticated) {
        next('/login');
    } else {
        next();
    }
});

export default router;
