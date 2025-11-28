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
                    <router-link to="/" class="hover:underline">gündem</router-link>
                    <router-link to="/feed" class="hover:underline">takip</router-link>
                    <router-link to="/messages" class="hover:underline">mesaj</router-link>
                    <router-link to="/notifications" class="hover:underline relative">
                        bildirim
                        <span v-if="unreadCount > 0" class="ml-1 bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ unreadCount }}</span>
                    </router-link>
                    <router-link v-if="isModerator" to="/reports" class="hover:underline">raporlar</router-link>
                    <template v-if="isAuthenticated">
                        <router-link :to="`/user/${user?.username}`" class="text-green-200 hover:underline">
                            {{ user?.name || user?.username }}
                        </router-link>
                        <button @click="logout" class="hover:underline">çıkış</button>
                    </template>
                    <template v-else>
                        <router-link to="/signup" class="hover:underline">kayıt ol</router-link>
                        <router-link to="/login" class="hover:underline">giriş</router-link>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useTheme } from '../../composables/useTheme';
import { useAuth } from '../../composables/useAuth';
import { useNotifications } from '../../composables/useNotifications';

const { toggleTheme } = useTheme();
const { user, isAuthenticated, logout } = useAuth();
const { unreadCount, startPolling, fetchUnreadCount, stopPolling } = useNotifications();

const searchQuery = ref('');
const router = useRouter();
const isModerator = computed(() => !!user.value?.is_moderator);

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/', query: { q: searchQuery.value.trim() } });
    }
};

onMounted(() => {
    if (isAuthenticated.value) {
        fetchUnreadCount();
        startPolling();
    }
});

watch(isAuthenticated, (val) => {
    if (val) {
        fetchUnreadCount();
        startPolling();
    } else {
        stopPolling();
    }
});
</script>
