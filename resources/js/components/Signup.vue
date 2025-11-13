<template>
    <div>
        <Header />
        <main class="container mx-auto px-4 py-12">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-sm p-6">
                <h1 class="text-2xl font-semibold mb-6">kayıt formu</h1>

                <form @submit.prevent="handleRegister" class="space-y-4">
                    <div v-if="errorMessage" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                        {{ errorMessage }}
                    </div>

                    <div v-if="successMessage" class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                        {{ successMessage }}
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">e-posta</label>
                        <input
                            type="email"
                            id="email"
                            v-model="form.email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            required
                        >
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">nick</label>
                        <input
                            type="text"
                            id="name"
                            v-model="form.name"
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

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">şifre tekrar</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            required
                        >
                    </div>

                    <div class="flex items-start">
                        <input
                            type="checkbox"
                            id="terms"
                            v-model="form.terms"
                            class="h-4 w-4 mt-1 text-green-600 rounded"
                            required
                        >
                        <label for="terms" class="ml-2 text-sm text-gray-600">
                            kullanıcı sözleşmesini ve gizlilik politikasını okudum ve kabul ediyorum
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full bg-green-700 text-white py-2 px-4 rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="loading">Kayıt yapılıyor...</span>
                        <span v-else>kayıt ol</span>
                    </button>
                </form>
            </div>
        </main>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Header from './partials/Header.vue';

const router = useRouter();
const loading = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false
});

const handleRegister = async () => {
    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    // Şifre eşleşme kontrolü
    if (form.password !== form.password_confirmation) {
        errorMessage.value = 'Şifreler eşleşmiyor.';
        loading.value = false;
        return;
    }

    // Kullanıcı sözleşmesi kontrolü
    if (!form.terms) {
        errorMessage.value = 'Kullanıcı sözleşmesini kabul etmelisiniz.';
        loading.value = false;
        return;
    }

    try {
        const response = await axios.post('/api/auth/register', {
            name: form.name,
            email: form.email,
            password: form.password,
            password_confirmation: form.password_confirmation
        });

        if (response.data.message) {
            successMessage.value = response.data.message;

            // Başarılı kayıt sonrası 2 saniye bekleyip login sayfasına yönlendir
            setTimeout(() => {
                router.push('/login');
            }, 2000);
        }
    } catch (error) {
        console.error('Register error:', error);

        if (error.response) {
            // Backend'den gelen hata mesajı
            const errors = error.response.data?.errors;
            const message = error.response.data?.message;

            if (errors) {
                // Validation hataları
                const firstError = Object.values(errors)[0];
                if (Array.isArray(firstError)) {
                    errorMessage.value = firstError[0];
                } else {
                    errorMessage.value = firstError;
                }
            } else if (Array.isArray(message)) {
                errorMessage.value = message[0];
            } else if (typeof message === 'string') {
                errorMessage.value = message;
            } else {
                errorMessage.value = error.response.data?.error || 'Kayıt yapılırken bir hata oluştu.';
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
