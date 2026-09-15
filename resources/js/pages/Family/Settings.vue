<script setup>
import { Head, useForm, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

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
            "Are you sure you want to generate a new invite code? The old one will no longer work.",
        )
    ) {
        regenerateForm.post("/family/regenerate-code", {
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

    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-8">
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
                class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200"
            >
                <div
                    class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50 flex justify-between items-center"
                >
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Family Information
                    </h3>
                </div>
                <div class="px-4 py-5 sm:p-6 space-y-4">
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
                                class="px-4 py-2 bg-indigo-50 text-indigo-700 font-mono font-bold rounded border border-indigo-200 tracking-widest"
                            >
                                {{ family.invite_code }}
                            </span>
                            <button
                                @click="regenerateCode"
                                :disabled="regenerateForm.processing"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                            >
                                {{
                                    regenerateForm.processing
                                        ? "Generating..."
                                        : "Regenerate Code"
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
                class="bg-white shadow overflow-hidden sm:rounded-lg border border-gray-200"
            >
                <div
                    class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50"
                >
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Family Members
                    </h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    <li
                        v-for="member in members"
                        :key="member.id"
                        class="px-4 py-4 sm:px-6 flex items-center justify-between hover:bg-gray-50"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center"
                            >
                                <span
                                    class="text-indigo-700 font-bold text-sm"
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
                                        class="text-xs text-gray-500 font-normal"
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
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                            >
                                Remove
                            </button>
                        </div>
                        <div v-else>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
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
