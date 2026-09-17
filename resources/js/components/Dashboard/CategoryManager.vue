<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Trash2 } from '@lucide/vue';
import {
    destroy as destroyCategory,
    store as storeCategory,
} from '@/actions/App/Http/Controllers/CategoryController';
import type { Category } from './types';

type Props = {
    categories: Category[];
};

const props = defineProps<Props>();
const categoryForm = useForm<{ name: string; type: 'expense' | 'income' }>({
    name: '',
    type: 'expense',
});
const categoryDeleteForm = useForm({});

const submitCategory = (): void => {
    categoryForm.post(storeCategory.url(), {
        preserveScroll: true,
        onSuccess: () => categoryForm.reset('name'),
    });
};

const deleteCategory = (category: Category): void => {
    if (!window.confirm(`Delete the ${category.name} category?`)) {
        return;
    }

    categoryDeleteForm.delete(destroyCategory.url(category.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section
        class="mb-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8 dark:border-stone-800 dark:bg-stone-900"
    >
        <div class="mb-6">
            <h3
                class="text-lg font-semibold text-stone-900 dark:text-stone-100"
            >
                Family categories
            </h3>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                Add categories for your family and remove unused ones.
            </p>
        </div>
        <form
            class="grid gap-4 sm:grid-cols-[1fr_180px_auto] sm:items-start"
            @submit.prevent="submitCategory"
        >
            <div>
                <label
                    for="category-name"
                    class="text-sm font-medium text-stone-700 dark:text-stone-200"
                    >Category name</label
                >
                <input
                    id="category-name"
                    v-model="categoryForm.name"
                    type="text"
                    maxlength="100"
                    placeholder="e.g. Groceries"
                    class="mt-2 h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 transition outline-none placeholder:text-stone-400 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100"
                    required
                />
                <p
                    v-if="categoryForm.errors.name"
                    class="mt-1 text-xs text-rose-600"
                >
                    {{ categoryForm.errors.name }}
                </p>
            </div>
            <div>
                <label
                    for="category-type"
                    class="text-sm font-medium text-stone-700 dark:text-stone-200"
                    >Used for</label
                >
                <select
                    id="category-type"
                    v-model="categoryForm.type"
                    class="mt-2 h-10 w-full rounded-md border border-stone-300 bg-white px-3 text-sm text-stone-900 transition outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 dark:border-stone-700 dark:bg-stone-950 dark:text-stone-100"
                >
                    <option value="expense">Expenses</option>
                    <option value="income">Income</option>
                </select>
            </div>
            <button
                type="submit"
                :disabled="categoryForm.processing"
                class="h-10 rounded-md bg-stone-900 px-5 text-sm font-medium text-white transition hover:bg-stone-700 disabled:cursor-not-allowed disabled:opacity-50 sm:mt-7 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white"
            >
                {{ categoryForm.processing ? 'Adding...' : 'Add category' }}
            </button>
        </form>
        <div
            v-if="props.categories.length > 0"
            class="mt-6 flex flex-wrap gap-2 border-t border-stone-200 pt-5 dark:border-stone-800"
        >
            <div
                v-for="category in props.categories"
                :key="category.id"
                class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-stone-50 py-1 pr-1 pl-3 text-sm text-stone-700 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-200"
            >
                <span>{{ category.name }}</span>
                <span class="text-xs text-stone-400">{{
                    category.type === 'income' ? 'Income' : 'Expense'
                }}</span>
                <button
                    type="button"
                    class="flex h-6 w-6 items-center justify-center rounded-full text-stone-400 transition hover:bg-rose-100 hover:text-rose-700 dark:hover:bg-rose-950/50 dark:hover:text-rose-300"
                    :aria-label="`Delete ${category.name} category`"
                    :disabled="categoryDeleteForm.processing"
                    @click="deleteCategory(category)"
                >
                    <Trash2 class="h-3.5 w-3.5" />
                </button>
            </div>
        </div>
        <p
            v-else
            class="mt-5 border-t border-stone-200 pt-5 text-sm text-stone-500 dark:border-stone-800 dark:text-stone-400"
        >
            Add your first category to start recording transactions.
        </p>
    </section>
</template>
