<template>
    <MainLayout>
        <EntryList :topic-title="topicTitle" />
    </MainLayout>
</template>

<script setup>
import { ref } from 'vue';
import MainLayout from './layouts/MainLayout.vue';
import EntryList from './partials/EntryList.vue';

const topicTitle = ref('6 kasım 2025 dolar kuru');
</script>
