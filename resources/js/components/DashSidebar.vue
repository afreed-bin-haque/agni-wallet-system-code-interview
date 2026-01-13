<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Clock, CreditCard } from 'lucide-vue-next';
</script>

<template>
    <aside class="flex min-h-screen w-64 flex-col rounded-2xl bg-white p-6 font-semibold text-black shadow-md">
        <nav class="flex flex-col space-y-4">
            <Link href="/add-balance" class="flex cursor-pointer items-center gap-2 hover:text-green-700">
                <CreditCard class="h-5 w-5" />
                {{ __('AddBalance') }}
            </Link>
            <Link href="/history" class="flex cursor-pointer items-center gap-2 hover:text-green-700">
                <Clock class="h-5 w-5" />
                {{ __('History') }}
            </Link>
        </nav>
    </aside>
</template>
