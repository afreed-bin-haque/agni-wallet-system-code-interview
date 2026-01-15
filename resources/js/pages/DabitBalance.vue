<script setup lang="ts">
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, reactive, ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

const { props } = usePage();
const getBalance = props.wallet?.balance || 0;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.withCredentials = true;

const toast = useToast();

const form = reactive({
    phone: '',
    amount: null as number | null,
});

const errors = reactive({
    phone: '',
    amount: '',
});

const submitting = ref(false);

const isPhoneValid = computed(() => /^01\d{9}$/.test(form.phone));
const isAmountValid = computed(() => form.amount !== null && form.amount > 0);
const canSubmit = computed(() => isPhoneValid.value && isAmountValid.value);

const submit = async () => {
    errors.phone = '';
    errors.amount = '';

    if (!canSubmit.value) {
        if (!isPhoneValid.value) errors.phone = 'Invalid phone number';
        if (!isAmountValid.value) errors.amount = 'Amount must be greater than 0';
        return;
    }

    submitting.value = true;
    try {
        const res = await axios.post('/request-to-debit-balance', form);
        const resData = res.data;

        if (resData?.status) {
            toast.success(resData.msg || 'Balance debited successfully');
            form.phone = '';
            form.amount = null;
        } else {
            toast.error(resData.msg || 'Request failed');
        }
    } catch (err: any) {
        toast.error(err.response?.data?.msg || 'Request failed');
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <DashboardLayout title="Debit ~ Agni .::. Afreed Bin Haque">
        <div class="flex justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">{{ __('Debit') }}</h1>
            </div>
            <div>
                <p class="text-2xl font-bold">{{ __('Balance') }}: {{ getBalance }} ৳</p>
            </div>
        </div>
        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Phone Number</label>
                        <input
                            type="text"
                            v-model="form.phone"
                            placeholder="01*********"
                            class="w-full rounded-md border px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                        <p v-if="errors.phone" class="mt-1 text-sm text-red-500">{{ errors.phone }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Amount</label>
                        <input
                            type="number"
                            v-model.number="form.amount"
                            placeholder="10"
                            min="1"
                            class="w-full rounded-md border px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                        <p v-if="errors.amount" class="mt-1 text-sm text-red-500">{{ errors.amount }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="!canSubmit || submitting"
                        class="w-full rounded-md bg-[#04a96d] py-2 text-white hover:bg-green-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span v-if="submitting">Please wait...</span>
                        <span v-else>Debit Balance</span>
                    </button>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
