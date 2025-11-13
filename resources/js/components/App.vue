<template>
    <div class="bg-zinc-100 min-h-screen">
        <router-view />
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useTheme } from '../composables/useTheme';
import { useAuth } from '../composables/useAuth';

const { initTheme } = useTheme();
const { initAuth, fetchUser } = useAuth();

onMounted(() => {
    initTheme();
    initAuth();
    // Token varsa kullanıcı bilgilerini al
    if (localStorage.getItem('auth_token')) {
        fetchUser();
    }
});
</script>
