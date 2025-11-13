<template>
    <div class="bg-zinc-100 min-h-screen">
        <router-view />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useTheme } from '../composables/useTheme';

const { initTheme } = useTheme();

onMounted(() => {
    initTheme();
});
</script>
