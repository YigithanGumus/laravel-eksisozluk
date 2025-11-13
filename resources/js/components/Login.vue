<template>
    <div>
        <Header />
        <main class="container mx-auto px-4 py-12">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm p-6">
                <h1 class="text-2xl font-semibold mb-6">giriş</h1>

                <form @submit.prevent="handleLogin" class="space-y-4">
                    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ errorMessage }}
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">email</label>
                        <input
                            type="email"
                            id="email"
                            v-model="form.email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">şifre</label>
                        <input
                            type="password"
                            id="password"
                            v-model="form.password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            required
                        >
                    </div>

                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            id="remember"
                            v-model="form.remember"
                            class="h-4 w-4 text-green-600 rounded"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-600">beni hatırla</label>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-green-700 text-white py-2 px-4 rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading">Giriş yapılıyor...</span>
                        <span v-else>giriş yap</span>
                    </button>
                </form>

                <div class="mt-4 text-sm text-gray-600">
                    <a href="#" class="text-green-700 hover:underline">şifremi unuttum</a>
                </div>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Header from './partials/Header.vue';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const { login } = useAuth();
const loading = ref(false);
const errorMessage = ref('');

const form = reactive({
    email: '',
    password: '',
    remember: false
});

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        const response = await axios.post('/api/auth/login', {
            email: form.email,
            password: form.password
        });

        if (response.data.token) {
            // useAuth composable'ındaki login fonksiyonunu kullan
            login(response.data.token, response.data.user);

            // Başarılı giriş sonrası ana sayfaya yönlendir
            router.push('/');
        }
    } catch (error) {
        console.error('Login error:', error);

        if (error.response) {
            // Backend'den gelen hata mesajı
            const message = error.response.data?.message;
            if (Array.isArray(message)) {
                errorMessage.value = message[0];
            } else if (typeof message === 'string') {
                errorMessage.value = message;
            } else {
                errorMessage.value = error.response.data?.error || 'Giriş yapılırken bir hata oluştu.';
            }
        } else if (error.request) {
            // İstek gönderildi ama yanıt alınamadı
            errorMessage.value = 'Sunucuya bağlanılamadı. Lütfen internet bağlantınızı kontrol edin.';
        } else {
            // İstek hazırlanırken bir hata oluştu
            errorMessage.value = error.message || 'Bir hata oluştu. Lütfen tekrar deneyin.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>

</style>
