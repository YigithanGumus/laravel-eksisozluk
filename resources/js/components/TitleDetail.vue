<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-2xl font-semibold">{{ title?.title || 'baslik' }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ title?.entry_count || 0 }} entry · son hareket: {{ formatDate(title?.last_activity_at) }}
                    </p>
                    <p v-if="title?.is_locked" class="text-sm text-red-600">kilitli {{ title?.lock_reason ? `(${title.lock_reason})` : '' }}</p>
                    <p v-if="title?.is_pinned" class="text-sm text-green-700">pinned {{ title?.pin_reason ? `(${title.pin_reason})` : '' }}</p>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <div class="font-semibold">{{ title?.user?.username || title?.user?.name }}</div>
                </div>
            </div>

            <div v-if="isModerator && title" class="mb-4 bg-gray-50 border rounded p-3 text-sm">
                <div class="font-semibold mb-2">moderasyon</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" v-model="modForm.is_locked" />
                        <span>kilitli</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" v-model="modForm.is_pinned" />
                        <span>pinned</span>
                    </label>
                </div>
                <textarea
                    v-model="modForm.lock_reason"
                    rows="2"
                    placeholder="kilit nedeni"
                    class="w-full mt-2 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                ></textarea>
                <textarea
                    v-model="modForm.pin_reason"
                    rows="2"
                    placeholder="pin nedeni"
                    class="w-full mt-2 px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                ></textarea>
                <div class="flex justify-end mt-2">
                    <button @click="updateTitleMod" class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">
                        kaydet
                    </button>
                </div>
            </div>

            <div v-if="isAuthenticated" class="mb-6">
                <textarea
                    v-model="entryContent"
                    rows="4"
                    placeholder="entry yaz..."
                    class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                ></textarea>
                <div class="flex items-center justify-between mt-2 text-sm">
                    <span class="text-red-600" v-if="entryError">{{ entryError }}</span>
                    <button
                        @click="submitEntry"
                        :disabled="submitting"
                        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800 disabled:opacity-60"
                    >
                        {{ submitting ? 'gonderiliyor...' : 'entry ekle' }}
                    </button>
                </div>
            </div>

            <div v-if="loading" class="text-gray-500 text-sm">yukleniyor...</div>
            <div v-else class="space-y-6">
                <div v-for="entry in entries" :key="entry.id" class="border-b pb-4">
                    <div class="text-sm text-gray-600 mb-2 flex justify-between items-center">
                        <div>
                            <span class="font-medium">
                                {{ entry.user?.username || entry.user?.name || 'anonim' }}
                            </span>
                            <span class="mx-2">·</span>
                            <span>{{ formatDate(entry.created_at) }}</span>
                            <span v-if="entry.is_deleted" class="ml-2 text-red-600">(silindi)</span>
                        </div>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <div class="flex items-center space-x-1">
                                <button
                                    @click="vote(entry, 'up')"
                                    class="text-gray-500 hover:text-green-700"
                                    title="sukela"
                                    :disabled="entry.is_deleted"
                                >▲</button>
                                <span>{{ entry.up_votes_count || 0 }}</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <button
                                    @click="vote(entry, 'down')"
                                    class="text-gray-500 hover:text-red-600"
                                    title="eksile"
                                    :disabled="entry.is_deleted"
                                >▼</button>
                                <span>{{ entry.down_votes_count || 0 }}</span>
                            </div>
                            <button
                                @click="toggleFavorite(entry)"
                                class="text-gray-500 hover:text-green-700 flex items-center space-x-1"
                                title="favori"
                                :disabled="entry.is_deleted"
                            >
                                <span>★</span>
                                <span>{{ entry.favorites_count || 0 }}</span>
                            </button>
                            <div class="flex items-center space-x-2">
                                <button class="text-gray-600 hover:underline" @click="reportEntry(entry)">rapor</button>
                                <template v-if="canEdit(entry)">
                                    <button class="text-green-700 hover:underline" @click="startEdit(entry)">duzenle</button>
                                    <button class="text-red-600 hover:underline" @click="removeEntry(entry)">sil</button>
                                    <button class="text-gray-600 hover:underline" @click="loadHistory(entry)">gecmis</button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div v-if="editingEntryId === entry.id" class="mb-2">
                        <textarea
                            v-model="editContent"
                            rows="3"
                            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                        ></textarea>
                        <div class="flex space-x-3 mt-2 text-sm">
                            <button @click="saveEdit(entry)" class="bg-green-700 text-white px-3 py-1 rounded">kaydet</button>
                            <button @click="cancelEdit" class="text-gray-600">vazgec</button>
                        </div>
                    </div>
                    <p v-else class="text-gray-800 whitespace-pre-line" v-html="formatContent(entry.content)"></p>

                    <div v-if="history[entry.id]" class="mt-2 bg-gray-50 border rounded p-2 text-xs text-gray-600">
                        <div class="font-semibold mb-1">gecmis</div>
                        <ul class="space-y-1 max-h-40 overflow-y-auto">
                            <li v-for="edit in history[entry.id]" :key="edit.id">
                                {{ formatDate(edit.created_at) }} · {{ edit.user?.username || edit.user?.name }}:
                                <span class="text-gray-800">{{ edit.content_after }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div v-if="pagination.total > pagination.per_page" class="flex justify-between items-center mt-6 text-sm">
                <button
                    @click="changePage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1"
                    class="text-gray-500 hover:text-green-700 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    onceki
                </button>
                <span class="text-gray-500">sayfa {{ pagination.current_page }}/{{ pagination.last_page }}</span>
                <button
                    @click="changePage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page"
                    class="text-gray-500 hover:text-green-700 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    sonraki
                </button>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import axios from 'axios';
import MainLayout from './layouts/MainLayout.vue';
import { useAuth } from '../composables/useAuth';

const props = defineProps({
    slug: {
        type: String,
        required: true,
    },
});

const title = ref(null);
const entries = ref([]);
const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0,
    per_page: 15,
});
const loading = ref(false);
const submitting = ref(false);
const entryContent = ref('');
const entryError = ref('');
const history = ref({});
const editingEntryId = ref(null);
const editContent = ref('');
const modForm = ref({
    is_locked: false,
    lock_reason: '',
    is_pinned: false,
    pin_reason: '',
});

