<template>
    <header class="bg-green-900 text-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center space-x-4">
                    <router-link to="/" class="text-xl font-semibold">eksisozluk</router-link>
                    <input
                        type="text"
                        placeholder="başlık, #entry, @yazar"
                        class="bg-green-800 px-4 py-1 rounded text-sm w-64 focus:outline-none focus:ring-2 focus:ring-green-700"
                        v-model="searchQuery"
                        @keyup.enter="handleSearch"
                    />
                </div>
                <div class="flex items-center space-x-4 text-sm">
                    <button @click="toggleTheme" class="hover:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    </button>
                    <router-link to="#" class="hover:underline">#gündem</router-link>
                    <router-link to="#" class="hover:underline">debe</router-link>
                    <router-link to="#" class="hover:underline">sorunsallar</router-link>
                    <router-link to="#" class="hover:underline">takip</router-link>
                    <router-link to="/signup" class="hover:underline">kayıt ol</router-link>
                    <router-link to="/login" class="hover:underline">giriş</router-link>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref } from 'vue';
import { useTheme } from '../../composables/useTheme';

const { toggleTheme } = useTheme();
const searchQuery = ref('');

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        // Arama işlemi burada yapılacak
        console.log('Arama:', searchQuery.value);
    }
};
</script>

