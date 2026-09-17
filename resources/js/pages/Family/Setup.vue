<script setup lang="ts">
import { Head, useForm } from "@inertiajs/vue3";
import { Clipboard, Check, Plus, Users } from "@lucide/vue";
import { ref } from "vue";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    join as joinFamily,
    store as createFamily,
} from "@/actions/App/Http/Controllers/FamilyController";

const createForm = useForm({ name: "" });
const joinForm = useForm({ invite_code: "" });
const pasted = ref(false);

const submitCreate = () => {
    createForm.post(createFamily.url());
};

const submitJoin = () => {
    joinForm.post(joinFamily.url());
};

const pasteInviteCode = async () => {
    let code = "";

    try {
        code = await navigator.clipboard.readText();
    } catch {
        return;
    }

    if (!code.trim()) {
        return;
    }

    joinForm.invite_code = code.trim().toUpperCase();
    pasted.value = true;
    window.setTimeout(() => {
        pasted.value = false;
    }, 2000);
};
</script>

<template>
    <Head title="Family Setup" />

    <div
        class="min-h-screen bg-stone-50 px-5 py-12 text-stone-900 dark:bg-stone-950 dark:text-stone-100 sm:px-8 lg:py-20"
    >
        <div class="mx-auto max-w-5xl">
            <div class="mb-10 max-w-xl">
                <div
                    class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-700 text-white"
                >
                    <Users class="h-5 w-5" />
                </div>
                <p
                    class="text-sm font-medium text-emerald-700 dark:text-emerald-400"
                >
                    Family Eco
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    Set up your family space
                </h1>
                <p
                    class="mt-3 text-base leading-7 text-stone-600 dark:text-stone-300"
                >
                    Create a new family or use an invite code to join one that
                    already exists.
                </p>
            </div>

            <div class="grid gap-5 lg:grid-cols-2">
                <section
                    class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-8"
                >
                    <div class="mb-8 flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400"
                            >
                                Start fresh
                            </p>
                            <h2 class="mt-2 text-xl font-semibold">
                                Create a family
                            </h2>
                            <p
                                class="mt-2 text-sm leading-6 text-stone-500 dark:text-stone-400"
                            >
                                Become the family head and invite others with
                                your new code.
                            </p>
                        </div>
                        <Plus class="h-5 w-5 text-emerald-600" />
                    </div>
                    <form class="space-y-5" @submit.prevent="submitCreate">
                        <div>
                            <Label for="name">Family name</Label>
                            <Input
                                id="name"
                                v-model="createForm.name"
                                type="text"
                                required
                                placeholder="e.g. Rashed Family"
                                class="mt-2"
                            />
                            <InputError
                                :message="createForm.errors.name"
                                class="mt-2"
                            />
                        </div>
                        <Button
                            type="submit"
                            class="w-full bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500"
                            :disabled="createForm.processing"
                        >
                            {{
                                createForm.processing
                                    ? "Creating..."
                                    : "Create family"
                            }}
                        </Button>
                    </form>
                </section>

                <section
                    class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm dark:border-stone-800 dark:bg-stone-900 sm:p-8"
                >
                    <div class="mb-8 flex items-start justify-between gap-4">
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-wider text-sky-700 dark:text-sky-400"
                            >
                                Have an invite?
                            </p>
                            <h2 class="mt-2 text-xl font-semibold">
                                Join a family
                            </h2>
                            <p
                                class="mt-2 text-sm leading-6 text-stone-500 dark:text-stone-400"
                            >
                                Enter the code shared by your family head.
                            </p>
                        </div>
                        <Users class="h-5 w-5 text-sky-600" />
                    </div>
                    <form class="space-y-5" @submit.prevent="submitJoin">
                        <div>
                            <Label for="invite_code">Invite code</Label>
                            <Input
                                id="invite_code"
                                v-model="joinForm.invite_code"
                                type="text"
                                required
                                placeholder="e.g. ABCD1234"
                                class="mt-2 font-mono uppercase tracking-wider"
                            />
                            <InputError
                                :message="joinForm.errors.invite_code"
                                class="mt-2"
                            />
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <Button
                                type="submit"
                                class="flex-1 bg-stone-900 hover:bg-stone-700 dark:bg-stone-100 dark:text-stone-900 dark:hover:bg-white"
                                :disabled="joinForm.processing"
                            >
                                {{
                                    joinForm.processing
                                        ? "Joining..."
                                        : "Join family"
                                }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                class="gap-2 sm:px-4"
                                @click="pasteInviteCode"
                            >
                                <Check
                                    v-if="pasted"
                                    class="h-4 w-4 text-emerald-600"
                                />
                                <Clipboard v-else class="h-4 w-4" />
                                {{ pasted ? "Pasted" : "Paste code" }}
                            </Button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</template>
