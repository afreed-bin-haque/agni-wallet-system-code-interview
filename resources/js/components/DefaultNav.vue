<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { Languages } from 'lucide-vue-next';
import { useToast } from 'vue-toast-notification';
const page = usePage();
const toast = useToast();
const currentLocale = (page.props.locale as string) ?? 'en';
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
        <div class="mx-auto flex max-w-7xl justify-between px-6 py-4">
            <div class="text-lg font-bold">
                <Link href="/">{{ __('AgniWallet') }}</Link>
            </div>

            <div class="space-x-4">
                <div class="flex items-center space-x-2">
                    <button
                        v-if="currentLocale === 'en'"
                        @click="switchLanguage('bn')"
                        class="flex cursor-pointer items-center gap-1 rounded bg-white px-3 py-1 text-black hover:bg-gray-100"
                    >
                        <Languages /> BN
                    </button>

                    <button
                        v-else
                        @click="switchLanguage('en')"
                        class="flex items-center gap-1 rounded bg-white px-3 py-1 text-black hover:bg-gray-100"
                    >
                        <Languages /> EN
                    </button>

                    <Link href="/login" class="cursor-pointer">{{ __('Login') }}</Link>
                    <Link href="/register" class="cursor-pointer">{{ __('Register') }}</Link>
                </div>
            </div>
        </div>
    </nav>
</template>
