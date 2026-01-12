<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
<template>
    <aside class="flex min-h-screen w-64 flex-col rounded-2xl bg-white p-6 text-[#04a96d] shadow-md">
        <nav class="flex flex-col space-y-4">
            <Link href="/add-balance" class="hover:text-green-700">Add Balance</Link>
            <Link href="/history" class="hover:text-green-700">History</Link>
        </nav>
    </aside>
</template>
