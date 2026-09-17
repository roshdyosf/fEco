<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { ArrowRight, LogOut, PiggyBank, ShieldCheck, Users } from "@lucide/vue";
import { dashboard, login, logout, register } from "@/routes";
</script>

<template>
    <Head title="Welcome | Family Eco" />

    <div
        class="min-h-screen bg-stone-50 text-stone-900 dark:bg-stone-950 dark:text-stone-100"
    >
        <header class="border-b border-stone-200/80 bg-stone-50/90 dark:border-stone-800 dark:bg-stone-950/90">
            <nav class="mx-auto flex w-full max-w-6xl items-center justify-between px-5 py-5 lg:px-8">
                <div
                    class="flex items-center gap-2 text-sm font-semibold tracking-wide text-stone-900 dark:text-stone-100"
                >
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-700 text-white">
                        <PiggyBank class="h-5 w-5" />
                    </span>
                    Family Eco
                </div>
                <div class="flex items-center gap-1 sm:gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="dashboard()"
                            class="rounded-md px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-200/70 hover:text-stone-950 dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white"
                        >
                            Dashboard
                        </Link>
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-200/70 hover:text-rose-700 dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-rose-300"
                        >
                            <LogOut class="h-4 w-4" />
                            Log Out
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-md px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-stone-200/70 hover:text-stone-950 dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                        >
                            Create account
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <div class="mx-auto flex w-full max-w-6xl items-center px-5 py-12 lg:min-h-[calc(100vh-82px)] lg:px-8 lg:py-16">
            <main
                class="grid w-full overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-[0_20px_60px_-35px_rgba(28,25,23,0.35)] lg:grid-cols-[1.1fr_0.9fr] dark:border-stone-800 dark:bg-stone-900"
            >
                <div
                    class="flex flex-col justify-center p-7 sm:p-10 lg:p-16"
                >
                    <span
                        class="mb-5 inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300"
                    >
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-600" />
                        Shared finances, made simple
                    </span>
                    <h1
                        class="max-w-xl text-4xl font-semibold tracking-tight text-stone-950 sm:text-5xl dark:text-white"
                    >
                        A clearer view of your family's money.
                    </h1>
                    <p
                        class="mt-5 max-w-lg text-base leading-7 text-stone-600 dark:text-stone-300"
                    >
                        Keep income, expenses, and everyday decisions together in
                        one calm space built for the people who share them.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <template v-if="$page.props.auth.user">
                            <Link
                                :href="dashboard()"
                                class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                            >
                                Open dashboard
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="register()"
                                class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                            >
                                Get started
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                            <Link
                                :href="login()"
                                class="inline-flex items-center justify-center rounded-md border border-stone-300 px-5 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-200 dark:hover:bg-stone-800"
                            >
                                Sign in
                            </Link>
                        </template>
                    </div>
                </div>

                <div
                    class="relative flex min-h-[280px] flex-col justify-between bg-emerald-800 p-8 text-white sm:p-10 lg:min-h-full"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-emerald-100">Family Eco</span>
                        <ShieldCheck class="h-5 w-5 text-emerald-200" />
                    </div>
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm text-emerald-100">Your household snapshot</p>
                            <p class="mt-2 text-3xl font-semibold tracking-tight">Simple, shared, visible.</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg border border-white/15 bg-white/10 p-4">
                                <Users class="h-5 w-5 text-emerald-200" />
                                <p class="mt-5 text-sm font-medium">One family view</p>
                            </div>
                            <div class="rounded-lg border border-white/15 bg-white/10 p-4">
                                <PiggyBank class="h-5 w-5 text-emerald-200" />
                                <p class="mt-5 text-sm font-medium">Everyday clarity</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
