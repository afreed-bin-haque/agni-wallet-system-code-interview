<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Languages } from 'lucide-vue-next';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

const toast = useToast();
const page = usePage();
const currentLocale = (page.props.locale as string) ?? 'en';

const handleLogout = async () => {
    try {
        const res = await axios.get('/logout', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            withCredentials: true,
        });
        if (res?.data?.status) {
            toast.success(res.data.msg || 'Logged out successfully!');
            window.location.reload();
        } else {
            toast.error('Logout failed');
        }
    } catch {
        toast.error('Logout failed');
    }
};

const switchLanguage = async (lang: string) => {
    try {
        await axios.get('/language', {
            params: { lang },
            withCredentials: true,
        });
        window.location.reload();
    } catch {
        toast.error('Language switch failed');
    }
};
</script>

<template>
    <nav class="bg-[#04a96d] text-white">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <div class="text-lg font-bold">
                <Link href="/">{{ __('AgniWallet') }}</Link>
            </div>

            <div class="flex items-center space-x-2">
                <button
                    v-if="currentLocale === 'en'"
                    @click="switchLanguage('bn')"
                    class="flex cursor-pointer items-center gap-1 rounded bg-white px-3 py-1 text-black hover:bg-gray-100"
                >
                    <Languages /> BN
                </button>

                <button v-else @click="switchLanguage('en')" class="flex items-center gap-1 rounded bg-white px-3 py-1 text-black hover:bg-gray-100">
                    <Languages /> EN
                </button>

                <button @click="handleLogout" class="cursor-pointer">{{ __('Logout') }}</button>
            </div>
        </div>
    </nav>
</template>
