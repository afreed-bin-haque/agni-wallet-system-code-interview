<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const name = props?.user.name || 'Guest';
</script>

<template>
    <DashboardLayout title="Dashboard ~ Agni .::.Afreed Bin Haque">
        <h1 class="text-2xl font-bold">{{ __('Greetings') }} {{ name }}</h1>
    </DashboardLayout>
</template>
