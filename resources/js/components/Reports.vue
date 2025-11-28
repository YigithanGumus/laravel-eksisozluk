<template>
    <MainLayout>
        <div class="bg-white rounded p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-semibold">raporlar</h1>
                <button @click="fetchReports" class="text-sm text-green-700 hover:underline">yenile</button>
            </div>
            <div v-if="loading" class="text-sm text-gray-500">yükleniyor...</div>
            <div v-else class="space-y-3">
                <div v-for="report in reports" :key="report.id" class="border rounded p-3 text-sm">
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold">{{ report.reportable_type?.split('\\').pop() }} #{{ report.reportable_id }}</div>
                            <div class="text-gray-600">raporlayan: {{ report.reporter?.username || report.reported_by }}</div>
                            <div class="text-gray-800 mt-1">sebep: {{ report.reason || '-' }}</div>
                            <div class="text-xs text-gray-500 mt-1">durum: {{ report.status || 'open' }}</div>
                        </div>
                        <div class="text-xs text-gray-500">{{ formatDate(report.created_at) }}</div>
                    </div>
                    <div class="mt-2">
                        <textarea
                            v-model="resolutions[report.id]"
                            rows="2"
                            placeholder="cevap / aksiyon"
                            class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-600"
                        ></textarea>
                        <div class="flex space-x-2 mt-2">
                            <button @click="resolve(report, 'resolved')" class="bg-green-700 text-white px-3 py-1 rounded">çöz</button>
                            <button @click="resolve(report, 'ignored')" class="bg-gray-200 text-gray-700 px-3 py-1 rounded">yoksay</button>
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

const reports = ref([]);
const resolutions = ref({});
const loading = ref(false);

const formatDate = (value) => value ? new Date(value).toLocaleString('tr-TR') : '';

const fetchReports = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/reports');
        reports.value = response.data?.data?.data || [];
    } catch (error) {
        console.error('reports fetch error', error);
    } finally {
        loading.value = false;
    }
};

const resolve = async (report, status) => {
    try {
        await axios.post(`/api/reports/${report.id}/resolve`, {
            status,
            resolution: resolutions.value[report.id] || '',
        });
        report.status = status;
    } catch (error) {
        console.error('report resolve error', error);
    }
};

onMounted(fetchReports);
</script>