const { user, isAuthenticated } = useAuth();
const isModerator = computed(() => !!user.value?.is_moderator);

const formatDate = (value) => {
    if (!value) return '';
    return new Date(value).toLocaleString('tr-TR');
};

const formatContent = (text) => {
    if (!text) return '';
    const escaped = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return escaped.replace(/(https?:\/\/[^\s]+)/g, '<a class="text-green-700 underline" href="$1" target="_blank" rel="noopener">$1</a>');
};

const canEdit = (entry) => {
    if (isModerator.value) return true;
    return user.value && entry.user_id === user.value.id;
};

const fetchTitle = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/api/titles/${props.slug}`, {
            params: { page },
        });

        title.value = response.data?.data?.title;
        if (title.value) {
            modForm.value = {
                is_locked: !!title.value.is_locked,
                lock_reason: title.value.lock_reason || '',
                is_pinned: !!title.value.is_pinned,
                pin_reason: title.value.pin_reason || '',
            };
        }

        const entriesData = response.data?.data?.entries;
        entries.value = entriesData?.data || [];
        if (entriesData) {
            pagination.value = {
                current_page: entriesData.current_page,
                last_page: entriesData.last_page,
                total: entriesData.total,
                per_page: entriesData.per_page,
            };
        }
    } catch (error) {
        console.error('title fetch error', error);
    } finally {
        loading.value = false;
    }
};

const submitEntry = async () => {
    if (!entryContent.value.trim()) {
        entryError.value = 'icerik bos olamaz';
        return;
    }
    entryError.value = '';
    submitting.value = true;
    try {
        await axios.post(`/api/entries/${props.slug}/store`, {
            content: entryContent.value,
        });
        entryContent.value = '';
        fetchTitle(pagination.value.current_page);
    } catch (error) {
        entryError.value = error.response?.data?.message || 'kaydedilemedi';
    } finally {
        submitting.value = false;
    }
};

const toggleFavorite = async (entry) => {
    if (!isAuthenticated.value || entry.is_deleted) return;
    try {
        const response = await axios.post(`/api/entries/${entry.id}/favorite`);
        entry.favorites_count = response.data?.data?.favorites_count ?? entry.favorites_count;
    } catch (error) {
        console.error('favorite error', error);
    }
};

const vote = async (entry, value) => {
    if (!isAuthenticated.value || entry.is_deleted) return;
    try {
        const response = await axios.post(`/api/entries/${entry.id}/vote`, { value });
        entry.up_votes_count = response.data?.data?.up_votes_count ?? entry.up_votes_count;
        entry.down_votes_count = response.data?.data?.down_votes_count ?? entry.down_votes_count;
    } catch (error) {
        console.error('vote error', error);
    }
};

const startEdit = (entry) => {
    if (entry.is_deleted) return;
    editingEntryId.value = entry.id;
    editContent.value = entry.content;
};

const cancelEdit = () => {
    editingEntryId.value = null;
    editContent.value = '';
};

const saveEdit = async (entry) => {
    try {
        await axios.patch(`/api/entries/${entry.id}`, { content: editContent.value });
        entry.content = editContent.value;
        editingEntryId.value = null;
        editContent.value = '';
        loadHistory(entry);
    } catch (error) {
        console.error('edit error', error);
    }
};

const removeEntry = async (entry) => {
    const reason = prompt('Silme sebebi (opsiyonel)');
    try {
        await axios.delete(`/api/entries/${entry.id}`, { data: { reason } });
        entry.is_deleted = true;
        entry.content = '[silindi]';
    } catch (error) {
        console.error('delete error', error);
    }
};

const loadHistory = async (entry) => {
    try {
        const response = await axios.get(`/api/entries/${entry.id}/history`);
        history.value = { ...history.value, [entry.id]: response.data?.data || [] };
    } catch (error) {
        console.error('history error', error);
    }
};

const reportEntry = async (entry) => {
    try {
        await axios.post('/api/reports', { type: 'entry', id: entry.id, reason: '' });
        alert('Rapor alindi.');
    } catch (error) {
        console.error('report error', error);
    }
};

const updateTitleMod = async () => {
    if (!title.value?.uuid) return;
    try {
        await axios.patch(`/api/titles/${title.value.uuid}`, {
            is_locked: modForm.value.is_locked ? 1 : 0,
            is_pinned: modForm.value.is_pinned ? 1 : 0,
            lock_reason: modForm.value.lock_reason,
            pin_reason: modForm.value.pin_reason,
        });
        title.value.is_locked = modForm.value.is_locked;
        title.value.is_pinned = modForm.value.is_pinned;
        title.value.lock_reason = modForm.value.lock_reason;
        title.value.pin_reason = modForm.value.pin_reason;
    } catch (error) {
        console.error('title update error', error);
    }
};

const changePage = (page) => {
    if (page < 1 || page > pagination.value.last_page) return;
    fetchTitle(page);
};

watch(() => props.slug, () => fetchTitle(1));
onMounted(fetchTitle);
</script>
