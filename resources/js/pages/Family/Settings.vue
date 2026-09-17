<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    family: Object,
    members: Array,
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

const regenerateForm = useForm({});
const removeForm = useForm({});

const regenerateCode = () => {
    if (
        confirm(
            'Are you sure you want to generate a new invite code? The old one will no longer work.',
        )
    ) {
        regenerateForm.post('/family/regenerate-code', {
            preserveScroll: true,
        });
    }
};

const removeMember = (member) => {
    if (
        confirm(
            `Are you sure you want to remove ${member.name} from the family?`,
        )
    ) {
        removeForm.delete(`/family/member/${member.id}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Family Settings" />

    <div class="min-h-screen bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-4xl space-y-8">
            <!-- Header -->
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Family Settings
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Manage your family details and members.
                </p>
            </div>

            <!-- Family Details & Invite Code -->
            <div
                class="overflow-hidden border border-gray-200 bg-white shadow sm:rounded-lg"
            >
                <div
                    class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-5 sm:px-6"
                >
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Family Information
                    </h3>
                </div>
                <div class="space-y-4 px-4 py-5 sm:p-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Family Name</label
                        >
                        <div class="mt-1 text-lg font-semibold text-gray-900">
                            {{ family.name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Invite Code</label
                        >
                        <div class="mt-1 flex items-center gap-4">
                            <span
                                class="rounded border border-indigo-200 bg-indigo-50 px-4 py-2 font-mono font-bold tracking-widest text-indigo-700"
                            >
                                {{ family.invite_code }}
                            </span>
                            <button
                                @click="regenerateCode"
                                :disabled="regenerateForm.processing"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm leading-4 font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                            >
                                {{
                                    regenerateForm.processing
                                        ? 'Generating...'
                                        : 'Regenerate Code'
                                }}
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Share this code with your family members to let them
                            join.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div
                class="overflow-hidden border border-gray-200 bg-white shadow sm:rounded-lg"
            >
                <div
                    class="border-b border-gray-200 bg-gray-50 px-4 py-5 sm:px-6"
                >
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Family Members
                    </h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    <li
                        v-for="member in members"
                        :key="member.id"
                        class="flex items-center justify-between px-4 py-4 hover:bg-gray-50 sm:px-6"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100"
                            >
                                <span
                                    class="text-sm font-bold text-indigo-700"
                                    >{{
                                        member.name.charAt(0).toUpperCase()
                                    }}</span
                                >
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ member.name }}
                                    <span
                                        v-if="member.id === currentUser.id"
                                        class="text-xs font-normal text-gray-500"
                                        >(You)</span
                                    >
                                </p>
                                <p class="text-sm text-gray-500">
                                    {{ member.email }}
                                </p>
                            </div>
                        </div>

                        <div v-if="member.id !== currentUser.id">
                            <button
                                @click="removeMember(member)"
                                :disabled="removeForm.processing"
                                class="inline-flex items-center rounded border border-transparent bg-red-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none disabled:opacity-50"
                            >
                                Remove
                            </button>
                        </div>
                        <div v-else>
                            <span
                                class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                            >
                                Family Head
                            </span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
