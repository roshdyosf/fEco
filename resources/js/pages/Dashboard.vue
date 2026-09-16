<script setup>
import { Head, usePage, Link, useForm } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    family: Object,
    monthly_income: Number,
    monthly_expenses: Number,
    recent_transactions: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const copyInviteCode = () => {
    navigator.clipboard.writeText(props.family.invite_code);
    alert("Invite code copied to clipboard!");
};

// Form for creating a new transaction
const transactionForm = useForm({
    type: 'expense',
    amount: '',
    description: '',
    date: new Date().toISOString().split('T')[0], // Today's date by default
});

const submitTransaction = () => {
    transactionForm.post('/transactions', {
        preserveScroll: true,
        onSuccess: () => transactionForm.reset('amount', 'description'),
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gray-50">
        <!-- Navigation Bar -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center font-extrabold text-xl text-indigo-600">
                            FamilyTracker
                        </div>
                        <div class="hidden sm:-my-px sm:ml-6 sm:flex sm:space-x-8">
                            <Link href="/dashboard" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </Link>
                            <Link href="/family/settings" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors">
                                Family Settings
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ user?.name }}</span>
                        <Link href="/logout" method="post" as="button" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors">
                            Logout
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Header Section -->
                <div class="bg-white shadow rounded-lg p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ props.family.name }} Dashboard</h1>
                        <p class="text-sm text-gray-600">Welcome back, {{ user?.name }}!</p>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-100 px-4 py-2 rounded-lg border border-gray-200">
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Invite Code:</span>
                        <span class="text-sm font-mono font-bold text-indigo-600 tracking-widest">{{ props.family.invite_code }}</span>
                        <button @click="copyInviteCode" class="ml-2 text-gray-400 hover:text-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-sm font-medium text-gray-500">Total Family Balance</h3>
                        <p class="mt-2 text-3xl font-bold text-gray-900">${{ props.family.total_balance }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6 border-l-4 border-red-500">
                        <h3 class="text-sm font-medium text-gray-500">Monthly Expenses</h3>
                        <p class="mt-2 text-3xl font-bold text-red-600">${{ props.monthly_expenses }}</p>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-sm font-medium text-gray-500">Monthly Income</h3>
                        <p class="mt-2 text-3xl font-bold text-green-600">${{ props.monthly_income }}</p>
                    </div>
                </div>

                <!-- Two-Column Layout for Form and List -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <!-- Add Transaction Form -->
                    <div class="bg-white shadow rounded-lg p-6 lg:col-span-1 h-fit">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Transaction</h3>
                        <form @submit.prevent="submitTransaction" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Type</label>
                                <select v-model="transactionForm.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="expense">Expense</option>
                                    <option value="income">Income</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount ($)</label>
                                <input type="number" step="0.01" min="0.01" v-model="transactionForm.amount" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" v-model="transactionForm.description" required placeholder="e.g. Groceries" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date</label>
                                <input type="date" v-model="transactionForm.date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <button type="submit" :disabled="transactionForm.processing" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50">
                                {{ transactionForm.processing ? 'Adding...' : 'Add Transaction' }}
                            </button>
                        </form>
                    </div>

                    <!-- Transactions List -->
                    <div class="bg-white shadow rounded-lg p-6 lg:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Transactions</h3>

                        <div v-if="props.recent_transactions.length === 0" class="text-center py-8">
                            <p class="text-sm text-gray-500">No transactions recorded yet. Start managing your expenses!</p>
                        </div>

                        <ul v-else class="divide-y divide-gray-200">
                            <li v-for="transaction in props.recent_transactions" :key="transaction.id" class="py-4 flex justify-between items-center">
                                <div class="flex items-center gap-4">
                                    <div :class="transaction.type === 'income' ? 'bg-green-100' : 'bg-red-100'" class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center">
                                        <svg v-if="transaction.type === 'income'" class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                        <svg v-else class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ transaction.description }}</p>
                                        <p class="text-xs text-gray-500">{{ transaction.date }} • Added by {{ transaction.user?.name }}</p>
                                    </div>
                                </div>
                                <div>
                                    <span :class="transaction.type === 'income' ? 'text-green-600' : 'text-red-600'" class="text-sm font-bold">
                                        {{ transaction.type === 'income' ? '+' : '-' }}${{ transaction.amount }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
