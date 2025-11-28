<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold">mesajlar</h1>
                <button @click="fetchMessages" class="text-sm text-green-700 hover:underline">yenile</button>
            </div>

            <div class="mb-4">
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-1 relative">
                        <input v-model="userSearch" type="text" placeholder="alıcı kullanıcı adı"
                            @input="searchUsers"
                            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600" />
                        <ul v-if="userSuggestions.length" class="absolute z-10 bg-white border rounded w-full max-h-40 overflow-y-auto text-sm">
                            <li
                                v-for="s in userSuggestions"
                                :key="s.id"
                                class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                @click="selectUser(s)"
                            >
                                {{ s.username }} ({{ s.name }})
                            </li>
                        </ul>
                    </div>
                    <input v-model="newMessage.content" type="text" placeholder="mesaj"
                        class="col-span-2 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600" />
                </div>
                <div class="flex justify-end mt-2">
                    <button @click="sendMessage" class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">gönder</button>
                </div>
            </div>

            <div v-if="loading" class="text-sm text-gray-500">yükleniyor...</div>
            <div v-else class="space-y-3">
                <div v-for="msg in messages" :key="msg.id" class="border rounded p-3">
                    <div class="text-xs text-gray-500 flex justify-between">
                        <span>{{ msg.sender?.username || msg.sender_id }} ➜ {{ msg.receiver?.username || msg.receiver_id }}</span>
                        <span>{{ formatDate(msg.created_at) }}</span>
                    </div>
                    <p class="mt-1 text-gray-800">{{ msg.content }}</p>
                    <div class="text-xs text-gray-500 mt-1">
                        <button v-if="!msg.read_at" @click="markRead(msg)" class="text-green-700 hover:underline">okundu işaretle</button>
                        <span v-else>okundu</span>
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

const messages = ref([]);
const loading = ref(false);
const newMessage = ref({
    receiver_username: '',
    content: '',
});
const userSearch = ref('');
const userSuggestions = ref([]);
let searchTimeout = null;

const formatDate = (value) => value ? new Date(value).toLocaleString('tr-TR') : '';

const fetchMessages = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/messages');
        messages.value = response.data?.data?.data || [];
    } catch (error) {
        console.error('messages fetch error', error);
    } finally {
        loading.value = false;
    }
};

const searchUsers = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        if (!userSearch.value || userSearch.value.length < 2) {
            userSuggestions.value = [];
            return;
        }
        try {
            const response = await axios.get('/api/users', { params: { search: userSearch.value } });
            userSuggestions.value = response.data?.data?.data || [];
        } catch (error) {
            console.error('user search error', error);
        }
    }, 250);
};

const selectUser = (user) => {
    newMessage.value.receiver_username = user.username;
    userSearch.value = user.username;
    userSuggestions.value = [];
};

const sendMessage = async () => {
    try {
        await axios.post('/api/messages', {
            receiver_username: newMessage.value.receiver_username || userSearch.value,
            content: newMessage.value.content,
        });
        newMessage.value.receiver_username = '';
        newMessage.value.content = '';
        userSearch.value = '';
        userSuggestions.value = [];
        fetchMessages();
    } catch (error) {
        console.error('send message error', error);
    }
};

const markRead = async (msg) => {
    try {
        await axios.post(`/api/messages/${msg.id}/read`);
        msg.read_at = new Date().toISOString();
    } catch (error) {
        console.error('mark read error', error);
    }
};

onMounted(fetchMessages);
</script>
