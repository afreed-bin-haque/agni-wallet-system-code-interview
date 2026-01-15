<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Clock, CreditCard, HandCoins, LayoutDashboard, Menu, X } from 'lucide-vue-next';
import { ref } from 'vue';

const isOpen = ref(false);

function toggleSidebar() {
    isOpen.value = !isOpen.value;
}
</script>

<template>
    <button
        @click="toggleSidebar"
        :class="['fixed bottom-4 left-4 z-50 rounded p-3 shadow-md md:hidden', isOpen ? 'bg-[#04a96d] text-white' : 'bg-white text-black']"
    >
        <Menu class="h-6 w-6" v-if="!isOpen" />
        <X class="h-6 w-6" v-else />
    </button>

    <aside
        :class="[
            'fixed top-0 left-0 z-40 h-full w-64 bg-[#bcffe6] p-6 font-semibold text-black shadow-md transition-transform duration-300 md:relative md:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <nav class="mt-8 flex flex-col space-y-4">
            <Link href="/dashboard" class="flex items-center gap-2 hover:text-green-700">
                <LayoutDashboard class="h-5 w-5" />
                {{ __('Dashboard') }}
            </Link>
            <Link href="/add-balance" class="flex items-center gap-2 hover:text-green-700">
                <CreditCard class="h-5 w-5" />
                {{ __('AddBalance') }}
            </Link>
            <Link href="/debit-balance" class="flex cursor-pointer items-center gap-2 hover:text-green-700">
                <HandCoins class="h-5 w-5" />
                {{ __('Debit') }}
            </Link>
            <Link href="/history" class="flex items-center gap-2 hover:text-green-700">
                <Clock class="h-5 w-5" />
                {{ __('History') }}
            </Link>
        </nav>
    </aside>

    <div v-if="isOpen" @click="toggleSidebar" class="fixed inset-0 z-30 bg-black/30 md:hidden"></div>
</template>
