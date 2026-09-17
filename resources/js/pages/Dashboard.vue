<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import CategoryManager from '@/components/Dashboard/CategoryManager.vue';
import FamilyHeader from '@/components/Dashboard/FamilyHeader.vue';
import FamilySettings from '@/components/Dashboard/FamilySettings.vue';
import RecentTransactions from '@/components/Dashboard/RecentTransactions.vue';
import StatsOverview from '@/components/Dashboard/StatsOverview.vue';
import TransactionForm from '@/components/Dashboard/TransactionForm.vue';
import type {
    Category,
    Family,
    FamilyMember,
    Transaction,
} from '@/components/Dashboard/types';

type Props = {
    family: Family;
    is_family_head: boolean;
    current_user_id: number;
    members: FamilyMember[];
    categories: Category[];
    monthly_income: number;
    monthly_expenses: number;
    recent_transactions: Transaction[];
};

const props = defineProps<Props>();
const familySettingsOpen = ref(false);
</script>

<template>
    <Head title="Dashboard | Family Eco" />

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <FamilyHeader
                :family="props.family"
                :settings-open="familySettingsOpen"
                @toggle-settings="familySettingsOpen = !familySettingsOpen"
            />

            <FamilySettings
                v-if="familySettingsOpen"
                :is-family-head="props.is_family_head"
                :current-user-id="props.current_user_id"
                :members="props.members"
            />

            <TransactionForm :categories="props.categories" />
            <CategoryManager :categories="props.categories" />
            <StatsOverview
                :family="props.family"
                :monthly-income="props.monthly_income"
                :monthly-expenses="props.monthly_expenses"
            />
            <RecentTransactions
                :transactions="props.recent_transactions"
                :is-family-head="props.is_family_head"
            />
        </div>
    </div>
</template>
