<script setup lang="ts">
import DefaultNav from '@components/DefaultNav.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <Head :title="title ?? 'Agni Wallet'" />

    <div class="min-h-screen bg-[#f0f5ff]">
        <DefaultNav />
        <main class="mx-auto max-w-7xl p-6">
            <slot />
        </main>
    </div>
</template>
