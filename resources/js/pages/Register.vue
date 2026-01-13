<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';
import { reactive, ref } from 'vue';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
axios.defaults.withCredentials = true;

const form = reactive({
    name: '',
    email: '',
    password: '',
});

const errors = reactive({
    name: '',
    email: '',
    password: '',
});

const submitting = ref(false);
const toast = useToast();

const submit = async () => {
    errors.name = '';
    errors.email = '';
    errors.password = '';
    submitting.value = true;

    try {
        const res = await axios.post('/register-user', form);
        const resData = res?.data;

        if (resData?.status) {
            form.name = '';
            form.email = '';
            form.password = '';
            toast.success('Registration successful!');
            Inertia.visit('/login');
        } else {
            toast.error(resData?.msg || 'Unprocessable request');
        }
    } catch (err: any) {
        if (err.response?.status === 422) {
            const vErrors = err.response.data.errors;
            errors.name = vErrors?.name?.[0] ?? '';
            errors.email = vErrors?.email?.[0] ?? '';
            errors.password = vErrors?.password?.[0] ?? '';
        } else {
            toast.error(err.response?.data?.msg || 'Registration failed');
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <DefaultLayout title="Register here">
        <div class="flex min-h-screen items-center justify-center px-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
                <h1 class="mb-2 text-center text-2xl font-semibold text-gray-900">Register your account</h1>
                <p class="mb-6 text-center text-sm text-gray-500">Enter your credentials to create your account</p>

                <form class="space-y-5" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                        <input v-model="form.name" type="text" class="w-full rounded-md border px-3 py-2" />
                        <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" class="w-full rounded-md border px-3 py-2" />
                        <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email }}</p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                        <input v-model="form.password" type="password" class="w-full rounded-md border px-3 py-2" />
                        <p v-if="errors.password" class="mt-1 text-xs text-red-500">{{ errors.password }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="w-full rounded-md bg-[#04a96d] py-2 text-white hover:bg-green-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <span v-if="submitting">Registering...</span>
                        <span v-else>Register</span>
                    </button>
                </form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Already have an Account?
                    <Link href="/login" class="font-medium text-black hover:underline"> Login here </Link>
                </p>
            </div>
        </div>
    </DefaultLayout>
</template>
