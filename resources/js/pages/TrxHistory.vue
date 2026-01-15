<script setup lang="ts">
import SuccessfulTransactionHistory from '@/components/SuccessfulTransactionHistory.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
</script>

<template>
    <DashboardLayout title="Dashboard ~ Agni .::.Afreed Bin Haque">
        <h1 class="text-2xl font-bold">{{ __('SuccessfullTransactionHistoryStatement') }}</h1>
        <div class="mt-4"><SuccessfulTransactionHistory /></div>
    </DashboardLayout>
</template>
