<script setup lang="ts">
import { WalletCards } from "@lucide/vue";
import { Link } from "@inertiajs/vue3";
import { index as transactionsIndex } from "@/actions/App/Http/Controllers/TransactionController";
import type { Transaction } from "./types";

type Props = {
    transactions: Transaction[];
    isFamilyHead: boolean;
};

const props = defineProps<Props>();

const formatDate = (date: string): string =>
    new Intl.DateTimeFormat("en-US", { month: "short", day: "numeric" }).format(
        new Date(date),
    );

const formatCurrency = (amount: number): string =>
    new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(amount);
</script>

<template>
    <section
        class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10"
    >
        <div class="p-6 sm:p-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h3
                        class="text-lg font-semibold text-stone-900 dark:text-white"
                    >
                        Recent transactions
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Your latest household activity.
                    </p>
                </div>
                <Link
                    v-if="props.isFamilyHead"
                    :href="transactionsIndex()"
                    class="text-sm font-medium text-emerald-700 transition hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300"
                    >View all</Link
                >
            </div>
            <div v-if="props.transactions.length > 0" class="overflow-x-auto">
                <table
                    class="w-full text-left text-sm text-gray-600 dark:text-gray-300"
                >
                    <thead
                        class="border-b border-gray-100 text-xs tracking-wider text-gray-400 uppercase dark:border-gray-700 dark:text-gray-500"
                    >
                        <tr>
                            <th class="pb-3 font-semibold">Description</th>
                            <th class="pb-3 font-semibold">Category</th>
                            <th class="pb-3 font-semibold">Date</th>
                            <th class="pb-3 text-right font-semibold">
                                Amount
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-gray-100 dark:divide-gray-700/60"
                    >
                        <tr
                            v-for="transaction in props.transactions"
                            :key="transaction.id"
                            class="transition hover:bg-gray-50/50 dark:hover:bg-gray-700/30"
                        >
                            <td
                                class="py-4 font-medium text-gray-900 dark:text-white"
                            >
                                {{
                                    transaction.description || "No description"
                                }}
                            </td>
                            <td class="py-4">
                                <span
                                    class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    >{{
                                        transaction.category?.name ??
                                        "Uncategorized"
                                    }}</span
                                >
                            </td>
                            <td class="py-4 text-xs text-gray-400">
                                {{ formatDate(transaction.created_at) }}
                            </td>
                            <td
                                class="py-4 text-right font-bold"
                                :class="
                                    transaction.type === 'income'
                                        ? 'text-emerald-600 dark:text-emerald-400'
                                        : 'text-rose-600 dark:text-rose-400'
                                "
                            >
                                {{ transaction.type === "income" ? "+" : "-"
                                }}{{ formatCurrency(transaction.amount) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else
                class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-200 px-4 py-12 text-center dark:border-gray-700"
            >
                <div
                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400"
                >
                    <WalletCards class="h-5 w-5" />
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                    No transactions recorded yet
                </h4>
                <p
                    class="mt-1 max-w-sm text-xs text-gray-500 dark:text-gray-400"
                >
                    Start keeping your family budget green and structured by
                    adding your first transaction.
                </p>
            </div>
        </div>
    </section>
</template>
