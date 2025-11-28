<template>
    <div class="col-span-3">
        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">
                debe <span class="font-normal text-sm">(dünün en beğenilen entryleri)</span>
            </h3>
            <div v-if="loading" class="text-xs text-gray-500">yükleniyor...</div>
            <ul v-else class="space-y-2 text-sm">
                <li v-for="item in debeItems" :key="item.id">
                    <router-link :to="`/title/${item.title?.slug}`" class="hover:text-green-700">
                        {{ item.title?.title || 'başlık' }}
                        <span class="text-gray-400 text-xs">({{ item.favorites_count || 0 }})</span>
                    </router-link>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">popüler</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="item in popularItems" :key="item.slug">
                    <router-link :to="`/title/${item.slug}`" class="hover:text-green-700">{{ item.title }}</router-link>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded p-4 shadow-sm">
            <h3 class="font-semibold mb-3">başlık istatistikleri</h3>
            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">toplam entry</span>
                    <span class="font-medium">{{ stats.totalEntries }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">toplam yazar</span>
                    <span class="font-medium">{{ stats.totalAuthors }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">sayfadaki entry</span>
                    <span class="font-medium">{{ stats.currentPageEntries }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const debeItems = ref([]);
const popularItems = ref([]);
const stats = ref({
    totalEntries: 0,
    totalAuthors: 0,
    currentPageEntries: 0,
});
const loading = ref(false);

const fetchEntries = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/feed/top', { params: { range: 'yesterday' } });
        const entries = response.data?.data?.entries || [];
        const titles = response.data?.data?.titles || [];
        debeItems.value = entries.slice(0, 6);

        stats.value = {
            totalEntries: entries.length,
            totalAuthors: new Set(entries.map(e => e.user_id)).size,
            currentPageEntries: entries.length,
        };

        popularItems.value = titles.slice(0, 5);
    } catch (error) {
        console.error('right sidebar fetch error', error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchEntries);
</script>
