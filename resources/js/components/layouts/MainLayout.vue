<template>
    <div>
        <Header />
        <SubHeader />

        <main class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-12 gap-6">
                <LeftSidebar />
                <div class="col-span-7">
                    <slot />
                </div>
                <RightSidebar />
            </div>
        </main>
    </div>
</template>

<script setup>
import Header from '../partials/Header.vue';
import SubHeader from '../partials/SubHeader.vue';
import LeftSidebar from '../partials/LeftSidebar.vue';
import RightSidebar from '../partials/RightSidebar.vue';
</script>

