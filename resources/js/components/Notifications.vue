<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">bildirimler</h1>
                    <p class="text-sm text-gray-500">mention ve diğer bildirimler</p>
                </div>
                <button @click="loadNotifications" class="text-sm text-green-700 hover:underline">yenile</button>
            </div>

            <div v-if="loading" class="text-gray-500 text-sm">yükleniyor...</div>
            <div v-else>
                <div
                    v-for="note in notifications"
                    :key="note.id"
                    class="border-b py-3 flex items-start justify-between"
                >
                    <div>
                        <div class="text-sm">
                            <span class="font-semibold">{{ note.type }}</span>
                            <span class="text-gray-500 ml-2">{{ formatDate(note.created_at) }}</span>
                        </div>
                        <div class="text-gray-800 text-sm">
                            <span v-if="note.type === 'mention'">
                                {{ note.data?.from_user?.username }} seni bir entry’de andı.
                                <router-link
                                    v-if="note.data?.title_slug"
                                    :to="`/title/${note.data.title_slug}`"
                                    class="text-green-700 underline ml-1"
                                >görüntüle</router-link>
                            </span>
                            <span v-else>{{ note.data?.message || 'Yeni bildirim' }}</span>
                        </div>
                    </div>
                    <button
                        v-if="!note.is_read"
                        @click="markAsRead(note.id)"
                        class="text-xs text-green-700 hover:underline"
                    >
                        okundu işaretle
                    </button>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import MainLayout from './layouts/MainLayout.vue';
import { useNotifications } from '../composables/useNotifications';

const { notifications, fetchNotifications, markRead } = useNotifications();
const loading = ref(false);

const formatDate = (value) => value ? new Date(value).toLocaleString('tr-TR') : '';

const loadNotifications = async () => {
    loading.value = true;
    await fetchNotifications();
    loading.value = false;
};

const markAsRead = async (id) => {
    await markRead(id);
};

onMounted(loadNotifications);
</script>
