<script setup lang="ts">
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

axios.defaults.withCredentials = true;

const toast = useToast();

const handleLogout = async () => {
    try {
        const res = await axios.get('/logout', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (res?.data?.status) {
            toast.success(res.data.msg || 'Logged out successfully!');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = res.data.csrf_token;
            Inertia.visit(res.data.redirect);
        } else {
            toast.error('Logout failed');
        }
    } catch (err) {
        toast.error('Logout failed');
    }
};
</script>

<template>
    <nav class="bg-[#04a96d] text-white">
        <div class="mx-auto flex max-w-7xl justify-between px-6 py-4">
            <div class="text-lg font-bold">
                <Link href="/">Agni wallet</Link>
            </div>

            <div class="space-x-4">
                <Link href="/wallet" class="cursor-pointer">Wallet</Link>
                <button @click="handleLogout" class="cursor-pointer">Logout</button>
            </div>
        </div>
    </nav>
</template>
