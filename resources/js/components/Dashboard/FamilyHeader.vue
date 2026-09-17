<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { Check, Copy, Home, LogOut, Settings } from "@lucide/vue";
import { ref } from "vue";
import { home, logout } from "@/routes";
import type { Family } from "./types";

type Props = {
    family: Family;
    settingsOpen: boolean;
};

const emit = defineEmits<{
    (event: "toggle-settings"): void;
}>();

const props = defineProps<Props>();
const inviteCodeCopied = ref(false);

const copyInviteCode = async (): Promise<void> => {
    await navigator.clipboard.writeText(props.family.invite_code);
    inviteCodeCopied.value = true;

    window.setTimeout(() => {
        inviteCodeCopied.value = false;
    }, 2000);
};
</script>

<template>
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                {{ family.name }}
            </p>
            <h2 class="mt-1 text-2xl font-semibold leading-tight text-stone-900 dark:text-stone-100">
                Dashboard
            </h2>
            <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                A quick view of your family's finances.
            </p>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-stone-300">
                Invite code:
                <span class="ml-1 font-mono font-semibold tracking-wider text-emerald-700 dark:text-emerald-300">
                    {{ family.invite_code }}
                </span>
                <button
                    type="button"
                    class="inline-flex h-7 items-center gap-1.5 rounded-md border border-stone-300 px-2 text-xs font-medium text-stone-600 transition hover:bg-stone-100 hover:text-stone-900 dark:border-stone-700 dark:text-stone-300 dark:hover:bg-stone-800 dark:hover:text-white"
                    :aria-label="inviteCodeCopied ? 'Invite code copied' : 'Copy invite code'"
                    @click="copyInviteCode"
                >
                    <Check v-if="inviteCodeCopied" class="h-3.5 w-3.5 text-emerald-600" />
                    <Copy v-else class="h-3.5 w-3.5" />
                    {{ inviteCodeCopied ? "Copied" : "Copy" }}
                </button>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-md border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 dark:border-stone-700 dark:text-stone-200 dark:hover:bg-stone-800"
                :aria-expanded="settingsOpen"
                @click="emit('toggle-settings')"
            >
                <Settings class="h-4 w-4" />
                Family settings
            </button>
            <Link
                :href="home()"
                class="inline-flex items-center gap-2 rounded-md border border-stone-300 px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 dark:border-stone-700 dark:text-stone-200 dark:hover:bg-stone-800"
            >
                <Home class="h-4 w-4" />
                Home
            </Link>
            <Link
                :href="logout()"
                method="post"
                as="button"
                class="inline-flex items-center gap-2 rounded-md border border-rose-200 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40"
                data-test="dashboard-logout-button"
            >
                <LogOut class="h-4 w-4" />
                Log out
            </Link>
        </div>
    </div>
</template>
