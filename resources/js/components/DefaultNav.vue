<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
<template>
    <nav class="bg-[#04a96d] text-white">
        <div class="mx-auto flex max-w-7xl justify-between px-6 py-4">
            <div class="text-lg font-bold">
                <Link href="/">Agni wallet</Link>
            </div>

            <div class="space-x-4">
                <Link href="/dashboard">dashboard</Link>
                <Link href="/login">Login</Link>
                <Link href="/register">Register</Link>
            </div>
        </div>
    </nav>
</template>
