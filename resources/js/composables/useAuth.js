import { ref, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

// Singleton pattern - tüm component'ler aynı user state'ini paylaşır
const user = ref(null);
const isAuthenticated = computed(() => !!user.value);

export function useAuth() {
    const router = useRouter();

    // Token'ı localStorage'dan al ve axios header'ına ekle
    const initAuth = () => {
        const token = localStorage.getItem('auth_token');
        const userData = localStorage.getItem('user');

        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            if (userData) {
                try {
                    user.value = JSON.parse(userData);
                } catch (e) {
                    console.error('User data parse error:', e);
                }
            }
        }
    };

    // Kullanıcı bilgilerini backend'den al
    const fetchUser = async () => {
        try {
            const response = await axios.get('/api/user');
            if (response.data.user) {
                user.value = response.data.user;
                localStorage.setItem('user', JSON.stringify(response.data.user));
            }
        } catch (error) {
            if (error.response?.status === 401) {
                localStorage.removeItem('auth_token');
                localStorage.removeItem('user');
                delete axios.defaults.headers.common['Authorization'];
                user.value = null;
            }
        }
    };

    // Giriş yap
    const login = (token, userData) => {
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user', JSON.stringify(userData));
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        user.value = userData;
    };

    // Çıkış yap
    const logout = async () => {
        const token = localStorage.getItem('auth_token');
        
        if (token) {
            try {
                await axios.post('/api/auth/logout');
            } catch (error) {
                // sessizce geç
            }
        }
        
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
        user.value = null;
        router.push('/login');
    };

    return {
        user,
        isAuthenticated,
        initAuth,
        fetchUser,
        login,
        logout
    };
}
