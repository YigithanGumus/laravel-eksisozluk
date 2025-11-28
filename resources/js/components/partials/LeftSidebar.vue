<template>
    <div class="col-span-2">
        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">gündem</h3>
            <div v-if="loading" class="text-xs text-gray-500">yükleniyor...</div>
            <ul v-else class="space-y-2 text-sm">
                <li v-for="item in trendingTopics" :key="item.slug">
                    <router-link :to="`/title/${item.slug}`" class="hover:text-green-700">
                        {{ item.title }}
                        <span class="text-gray-400 text-xs">({{ item.entry_count }})</span>
                    </router-link>
                </li>
            </ul>
            <router-link to="/" class="text-sm text-green-700 hover:underline block mt-4">
                daha fazla göster
            </router-link>
        </div>

        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">kanallar</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="channel in channels" :key="channel">
                    <span class="text-gray-600">#{{ channel }}</span>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded p-4 shadow-sm">
            <h3 class="font-semibold mb-3">son</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="topic in trendingTopics.slice(0,5)" :key="topic.slug">
                    <router-link :to="`/title/${topic.slug}`" class="hover:text-green-700">{{ topic.title }}</router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const trendingTopics = ref([]);
const channels = ref(['spor', 'siyaset', 'teknoloji', 'edebiyat', 'müzik']);
const loading = ref(false);

const fetchTrending = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/titles');
        trendingTopics.value = response.data?.data?.data?.slice(0, 12) || [];
    } catch (error) {
        console.error('sidebar fetch error', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchTrending);
</script>
