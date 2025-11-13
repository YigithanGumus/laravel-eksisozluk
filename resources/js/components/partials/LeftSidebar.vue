<template>
    <div class="col-span-2">
        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">gündem</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="item in trendingTopics" :key="item.id">
                    <router-link :to="`/topic/${item.slug}`" class="hover:text-green-700">
                        {{ item.title }}
                        <span class="text-gray-400 text-xs">({{ item.count }})</span>
                    </router-link>
                </li>
            </ul>
            <router-link to="#" class="text-sm text-green-700 hover:underline block mt-4">
                daha fazla göster
            </router-link>
        </div>

        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">kanallar</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="channel in channels" :key="channel">
                    <router-link :to="`/channel/${channel}`" class="hover:text-green-700">
                        #{{ channel }}
                    </router-link>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded p-4 shadow-sm">
            <h3 class="font-semibold mb-3">son</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="topic in recentTopics" :key="topic">
                    <router-link to="#" class="hover:text-green-700">{{ topic }}</router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const trendingTopics = ref([
    { id: 1, title: '6 kasım 2025 dolar kuru', slug: '6-kasim-2025-dolar-kuru', count: 243 },
    { id: 2, title: 'windows 12', slug: 'windows-12', count: 156 },
    { id: 3, title: 'yapay zeka ile sohbet', slug: 'yapay-zeka-ile-sohbet', count: 134 },
    { id: 4, title: '2025 ekonomik krizi', slug: '2025-ekonomik-krizi', count: 98 },
    { id: 5, title: "türkiye'de yazılımcı maaşları", slug: 'turkiyede-yazilimci-maaslari', count: 87 },
]);

const channels = ref(['spor', 'siyaset', 'teknoloji', 'edebiyat', 'müzik']);

const recentTopics = ref([
    'bilgisayar oyunları',
    'sinema',
    'televizyon',
    'havadan sudan',
    'ilişkiler'
]);
</script>

