<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">{{ profile?.user?.username }}</h1>
                    <p class="text-gray-600">{{ profile?.user?.bio }}</p>
                </div>
                <div v-if="isAuthenticated && profile?.user?.id !== user?.id">
                    <button
                        @click="toggleFollow"
                        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm"
                    >
                        {{ following ? 'takipten çık' : 'takip et' }}
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-center">
                <div class="bg-gray-50 rounded p-3">
                    <div class="text-xl font-semibold">{{ profile?.stats?.entries_count || 0 }}</div>
                    <div class="text-xs text-gray-500">entry</div>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <div class="text-xl font-semibold">{{ profile?.stats?.titles_count || 0 }}</div>
                    <div class="text-xs text-gray-500">başlık</div>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <div class="text-xl font-semibold">{{ profile?.stats?.followers_count || 0 }}</div>
                    <div class="text-xs text-gray-500">takipçi</div>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <div class="text-xl font-semibold">{{ profile?.stats?.favorites_count || 0 }}</div>
                    <div class="text-xs text-gray-500">favori</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="flex space-x-2 mb-4">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="['px-3 py-1 rounded text-sm', activeTab === tab.key ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-700']"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <div v-if="activeTab === 'titles'">
                    <h2 class="font-semibold mb-3">başlıklar</h2>
                    <ul class="space-y-2">
                        <li v-for="title in profile?.recent_titles || []" :key="title.id">
                            <router-link :to="`/title/${title.slug}`" class="text-green-700 hover:underline">
                                {{ title.title }}
                            </router-link>
                        </li>
                    </ul>
                </div>

                <div v-else-if="activeTab === 'entries'">
                    <h2 class="font-semibold mb-3">son entry'ler</h2>
                    <div class="space-y-3">
                        <div v-for="entry in profile?.recent_entries || []" :key="entry.id" class="border-b pb-2">
                            <router-link :to="`/title/${entry.title?.slug}`" class="text-green-700 hover:underline">
                                {{ entry.title?.title }}
                            </router-link>
                            <div class="text-xs text-gray-500">{{ formatDate(entry.created_at) }}</div>
                            <p class="text-gray-800 text-sm">{{ entry.content }}</p>
                        </div>
                    </div>
                </div>

                <div v-else>
                    <h2 class="font-semibold mb-3">favoriler</h2>
                    <div class="space-y-3">
                        <div v-for="fav in profile?.recent_favorites || []" :key="fav.id" class="border-b pb-2">
                            <router-link :to="`/title/${fav.title?.slug}`" class="text-green-700 hover:underline">
                                {{ fav.title?.title }}
                            </router-link>
                            <p class="text-gray-800 text-sm">{{ fav.content }}</p>
                        </div>
                    </div>
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

const props = defineProps({
    username: {
        type: String,
        required: true,
    }
});

const profile = ref(null);
const following = ref(false);
const activeTab = ref('entries');
const tabs = [
    { key: 'entries', label: 'entryler' },
    { key: 'titles', label: 'başlıklar' },
    { key: 'favorites', label: 'favoriler' },
];
const { user, isAuthenticated } = useAuth();

const formatDate = (value) => value ? new Date(value).toLocaleString('tr-TR') : '';

const fetchProfile = async () => {
    try {
        const response = await axios.get(`/api/users/by-username/${props.username}`);
        profile.value = response.data?.data;
        following.value = profile.value?.is_following || false;
    } catch (error) {
        console.error('profile fetch error', error);
    }
};

const toggleFollow = async () => {
    if (!profile.value) return;
    try {
        const response = await axios.post(`/api/users/${profile.value.id}/follow`);
        following.value = response.data?.data?.following;
        if (profile.value.followers_count !== undefined) {
            profile.value.followers_count = response.data?.data?.followers_count;
        }
    } catch (error) {
        console.error('follow error', error);
    }
};

onMounted(fetchProfile);
</script>
