<script setup lang="ts">
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <DefaultLayout title="Register here">
        <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-lg">
                <h1 class="mb-2 text-center text-2xl font-semibold text-gray-900">Register your account</h1>
                <p class="mb-6 text-center text-sm text-gray-500">Enter your credentials to create your account</p>

                <form class="space-y-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700"> Name </label>
                        <input
                            type="text"
                            placeholder="afreed example"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700"> Email </label>
                        <input
                            type="email"
                            placeholder="m@example.com"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                    </div>

                    <div>
                        <div class="mb-1 flex items-center justify-between">
                            <label class="text-sm font-medium text-gray-700"> Password </label>
                        </div>
                        <input
                            type="password"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-md bg-[#04a96d] py-2 text-sm font-medium text-white hover:bg-green-300 hover:text-black"
                    >
                        Register
                    </button>
                </form>

                <p class="text-center text-sm text-gray-600">
                    Already have an Account?
                    <Link href="/login" class="font-medium text-black hover:underline"> Login here</Link>
                </p>
            </div>
        </div>
    </DefaultLayout>
</template>
