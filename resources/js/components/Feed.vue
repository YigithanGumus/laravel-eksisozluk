<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold">takip feed</h1>
                <button @click="fetchFeed" class="text-sm text-green-700 hover:underline">yenile</button>
            </div>
            <div v-if="!isAuthenticated" class="text-sm text-gray-600">
                Takip akışını görmek için giriş yapın.
            </div>
            <div v-else-if="loading" class="text-sm text-gray-500">yükleniyor...</div>
            <div v-else class="space-y-4">
                <div v-for="entry in entries" :key="entry.id" class="border-b pb-4">
                    <div class="text-sm text-gray-600 mb-2">
                        {{ entry.user?.username || entry.user?.name }} · {{ formatDate(entry.created_at) }}
                    </div>
                    <router-link :to="`/title/${entry.title?.slug}`" class="text-green-700 hover:underline block mb-1">
                        {{ entry.title?.title }}
                    </router-link>
                    <p class="text-gray-800 whitespace-pre-line">{{ entry.content }}</p>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import MainLayout from './layouts/MainLayout.vue';
import { useAuth } from '../composables/useAuth';

const entries = ref([]);
const loading = ref(false);
const { isAuthenticated } = useAuth();

const formatDate = (value) => value ? new Date(value).toLocaleString('tr-TR') : '';

const fetchFeed = async () => {
    if (!isAuthenticated.value) return;
    loading.value = true;
    try {
        const response = await axios.get('/api/feed/following');
        entries.value = response.data?.data?.data || [];
    } catch (error) {
        console.error('feed fetch error', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchFeed);
</script>
