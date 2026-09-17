<script setup lang="ts">
import { ArrowLeft } from "@lucide/vue";
import { Head, Link } from "@inertiajs/vue3";
import { index as dashboardIndex } from "@/actions/App/Http/Controllers/DashboardController";
import type { Transaction } from "@/components/Dashboard/types";

type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type TransactionPage = {
    data: Transaction[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
};

type Props = {
    transactions: TransactionPage;
};

const props = defineProps<Props>();

const formatDate = (date: string): string =>
    new Intl.DateTimeFormat("en-US", {
        dateStyle: "medium",
    }).format(new Date(date));

const formatCurrency = (amount: number | string): string =>
    new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(Number(amount));
</script>

<template>
    <Head title="Transactions" />

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1
                        class="text-2xl font-semibold text-gray-900 dark:text-white"
                    >
                        Transactions
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Review your family's financial activity.
                    </p>
                </div>
                <Link
                    :href="dashboardIndex()"
                    aria-label="Return to dashboard"
                    class="inline-flex shrink-0 items-center gap-2 rounded-md border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    <ArrowLeft class="h-4 w-4" />
                    <span>Back</span>
                </Link>
            </div>

            <section
                class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-900/5 dark:bg-gray-800 dark:ring-white/10"
            >
                <div
                    v-if="props.transactions.data.length"
                    class="overflow-x-auto"
                >
                    <table
                        class="w-full text-left text-sm text-gray-600 dark:text-gray-300"
                    >
                        <thead
                            class="border-b border-gray-100 text-xs tracking-wider text-gray-400 uppercase dark:border-gray-700 dark:text-gray-500"
                        >
                            <tr>
                                <th class="px-6 py-4 font-semibold">
                                    Description
                                </th>
                                <th class="px-6 py-4 font-semibold">
                                    Category
                                </th>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 text-right font-semibold">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody
                            class="divide-y divide-gray-100 dark:divide-gray-700/60"
                        >
                            <tr
                                v-for="transaction in props.transactions.data"
                                :key="transaction.id"
                                class="transition hover:bg-gray-50/50 dark:hover:bg-gray-700/30"
                            >
                                <td
                                    class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                                >
                                    {{
                                        transaction.description ||
                                        "No description"
                                    }}
                                </td>
                                <td class="px-6 py-4">
                                    {{
                                        transaction.category?.name ??
                                        "Uncategorized"
                                    }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">
                                    {{ formatDate(transaction.created_at) }}
                                </td>
                                <td
                                    class="px-6 py-4 text-right font-bold"
                                    :class="
                                        transaction.type === 'income'
                                            ? 'text-emerald-600 dark:text-emerald-400'
                                            : 'text-rose-600 dark:text-rose-400'
                                    "
                                >
                                    {{
                                        transaction.type === "income"
                                            ? "+"
                                            : "-"
                                    }}{{ formatCurrency(transaction.amount) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-else class="px-6 py-16 text-center">
                    <h2
                        class="text-sm font-semibold text-gray-900 dark:text-white"
                    >
                        No transactions recorded yet
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Add a transaction from the dashboard to see it here.
                    </p>
                </div>

                <nav
                    v-if="props.transactions.last_page > 1"
                    class="flex items-center justify-between border-t border-gray-100 px-6 py-4 dark:border-gray-700"
                    aria-label="Pagination"
                >
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ props.transactions.from }} to
                        {{ props.transactions.to }} of
                        {{ props.transactions.total }}
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in props.transactions.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            :class="[
                                'rounded-md px-3 py-2 text-sm',
                                link.active
                                    ? 'bg-emerald-600 text-white'
                                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700',
                                !link.url && 'pointer-events-none opacity-40',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </nav>
            </section>
        </div>
    </div>
</template>
