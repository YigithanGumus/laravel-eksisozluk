<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold">{{ profile?.username }}</h1>
                    <p class="text-gray-600">{{ profile?.bio }}</p>
                    <div class="text-sm text-gray-500 mt-2">
                        {{ profile?.followers_count || 0 }} takipçi · {{ profile?.following_count || 0 }} takip
                    </div>
                </div>
                <div v-if="isAuthenticated && profile?.id !== user?.id">
                    <button
                        @click="toggleFollow"
                        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 text-sm"
                    >
                        {{ following ? 'takipten çık' : 'takip et' }}
                    </button>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="font-semibold mb-3">başlıklar</h2>
                    <ul class="space-y-2">
                        <li v-for="title in profile?.titles || []" :key="title.id">
                            <router-link :to="`/title/${title.slug}`" class="text-green-700 hover:underline">
                                {{ title.title }}
                            </router-link>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="font-semibold mb-3">son entry'ler</h2>
                    <div class="space-y-3">
                        <div v-for="entry in profile?.entries || []" :key="entry.id" class="border-b pb-2">
                            <router-link :to="`/title/${entry.title?.slug}`" class="text-green-700 hover:underline">
                                {{ entry.title?.title }}
                            </router-link>
                            <div class="text-xs text-gray-500">{{ formatDate(entry.created_at) }}</div>
                            <p class="text-gray-800 text-sm">{{ entry.content }}</p>
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
