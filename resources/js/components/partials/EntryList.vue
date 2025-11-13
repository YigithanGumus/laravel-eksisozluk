<template>
    <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold">{{ topicTitle }}</h1>
                <div class="flex space-x-2">
                    <button @click="sortEntries('asc')" class="text-sm text-gray-500 hover:text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button @click="sortEntries('desc')" class="text-sm text-gray-500 hover:text-green-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <EntryItem
                    v-for="entry in entries"
                    :key="entry.id"
                    :entry="entry"
                    @like="handleLike"
                    @share="handleShare"
                    @report="handleReport"
                />
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-6 text-sm">
                <button
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="text-gray-500 hover:text-green-700 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    önceki
                </button>
                <span class="text-gray-500">sayfa {{ currentPage }}/{{ totalPages }}</span>
                <button
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="text-gray-500 hover:text-green-700 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    sonraki
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import EntryItem from '../partials/EntryItem.vue';

const props = defineProps({
    topicTitle: {
        type: String,
        default: '6 kasım 2025 dolar kuru'
    }
});

const currentPage = ref(1);
const totalPages = ref(24);

const entries = ref([
    {
        id: 1,
        author: 'yazaradi',
        date: '06.11.2025 09:15',
        content: 'dolar kuru hakkında örnek entry içeriği. bu içerik örnek amaçlı oluşturulmuştur. (bkz: ekonomi) yazar burada dolar kurunun son durumu hakkında detaylı bir analiz yapmış.',
        tags: ['ekonomi', 'dolar'],
        likes: 0
    },
    {
        id: 2,
        author: 'başkayazar',
        date: '06.11.2025 10:30',
        content: 'ikinci örnek entry içeriği. bu içerik de örnek amaçlı oluşturulmuştur. (bkz: merkez bankası) yazar finansal piyasalar hakkında görüşlerini paylaşmış.',
        tags: ['finans', 'merkez bankası'],
        likes: 0
    }
]);

const sortEntries = (order) => {
    console.log('Sıralama:', order);
    // Sıralama mantığı burada olacak
};

const handleLike = (entryId) => {
    const entry = entries.value.find(e => e.id === entryId);
    if (entry) {
        entry.likes++;
    }
};

const handleShare = (entryId) => {
    console.log('Paylaş:', entryId);
};

const handleReport = (entryId) => {
    console.log('Şikayet:', entryId);
};

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};
</script>

