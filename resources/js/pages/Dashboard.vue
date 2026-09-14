<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Define the props coming from the Laravel controller
const props = defineProps({
    family: Object,
    recent_transactions: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

// Simple function to copy the invite code
const copyInviteCode = () => {
    navigator.clipboard.writeText(props.family.invite_code);
    alert("Invite code copied to clipboard!");
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div
                class="bg-white shadow rounded-lg p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ props.family.name }} Dashboard
                    </h1>
                    <p class="text-sm text-gray-600">
                        Welcome back, {{ user?.name }}!
                    </p>
                </div>

                <!-- Invite Code Display -->
                <div
                    class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg border border-gray-200"
                >
                    <span
                        class="text-xs text-gray-500 uppercase tracking-wider font-semibold"
                        >Invite Code:</span
                    >
                    <span
                        class="text-sm font-mono font-bold text-indigo-600 tracking-widest"
                        >{{ props.family.invite_code }}</span
                    >
                    <button
                        @click="copyInviteCode"
                        class="ml-2 text-gray-400 hover:text-gray-700 transition-colors"
                        title="Copy Code"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                            ></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-500"
                >
                    <h3 class="text-sm font-medium text-gray-500">
                        Total Family Balance
                    </h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        ${{ props.family.total_balance }}
                    </p>
                </div>
                <div
                    class="bg-white shadow rounded-lg p-6 border-l-4 border-red-500"
                >
                    <h3 class="text-sm font-medium text-gray-500">
                        Monthly Expenses
                    </h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">$0.00</p>
                </div>
                <div
                    class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500"
                >
                    <h3 class="text-sm font-medium text-gray-500">
                        Monthly Income
                    </h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">$0.00</p>
                </div>
            </div>

            <!-- Transactions Area -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Recent Transactions
                </h3>

                <div
                    v-if="props.recent_transactions.length === 0"
                    class="text-center py-8"
                >
                    <p class="text-sm text-gray-500">
                        No transactions recorded yet. Start managing your
                        expenses!
                    </p>
                </div>

                <ul v-else class="divide-y divide-gray-200">
                    <!-- We will loop through real transactions here later -->
                </ul>
            </div>
        </div>
    </div>
</template>
