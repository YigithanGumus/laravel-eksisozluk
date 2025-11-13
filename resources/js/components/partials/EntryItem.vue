<template>
    <div class="border-b pb-4">
        <div class="text-sm text-gray-600 mb-2 flex justify-between items-center">
            <div>
                <router-link :to="`/user/${entry.author}`" class="hover:underline">
                    {{ entry.author }}
                </router-link>
                <span class="mx-2">·</span>
                <span>{{ entry.date }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <button
                    @click="$emit('like', entry.id)"
                    class="text-gray-400 hover:text-green-700"
                    :title="`${entry.likes} beğeni`"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
                <button
                    @click="$emit('share', entry.id)"
                    class="text-gray-400 hover:text-green-700"
                    title="Paylaş"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                    </svg>
                </button>
                <button
                    @click="$emit('report', entry.id)"
                    class="text-gray-400 hover:text-green-700"
                    title="Şikayet et"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            </div>
        </div>
        <p class="text-gray-800">{{ entry.content }}</p>
        <div class="mt-2" v-if="entry.tags && entry.tags.length > 0">
            <router-link
                v-for="tag in entry.tags"
                :key="tag"
                :to="`/tag/${tag}`"
                class="text-xs text-green-700 hover:underline mr-2"
            >
                #{{ tag }}
            </router-link>
        </div>
    </div>
</template>

<script setup>
defineProps({
    entry: {
        type: Object,
        required: true
    }
});

defineEmits(['like', 'share', 'report']);
</script>

