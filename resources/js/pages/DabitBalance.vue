<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { props } = usePage();
const getBalance = props.wallet?.balance || 0;
const phone = ref('');
const amount = ref<number | null>(null);
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const submitting = ref(false);

const isPhoneValid = computed(() => /^01\d{9}$/.test(phone.value));
const isAmountValid = computed(() => amount.value !== null && amount.value > 0);
const canSubmit = computed(() => isPhoneValid.value && isAmountValid.value);

const handleSubmit = (e: Event) => {
    submitting.value = true;
    (e.target as HTMLFormElement).submit();
};
</script>

<template>
    <DashboardLayout title="Dashboard ~ Agni .::. Afreed Bin Haque">
        <h1 class="text-2xl font-bold">{{ __('Balance') }}: {{ getBalance }} ৳</h1>

        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
                <h1 class="mb-2 text-center text-2xl font-semibold text-gray-900">
                    {{ __('Debit') }}
                </h1>

                <form class="space-y-5" method="POST" action="/request-to-add-balance" @submit.prevent="handleSubmit">
                    <input type="hidden" name="_token" :value="csrfToken" />

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                        <input
                            type="text"
                            name="phone"
                            v-model="phone"
                            placeholder="01*********"
                            class="w-full rounded-md border px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                        <p v-if="phone && !isPhoneValid" class="mt-1 text-sm text-red-500">Invalid phone number</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">{{ __('Add Amount') }}</label>
                        <input
                            type="number"
                            name="amount"
                            v-model.number="amount"
                            placeholder="10"
                            class="w-full rounded-md border px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                        <p v-if="amount !== null && !isAmountValid" class="mt-1 text-sm text-red-500">Amount must be greater than 0</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="!canSubmit || submitting"
                        class="w-full rounded-md bg-[#04a96d] py-2 text-white hover:bg-green-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        Debit Balance
                    </button>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
