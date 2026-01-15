<script setup lang="ts">
import TransactionHistory from '@/components/TransactionHistory.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { usePage } from '@inertiajs/vue3';

const { props } = usePage();
const name = props?.user.name || 'Guest';
const getBalance = props.wallet?.balance || 0;
</script>

<template>
    <DashboardLayout title="Dashboard ~ Agni .::.Afreed Bin Haque">
        <div class="flex justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Greetings') }} {{ name }}</h1>
            </div>
            <div>
                <p class="text-md font-semibold">{{ __('currentBalance') }} {{ getBalance }} ৳</p>
            </div>
        </div>
        <div class="mt-4"><TransactionHistory /></div>
    </DashboardLayout>
</template>
