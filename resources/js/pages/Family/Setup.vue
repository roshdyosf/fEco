<script setup>
import { useForm } from '@inertiajs/vue3';

const createForm = useForm({
    name: '',
});

const joinForm = useForm({
    invite_code: '',
});

const submitCreate = () => {
    createForm.post('/family/create');
};

const submitJoin = () => {
    joinForm.post('/family/join');
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Welcome to Family Finance 👋
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    To get started, please create a new family or join an existing one using an invite code.
                </p>
            </div>

            <div class="mt-8 space-y-6">
                <!-- Create Family Form -->
                <div class="p-6 bg-blue-50 rounded-lg border border-blue-100">
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Create a New Family</h3>
                    <p class="text-xs text-blue-700 mb-4">You will be designated as the family head and receive an invite code.</p>

                    <form @submit.prevent="submitCreate">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Family Name</label>
                            <input
                                type="text"
                                id="name"
                                v-model="createForm.name"
                                required
                                class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="e.g., Al-Rashed Family"
                            >
                            <span v-if="createForm.errors.name" class="text-red-500 text-xs mt-1">{{ createForm.errors.name }}</span>
                        </div>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none"
                        >
                            Create Family
                        </button>
                    </form>
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-sm">OR</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- Join Family Form -->
                <div class="p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Join an Existing Family</h3>
                    <p class="text-xs text-gray-600 mb-4">Enter the invite code shared by your family head.</p>

                    <form @submit.prevent="submitJoin">
                        <div class="mb-4">
                            <label for="invite_code" class="block text-sm font-medium text-gray-700 mb-1">Invite Code</label>
                            <input
                                type="text"
                                id="invite_code"
                                v-model="joinForm.invite_code"
                                required
                                class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 uppercase focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="e.g., A8B9C2D1"
                            >
                            <span v-if="joinForm.errors.invite_code" class="text-red-500 text-xs mt-1">{{ joinForm.errors.invite_code }}</span>
                        </div>
                        <button
                            type="submit"
                            :disabled="joinForm.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900 focus:outline-none"
                        >
                            Join Family
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
