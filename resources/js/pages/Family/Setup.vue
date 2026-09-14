<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import InputError from "@/components/InputError.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

// فورم الإنشاء
const createForm = useForm({
    name: "",
});

// فورم الانضمام
const joinForm = useForm({
    invite_code: "",
});

const submitCreate = () => {
    createForm.post("/family/create");
};

const submitJoin = () => {
    joinForm.post("/family/join");
};
</script>

<template>
    <Head title="Family Setup" />

    <div
        class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8"
    >
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                Welcome to Family Tracker
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                To continue, please create a new family or join an existing one.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div
                class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 space-y-8"
            >
                <!-- Create Family -->
                <div>
                    <h3
                        class="text-lg font-medium text-gray-900 mb-4 border-b pb-2"
                    >
                        Create a New Family
                    </h3>
                    <form @submit.prevent="submitCreate" class="space-y-4">
                        <div>
                            <Label for="name">Family Name</Label>
                            <Input
                                id="name"
                                type="text"
                                v-model="createForm.name"
                                required
                                placeholder="e.g. Rashed Family"
                                class="mt-1"
                            />
                            <InputError
                                :message="createForm.errors.name"
                                class="mt-2"
                            />
                        </div>
                        <Button
                            type="submit"
                            class="w-full"
                            :disabled="createForm.processing"
                        >
                            Create Family
                        </Button>
                    </form>
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span
                        class="flex-shrink mx-4 text-gray-400 text-sm font-medium uppercase"
                        >Or</span
                    >
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Join Family -->
                <div>
                    <h3
                        class="text-lg font-medium text-gray-900 mb-4 border-b pb-2"
                    >
                        Join Existing Family
                    </h3>
                    <form @submit.prevent="submitJoin" class="space-y-4">
                        <div>
                            <Label for="invite_code">Invite Code</Label>
                            <Input
                                id="invite_code"
                                type="text"
                                v-model="joinForm.invite_code"
                                required
                                placeholder="Enter code here"
                                class="mt-1"
                            />
                            <InputError
                                :message="joinForm.errors.invite_code"
                                class="mt-2"
                            />
                        </div>
                        <Button
                            type="submit"
                            variant="secondary"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900"
                            :disabled="joinForm.processing"
                        >
                            Join Family
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
