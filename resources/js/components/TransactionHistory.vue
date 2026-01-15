<script setup lang="ts">
import axios from 'axios';
import { reactive, ref } from 'vue';

const transactions = ref<any[]>([]);
const loading = ref(false);
const search = ref('');
const pagination = reactive({
    current_page: 1,
    last_page: 1,
    per_page: 25,
    total: 0,
});

const fetchTransactions = async (page = 1) => {
    loading.value = true;
    try {
        const params: any = { page };
        if (search.value.trim() !== '') params.search = search.value.trim();
        const res = await axios.get('/get-transaction-table', { params });
        const data = res.data.result;
        transactions.value = data?.data ?? [];
        pagination.current_page = data?.current_page ?? 1;
        pagination.last_page = data?.last_page ?? 1;
        pagination.per_page = data?.per_page ?? 25;
        pagination.total = data?.total ?? 0;
    } catch {
        transactions.value = [];
        pagination.current_page = 1;
        pagination.last_page = 1;
        pagination.total = 0;
    } finally {
        loading.value = false;
    }
};

const handlePageChange = (page: number) => {
    if (page >= 1 && page <= pagination.last_page) fetchTransactions(page);
};

const handleSearch = () => fetchTransactions(1);

fetchTransactions();
</script>

<template>
    <div class="w-full max-w-4xl rounded-2xl bg-white p-6 shadow-lg">
        <h2 class="mb-2 text-xl font-semibold">{{ __('transactionHeader') }}</h2>
        <p class="text-gray-700">{{ __('transactionHeaderHelper') }}</p>

        <div class="mt-4 flex gap-2">
            <input
                type="text"
                v-model="search"
                @keyup.enter="handleSearch"
                placeholder="Search by phone, payment ID, trx ID"
                class="flex-1 rounded-md border px-3 py-2 focus:border-[#04a96d] focus:ring-1 focus:ring-[#04a96d] focus:outline-none"
            />
            <button @click="handleSearch" class="rounded-md bg-[#04a96d] px-4 py-2 text-white hover:bg-green-600">Search</button>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full table-auto rounded-lg border border-gray-200 shadow-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Sl.</th>
                        <th class="px-4 py-2 text-left">Phone</th>
                        <th class="px-4 py-2 text-left">Amount</th>
                        <th class="px-4 py-2 text-left">Payment ID</th>
                        <th class="px-4 py-2 text-left">TRX ID</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="loading">
                        <td colspan="7" class="py-4 text-center text-gray-500">Loading...</td>
                    </tr>
                    <tr v-else-if="!transactions?.length">
                        <td colspan="7" class="py-4 text-center text-gray-500">No transactions found</td>
                    </tr>
                    <tr v-else v-for="(trx, index) in transactions" :key="trx.id" class="border-b">
                        <td class="px-4 py-2">{{ index + 1 + (pagination.current_page - 1) * pagination.per_page }}</td>
                        <td class="px-4 py-2">{{ trx.phone }}</td>
                        <td class="px-4 py-2">{{ trx.amount }}</td>
                        <td class="px-4 py-2 break-all">{{ trx.payment_id ?? '-' }}</td>
                        <td class="px-4 py-2 break-all">{{ trx.trx_id ?? '-' }}</td>
                        <td class="px-4 py-2 capitalize">{{ trx.status }}</td>
                        <td class="px-4 py-2">{{ new Date(trx.created_at).toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex justify-center space-x-2">
            <button
                class="rounded-md border px-3 py-1 hover:bg-gray-100 disabled:opacity-50"
                :disabled="pagination.current_page === 1"
                @click="handlePageChange(pagination.current_page - 1)"
            >
                Prev
            </button>

            <button
                v-for="page in pagination.last_page"
                :key="page"
                @click="handlePageChange(page)"
                class="rounded-md border px-3 py-1 hover:bg-gray-100"
                :class="{ 'bg-[#04a96d] text-white': page === pagination.current_page }"
            >
                {{ page }}
            </button>

            <button
                class="rounded-md border px-3 py-1 hover:bg-gray-100 disabled:opacity-50"
                :disabled="pagination.current_page === pagination.last_page"
                @click="handlePageChange(pagination.current_page + 1)"
            >
                Next
            </button>
        </div>
    </div>
</template>
