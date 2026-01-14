<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { Eye, EyeOff } from 'lucide-vue-next';
import { reactive, ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.withCredentials = true;

const form = reactive({ email: '', password: '' });
const errors = reactive({ email: '', password: '' });
const submitting = ref(false);
const toast = useToast();
const showPassword = ref(false);

const submit = async () => {
    errors.email = '';
    errors.password = '';
    submitting.value = true;
    try {
        const res = await axios.post('/login-user', form);
        const resData = res?.data;
        if (resData?.status) {
            form.email = '';
            form.password = '';
            toast.success('Login successful!');
            Inertia.visit('/dashboard');
        } else {
            toast.error(resData?.msg || 'Unprocessable request');
        }
    } catch (err: any) {
        if (err.response?.status === 422) {
            const vErrors = err.response.data.errors;
            errors.email = vErrors?.email?.[0] ?? '';
            errors.password = vErrors?.password?.[0] ?? '';
        } else {
            toast.error(err.response?.data?.msg || 'Login failed');
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <DefaultLayout title="Login here">
        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
                <h1 class="mb-2 text-center text-2xl font-semibold text-gray-900">{{ __('Logintoyouraccount') }}</h1>
                <p class="mb-6 text-center text-sm text-gray-500">{{ __('LoginHelper') }}</p>

                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            placeholder="example@example.com"
                            v-model="form.email"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                        <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                class="w-full rounded-md border border-gray-300 px-3 py-2 pr-10 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                            />
                            <span class="absolute top-2.5 right-3 cursor-pointer" @click="showPassword = !showPassword">
                                <component :is="showPassword ? EyeOff : Eye" class="h-5 w-5 text-gray-500" />
                            </span>
                        </div>
                        <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full rounded-md bg-[#04a96d] py-2 text-white hover:bg-green-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span v-if="submitting">Please wait...</span>
                        <span v-else>Login</span>
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    {{ __('RegisterPromntOne') }}
                    <Link href="/register" class="font-medium text-black hover:underline">{{ __('RegisterPromntTwo') }}</Link>
                </p>
            </div>
        </div>
    </DefaultLayout>
</template>
