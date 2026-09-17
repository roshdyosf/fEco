<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import { store } from "@/actions/App/Http/Controllers/TransactionController";
import type { Category } from "./types";

type Props = {
    categories: Category[];
};

const props = defineProps<Props>();
const transactionForm = useForm<{
    type: "expense" | "income";
    category_id: number | null;
    amount: number | null;
    description: string;
}>({
    type: "expense",
    category_id: null,
    amount: null,
    description: "",
});

const availableCategories = computed(() =>
    props.categories.filter((category) => category.type === transactionForm.type),
);

watch(
    () => transactionForm.type,
    () => {
        transactionForm.category_id = null;
    },
);

const submitTransaction = (): void => {
    transactionForm.post(store.url(), {
        preserveScroll: true,
        onSuccess: () => transactionForm.reset("amount", "description", "category_id"),
    });
};
</script>

<template>
    <section class="mb-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-8">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-stone-900 dark:text-stone-100">Add a transaction</h3>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">Record money coming in or going out of the family balance.</p>
        </div>
        <form class="grid gap-5 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-start" @submit.prevent="submitTransaction">
            <div class="space-y-2">
                <label class="text-sm font-medium text-stone-700 dark:text-stone-200">Transaction type</label>
                <div class="grid grid-cols-2 rounded-lg bg-stone-100 p-1 dark:bg-stone-800">
                    <button type="button" class="rounded-md px-3 py-2 text-sm font-medium transition" :class="transactionForm.type === 'expense' ? 'bg-white text-rose-700 shadow-sm dark:bg-stone-700 dark:text-rose-300' : 'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'" @click="transactionForm.type = 'expense'">Expense</button>
                    <button type="button" class="rounded-md px-3 py-2 text-sm font-medium transition" :class="transactionForm.type === 'income' ? 'bg-white text-emerald-700 shadow-sm dark:bg-stone-700 dark:text-emerald-300' : 'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'" @click="transactionForm.type = 'income'">Income</button>
                </div>
                <p v-if="transactionForm.errors.type" class="text-xs text-rose-600">{{ transactionForm.errors.type }}</p>
            </div>
            <div class="space-y-2">
                <label for="category" class="text-sm font-medium text-stone-700 dark:text-stone-200">Category</label>
                <select id="category" v-model="transactionForm.category_id" class="h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100" required>
                    <option :value="null" disabled>Select a category</option>
                    <option v-for="category in availableCategories" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
                <p v-if="transactionForm.errors.category_id" class="text-xs text-rose-600">{{ transactionForm.errors.category_id }}</p>
                <p v-if="availableCategories.length === 0" class="text-xs text-amber-600">No {{ transactionForm.type }} categories are available yet.</p>
            </div>
            <div class="space-y-2">
                <label for="amount" class="text-sm font-medium text-stone-700 dark:text-stone-200">Amount</label>
                <input id="amount" v-model.number="transactionForm.amount" type="number" min="0.01" step="0.01" placeholder="0.00" class="h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100" required />
                <p v-if="transactionForm.errors.amount" class="text-xs text-rose-600">{{ transactionForm.errors.amount }}</p>
            </div>
            <button type="submit" :disabled="transactionForm.processing || availableCategories.length === 0" class="h-10 rounded-md bg-emerald-700 px-5 text-sm font-medium text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50 lg:mt-7 dark:bg-emerald-600 dark:hover:bg-emerald-500">{{ transactionForm.processing ? "Saving..." : "Save transaction" }}</button>
            <div class="lg:col-span-4">
                <label for="description" class="text-sm font-medium text-stone-700 dark:text-stone-200">Note <span class="font-normal text-stone-400">(optional)</span></label>
                <input id="description" v-model="transactionForm.description" type="text" maxlength="500" placeholder="What was this for?" class="mt-2 h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100" />
                <p v-if="transactionForm.errors.description" class="mt-1 text-xs text-rose-600">{{ transactionForm.errors.description }}</p>
            </div>
        </form>
    </section>
</template>
