<script setup lang="ts">
import DashNav from '@components/DashNav.vue';
import DashSidebar from '@components/DashSidebar.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{ title?: string }>();
</script>

<template>
    <Head :title="title ?? 'Agni Wallet'" />

    <div class="flex min-h-screen flex-col bg-[#f0f5ff]">
        <DashNav />

        <div class="mt-5 mb-5 flex w-full max-w-7xl gap-6 px-6">
            <div class="w-64 flex-shrink-0">
                <DashSidebar />
            </div>

            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
