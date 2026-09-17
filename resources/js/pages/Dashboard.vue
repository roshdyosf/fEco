<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import { ArrowDownRight, ArrowUpRight, WalletCards } from "@lucide/vue";
import AppLayout from "@/layouts/AppLayout.vue";
import { store } from "@/actions/App/Http/Controllers/TransactionController";

type Transaction = {
    id: number;
    title: string;
    amount: number;
    type: "income" | "expense";
    category?: { name: string } | null;
    created_at: string;
};

type Category = {
    id: number;
    name: string;
    type: "income" | "expense";
};

const props = defineProps<{
    family: {
        name: string;
        total_balance: number;
    };
    categories: Category[];
    monthly_income: number;
    monthly_expenses: number;
    recent_transactions: Transaction[];
}>();

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
    props.categories.filter(
        (category) => category.type === transactionForm.type,
    ),
);

watch(
    () => transactionForm.type,
    () => {
        transactionForm.category_id = null;
    },
);

const submitTransaction = () => {
    transactionForm.post(store.url(), {
        preserveScroll: true,
        onSuccess: () =>
            transactionForm.reset("amount", "description", "category_id"),
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Intl.DateTimeFormat("en-US", {
        month: "short",
        day: "numeric",
    }).format(new Date(date));
};
</script>

<template>
    <Head title="Dashboard | Family Eco" />

    <AppLayout>
        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <p
                        class="text-sm font-medium text-emerald-600 dark:text-emerald-400"
                    >
                        {{ props.family.name }}
                    </p>
                    <h2
                        class="mt-1 text-2xl font-semibold leading-tight text-stone-900 dark:text-stone-100"
                    >
                        Dashboard
                    </h2>
                    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                        A quick view of your family's finances.
                    </p>
                </div>

                <section
                    class="mb-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-8"
                >
                    <div class="mb-6">
                        <h3
                            class="text-lg font-semibold text-stone-900 dark:text-stone-100"
                        >
                            Add a transaction
                        </h3>
                        <p
                            class="mt-1 text-sm text-stone-500 dark:text-stone-400"
                        >
                            Record money coming in or going out of the family
                            balance.
                        </p>
                    </div>

                    <form
                        class="grid gap-5 lg:grid-cols-[1fr_1fr_1fr_auto] lg:items-start"
                        @submit.prevent="submitTransaction"
                    >
                        <div class="space-y-2">
                            <label
                                class="text-sm font-medium text-stone-700 dark:text-stone-200"
                            >
                                Transaction type
                            </label>
                            <div
                                class="grid grid-cols-2 rounded-lg bg-stone-100 p-1 dark:bg-stone-800"
                            >
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-2 text-sm font-medium transition"
                                    :class="
                                        transactionForm.type === 'expense'
                                            ? 'bg-white text-rose-700 shadow-sm dark:bg-stone-700 dark:text-rose-300'
                                            : 'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'
                                    "
                                    @click="transactionForm.type = 'expense'"
                                >
                                    Expense
                                </button>
                                <button
                                    type="button"
                                    class="rounded-md px-3 py-2 text-sm font-medium transition"
                                    :class="
                                        transactionForm.type === 'income'
                                            ? 'bg-white text-emerald-700 shadow-sm dark:bg-stone-700 dark:text-emerald-300'
                                            : 'text-stone-500 hover:text-stone-900 dark:text-stone-400 dark:hover:text-stone-100'
                                    "
                                    @click="transactionForm.type = 'income'"
                                >
                                    Income
                                </button>
                            </div>
                            <p
                                v-if="transactionForm.errors.type"
                                class="text-xs text-rose-600"
                            >
                                {{ transactionForm.errors.type }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label
                                for="category"
                                class="text-sm font-medium text-stone-700 dark:text-stone-200"
                            >
                                Category
                            </label>
                            <select
                                id="category"
                                v-model="transactionForm.category_id"
                                class="h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 outline-none transition focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100"
                                required
                            >
                                <option :value="null" disabled>
                                    Select a category
                                </option>
                                <option
                                    v-for="category in availableCategories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <p
                                v-if="transactionForm.errors.category_id"
                                class="text-xs text-rose-600"
                            >
                                {{ transactionForm.errors.category_id }}
                            </p>
                            <p
                                v-if="availableCategories.length === 0"
                                class="text-xs text-amber-600"
                            >
                                No {{ transactionForm.type }} categories are
                                available yet.
                            </p>
                        </div>

                        <div class="space-y-2">
                            <label
                                for="amount"
                                class="text-sm font-medium text-stone-700 dark:text-stone-200"
                            >
                                Amount
                            </label>
                            <input
                                id="amount"
                                v-model.number="transactionForm.amount"
                                type="number"
                                min="0.01"
                                step="0.01"
                                placeholder="0.00"
                                class="h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100"
                                required
                            />
                            <p
                                v-if="transactionForm.errors.amount"
                                class="text-xs text-rose-600"
                            >
                                {{ transactionForm.errors.amount }}
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="
                                transactionForm.processing ||
                                availableCategories.length === 0
                            "
                            class="h-10 rounded-md bg-emerald-700 px-5 text-sm font-medium text-white transition hover:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-50 lg:mt-7 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                        >
                            {{
                                transactionForm.processing
                                    ? "Saving..."
                                    : "Save transaction"
                            }}
                        </button>

                        <div class="lg:col-span-4">
                            <label
                                for="description"
                                class="text-sm font-medium text-stone-700 dark:text-stone-200"
                            >
                                Note
                                <span class="font-normal text-stone-400"
                                    >(optional)</span
                                >
                            </label>
                            <input
                                id="description"
                                v-model="transactionForm.description"
                                type="text"
                                maxlength="500"
                                placeholder="What was this for?"
                                class="mt-2 h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100"
                            />
                            <p
                                v-if="transactionForm.errors.description"
                                class="mt-1 text-xs text-rose-600"
                            >
                                {{ transactionForm.errors.description }}
                            </p>
                        </div>
                    </form>
                </section>

                <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div
                        class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                                >Total Balance</span
                            >
                            <WalletCards
                                class="h-5 w-5 text-emerald-600 dark:text-emerald-400"
                            />
                        </div>
                        <p
                            class="mt-4 text-3xl font-extrabold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ formatCurrency(props.family.total_balance) }}
                        </p>
                    </div>

                    <div
                        class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                                >Monthly Income</span
                            >
                            <ArrowUpRight
                                class="h-5 w-5 text-sky-600 dark:text-sky-400"
                            />
                        </div>
                        <p
                            class="mt-4 text-3xl font-extrabold tracking-tight text-blue-600 dark:text-blue-400"
                        >
                            +{{ formatCurrency(props.monthly_income) }}
                        </p>
                    </div>

                    <div
                        class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                                >Monthly Expenses</span
                            >
                            <ArrowDownRight
                                class="h-5 w-5 text-rose-600 dark:text-rose-400"
                            />
                        </div>
                        <p
                            class="mt-4 text-3xl font-extrabold tracking-tight text-rose-600 dark:text-rose-400"
                        >
                            -{{ formatCurrency(props.monthly_expenses) }}
                        </p>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10"
                >
                    <div class="p-6 sm:p-8">
                        <div
                            class="mb-6 flex items-center justify-between gap-4"
                        >
                            <div>
                                <h3
                                    class="text-lg font-semibold text-stone-900 dark:text-white"
                                >
                                    Recent transactions
                                </h3>
                                <p
                                    class="text-xs text-gray-500 dark:text-gray-400"
                                >
                                    Your latest household activity.
                                </p>
                            </div>
                            <Link
                                href="/transactions"
                                class="text-sm font-medium text-emerald-700 transition hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
                            >
                                View all
                            </Link>
                        </div>

                        <div
                            v-if="props.recent_transactions.length > 0"
                            class="overflow-x-auto"
                        >
                            <table
                                class="w-full text-left text-sm text-gray-600 dark:text-gray-300"
                            >
                                <thead
                                    class="border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400 dark:border-gray-700 dark:text-gray-500"
                                >
                                    <tr>
                                        <th class="pb-3 font-semibold">
                                            Title
                                        </th>
                                        <th class="pb-3 font-semibold">
                                            Category
                                        </th>
                                        <th class="pb-3 font-semibold">Date</th>
                                        <th
                                            class="pb-3 font-semibold text-right"
                                        >
                                            Amount
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-gray-100 dark:divide-gray-700/60"
                                >
                                    <tr
                                        v-for="tx in props.recent_transactions"
                                        :key="tx.id"
                                        class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition"
                                    >
                                        <td
                                            class="py-4 font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ tx.title }}
                                        </td>
                                        <td class="py-4">
                                            <span
                                                class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                            >
                                                {{
                                                    tx.category?.name ??
                                                    "Uncategorized"
                                                }}
                                            </span>
                                        </td>
                                        <td class="py-4 text-xs text-gray-400">
                                            {{ formatDate(tx.created_at) }}
                                        </td>
                                        <td
                                            class="py-4 text-right font-bold"
                                            :class="
                                                tx.type === 'income'
                                                    ? 'text-emerald-600 dark:text-emerald-400'
                                                    : 'text-rose-600 dark:text-rose-400'
                                            "
                                        >
                                            {{ tx.type === "income" ? "+" : "-"
                                            }}{{ formatCurrency(tx.amount) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div
                            v-else
                            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 py-12 px-4 text-center dark:border-gray-700"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 mb-3"
                            >
                                <WalletCards class="h-5 w-5" />
                            </div>
                            <h4
                                class="text-sm font-semibold text-gray-900 dark:text-white"
                            >
                                No transactions recorded yet
                            </h4>
                            <p
                                class="mt-1 text-xs text-gray-500 dark:text-gray-400 max-w-sm"
                            >
                                Start keeping your family budget green and
                                structured by adding your first transaction.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
