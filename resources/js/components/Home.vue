<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">gündem</h1>
                    <p class="text-sm text-gray-500">son hareketlenen başlıklar ve en çok beğenilenler</p>
                </div>
                <button
                    v-if="isAuthenticated"
                    @click="showCreate = !showCreate"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm"
                >
                    yeni başlık aç
                </button>
            </div>

            <div v-if="showCreate" class="bg-gray-50 border border-gray-200 rounded p-4 mb-6">
                <h2 class="font-semibold mb-3 text-sm uppercase text-gray-600">başlık & ilk entry</h2>
                <div class="space-y-3">
                    <input
                        v-model="createForm.title"
                        type="text"
                        placeholder="başlık"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                    />
                    <textarea
                        v-model="createForm.content"
                        rows="4"
                        placeholder="ilk entry"
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                    ></textarea>
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <span v-if="createError" class="text-red-600">{{ createError }}</span>
                        </div>
                        <button
                            @click="submitCreate"
                            :disabled="creating"
                            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm disabled:opacity-60"
                        >
                            {{ creating ? 'kaydediliyor...' : 'oluştur' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <div class="space-x-2">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="setTab(tab.key)"
                        :class="[
                            'px-3 py-1 rounded text-sm',
                            activeTab === tab.key ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700'
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </div>
                <div class="flex items-center">
                    <input
                        v-model="search"
                        @keyup.enter="fetchData"
                        type="text"
                        placeholder="başlık ara..."
                        class="w-48 md:w-64 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                    />
                    <button
                        @click="fetchData"
                        class="ml-3 bg-gray-100 text-gray-700 px-3 py-2 rounded border hover:bg-gray-200 text-sm"
                    >
                        yenile
                    </button>
                </div>
            </div>

            <div v-if="loading" class="text-gray-500 text-sm">yükleniyor...</div>

            <div v-else>
                <template v-if="activeTab === 'gundem'">
                    <div class="divide-y">
                        <div
                            v-for="title in titles"
                            :key="title.uuid || title.id"
                            class="py-3 flex items-start justify-between"
                        >
                            <div>
                                <router-link
                                    :to="`/title/${title.slug}`"
                                    class="text-lg font-semibold hover:text-green-700"
                                >
                                    {{ title.title }}
                                </router-link>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ title.entry_count }} entry · son {{ formatDate(title.last_activity_at) }}
                                </div>
                                <div class="text-xs text-gray-500" v-if="title.is_pinned">pinned</div>
                            </div>
                            <div class="text-xs text-gray-500 text-right">
                                <div class="font-medium">{{ title.user?.username || title.user?.name }}</div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <div class="divide-y">
                        <div
                            v-for="entry in entries"
                            :key="entry.id"
                            class="py-3"
                        >
                            <div class="text-sm text-gray-500 mb-1">
                                {{ entry.user?.username || entry.user?.name }} · {{ formatDate(entry.created_at) }}
                            </div>
                            <router-link
                                :to="`/title/${entry.title?.slug}`"
                                class="text-green-700 font-semibold hover:underline"
                            >
                                {{ entry.title?.title }}
                            </router-link>
                            <p class="text-gray-800 whitespace-pre-line mt-1">{{ entry.content }}</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import MainLayout from './layouts/MainLayout.vue';
import { useAuth } from '../composables/useAuth';

const titles = ref([]);
const entries = ref([]);
const loading = ref(false);
const search = ref('');
const showCreate = ref(false);
const creating = ref(false);
const createError = ref('');
const activeTab = ref('gundem');
const tabs = [
    { key: 'gundem', label: 'gündem' },
    { key: 'bugun', label: 'bugün top' },
    { key: 'dun', label: 'dün / debe' },
    { key: 'hafta', label: 'hafta' },
];
const createForm = ref({
    title: '',
    content: '',
});

const { isAuthenticated } = useAuth();
const route = useRoute();

const formatDate = (value) => {
    if (!value) return '';
    return new Date(value).toLocaleString('tr-TR');
};

const setTab = (tab) => {
    activeTab.value = tab;
    fetchData();
};

const fetchData = async () => {
    loading.value = true;
    try {
        if (activeTab.value === 'gundem') {
            const response = await axios.get('/api/titles', {
                params: search.value ? { q: search.value } : {},
            });
            titles.value = response.data?.data?.data || response.data?.data || [];
            entries.value = [];
        } else {
            const rangeMap = {
                bugun: 'today',
                dun: 'yesterday',
                hafta: 'week',
            };
            const response = await axios.get('/api/feed/top', {
                params: { range: rangeMap[activeTab.value] || 'week' },
            });
            entries.value = response.data?.data?.entries || [];
            titles.value = [];
        }
    } catch (error) {
        console.error('home fetch error', error);
    } finally {
        loading.value = false;
    }
};

const submitCreate = async () => {
    createError.value = '';
    creating.value = true;
    try {
        const response = await axios.post('/api/titles', {
            title: createForm.value.title,
            content: createForm.value.content,
        });
        const newTitle = response.data?.data?.title;
        if (newTitle && activeTab.value === 'gundem') {
            titles.value.unshift(newTitle);
        }
        createForm.value.title = '';
        createForm.value.content = '';
        showCreate.value = false;
    } catch (error) {
        createError.value = error.response?.data?.message || 'kaydedilemedi';
    } finally {
        creating.value = false;
    }
};

watch(() => route.query.q, (val) => {
    search.value = val || '';
    fetchData();
}, { immediate: true });

onMounted(fetchData);
</script>
