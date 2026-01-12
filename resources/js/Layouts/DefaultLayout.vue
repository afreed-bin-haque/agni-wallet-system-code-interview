<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
}>();
</script>

<template>
    <Head :title="title ?? 'Agni Wallet'" />

    <div class="min-h-screen bg-[#f0f5ff]">
        <nav class="bg-[#04a96d] text-white">
            <div class="mx-auto flex max-w-7xl justify-between px-6 py-4">
                <div class="text-lg font-bold">
                    <Link href="/">Agni wallet</Link>
                </div>

                <div class="space-x-4">
                    <Link href="/dashboard">Dashboard</Link>
                    <Link href="/wallet">Wallet</Link>
                    <Link href="/login">Login</Link>
                    <Link href="/register">Register</Link>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl p-6">
            <slot />
        </main>
    </div>
</template>
