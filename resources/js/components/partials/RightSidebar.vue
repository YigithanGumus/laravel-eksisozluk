<template>
    <div class="col-span-3">
        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">
                debe <span class="font-normal text-sm">(dünün en beğenilen entryleri)</span>
            </h3>
            <ul class="space-y-2 text-sm">
                <li v-for="item in debeItems" :key="item.id">
                    <router-link :to="`/topic/${item.slug}`" class="hover:text-green-700">
                        {{ item.title }}
                        <span class="text-gray-400 text-xs">({{ item.count }})</span>
                    </router-link>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded p-4 shadow-sm mb-4">
            <h3 class="font-semibold mb-3">popüler</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="item in popularItems" :key="item">
                    <router-link to="#" class="hover:text-green-700">{{ item }}</router-link>
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
                    <span class="text-gray-600">son 24 saat</span>
                    <span class="font-medium">{{ stats.last24Hours }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const debeItems = ref([
    { id: 1, title: 'en iyi yazılım dili tartışması', slug: 'en-iyi-yazilim-dili-tartismasi', count: 345 },
    { id: 2, title: 'yapay zekanın geleceği', slug: 'yapay-zekanin-gelecegi', count: 289 },
    { id: 3, title: '2025 nobel ödülleri', slug: '2025-nobel-odulleri', count: 234 },
    { id: 4, title: "mars'ta yaşam izleri", slug: 'marsta-yasam-izleri', count: 198 },
    { id: 5, title: 'gelecek nesil telefonlar', slug: 'gelecek-nesil-telefonlar', count: 156 },
]);

const popularItems = ref([
    'günün videosu',
    'en çok favorilenen entryler',
    'bugün en çok paylaşılanlar'
]);

const stats = ref({
    totalEntries: 243,
    totalAuthors: 156,
    last24Hours: 87
});
</script>

