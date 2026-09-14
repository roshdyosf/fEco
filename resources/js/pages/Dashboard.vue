<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div
                class="bg-white shadow rounded-lg p-6 flex justify-between items-center"
            >
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Family Dashboard
                    </h1>
                    <p class="text-sm text-gray-600">
                        Welcome back, {{ user?.name }}!
                    </p>
                </div>
                <div>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                    >
                        Family Member
                    </span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">
                        Total Balance
                    </h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">$0.00</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">
                        Monthly Expenses
                    </h3>
                    <p class="mt-2 text-3xl font-bold text-red-600">$0.00</p>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">
                        Monthly Income
                    </h3>
                    <p class="mt-2 text-3xl font-bold text-green-600">$0.00</p>
                </div>
            </div>

            <!-- Content Area -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Recent Transactions
                </h3>
                <p class="text-sm text-gray-500">
                    No transactions recorded yet.
                </p>
            </div>
        </div>
    </div>
</template>
