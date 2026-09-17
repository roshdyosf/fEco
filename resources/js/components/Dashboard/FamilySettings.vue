<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { Trash2, UserMinus, UserRound } from "@lucide/vue";
import {
    destroy as destroyFamily,
    leave as leaveFamilyRoute,
    removeMember as removeFamilyMember,
} from "@/actions/App/Http/Controllers/FamilyController";
import type { FamilyMember } from "./types";

type Props = {
    isFamilyHead: boolean;
    currentUserId: number;
    members: FamilyMember[];
};

const props = defineProps<Props>();
const familyActionForm = useForm({});
const memberDeleteForm = useForm({});

const leaveFamily = (): void => {
    if (!window.confirm("Are you sure you want to leave this family?")) {
        return;
    }

    familyActionForm.post(leaveFamilyRoute.url());
};

const deleteFamily = (): void => {
    if (!window.confirm("Delete this family permanently? All members, categories, and transactions will be removed.")) {
        return;
    }

    familyActionForm.delete(destroyFamily.url());
};

const removeMember = (member: FamilyMember): void => {
    if (!window.confirm(`Remove ${member.name} from this family?`)) {
        return;
    }

    memberDeleteForm.delete(removeFamilyMember.url(member.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section class="mb-8 rounded-2xl border border-stone-200 bg-white p-5 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-semibold text-stone-900 dark:text-stone-100">
                    {{ isFamilyHead ? "Family administration" : "Your family membership" }}
                </h3>
                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                    {{ isFamilyHead ? "Deleting the family permanently removes its shared data." : "Leaving removes you from this family and returns you to setup." }}
                </p>
            </div>
            <button
                v-if="isFamilyHead"
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-md border border-rose-200 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50 disabled:opacity-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40"
                :disabled="familyActionForm.processing"
                @click="deleteFamily"
            >
                <Trash2 class="h-4 w-4" />
                Delete family
            </button>
            <button
                v-else
                type="button"
                class="inline-flex items-center justify-center gap-2 rounded-md border border-rose-200 px-3 py-2 text-sm font-medium text-rose-700 transition hover:bg-rose-50 disabled:opacity-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40"
                :disabled="familyActionForm.processing"
                @click="leaveFamily"
            >
                <UserMinus class="h-4 w-4" />
                Leave family
            </button>
        </div>

        <div class="mt-6 border-t border-stone-200 pt-5 dark:border-stone-800">
            <div class="mb-3 flex items-center justify-between gap-3">
                <h4 class="text-sm font-semibold text-stone-900 dark:text-stone-100">
                    Family members
                </h4>
                <span class="text-xs text-stone-500 dark:text-stone-400">
                    {{ members.length }} member{{ members.length === 1 ? "" : "s" }}
                </span>
            </div>
            <div class="divide-y divide-stone-200 rounded-lg border border-stone-200 dark:divide-stone-800 dark:border-stone-800">
                <div v-for="member in members" :key="member.id" class="flex items-center justify-between gap-3 px-3 py-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-stone-100 text-stone-600 dark:bg-stone-800 dark:text-stone-300">
                            <UserRound class="h-4 w-4" />
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-stone-900 dark:text-stone-100">
                                {{ member.name }}
                                <span v-if="member.id === currentUserId" class="ml-1 text-xs font-normal text-stone-400">(You)</span>
                            </p>
                            <p class="truncate text-xs text-stone-500 dark:text-stone-400">{{ member.email }}</p>
                        </div>
                    </div>
                    <button
                        v-if="isFamilyHead && member.id !== currentUserId"
                        type="button"
                        class="inline-flex shrink-0 items-center gap-1.5 rounded-md px-2.5 py-1.5 text-xs font-medium text-rose-700 transition hover:bg-rose-50 disabled:opacity-50 dark:text-rose-300 dark:hover:bg-rose-950/40"
                        :disabled="memberDeleteForm.processing"
                        @click="removeMember(member)"
                    >
                        <UserMinus class="h-3.5 w-3.5" />
                        Remove
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
