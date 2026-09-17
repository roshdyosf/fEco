<script setup lang="ts">
import { ArrowDownRight, ArrowUpRight, WalletCards } from "@lucide/vue";
import type { Family } from "./types";

type Props = {
    family: Family;
    monthlyIncome: number;
    monthlyExpenses: number;
};

const props = defineProps<Props>();

const formatCurrency = (amount: number): string =>
    new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(amount);
</script>

<template>
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10">
            <div class="flex items-center justify-between"><span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Balance</span><WalletCards class="h-5 w-5 text-emerald-600 dark:text-emerald-400" /></div>
            <p class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white">{{ formatCurrency(props.family.total_balance) }}</p>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10">
            <div class="flex items-center justify-between"><span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Monthly Income</span><ArrowUpRight class="h-5 w-5 text-sky-600 dark:text-sky-400" /></div>
            <p class="mt-4 text-3xl font-extrabold tracking-tight text-blue-600 dark:text-blue-400">+{{ formatCurrency(props.monthlyIncome) }}</p>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10">
            <div class="flex items-center justify-between"><span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Monthly Expenses</span><ArrowDownRight class="h-5 w-5 text-rose-600 dark:text-rose-400" /></div>
            <p class="mt-4 text-3xl font-extrabold tracking-tight text-rose-600 dark:text-rose-400">-{{ formatCurrency(props.monthlyExpenses) }}</p>
        </div>
    </div>
</template>
