<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        :class="className"
        v-bind="$attrs"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path
            d="M3 7.5A2.5 2.5 0 0 1 5.5 5H18a2 2 0 0 1 2 2v1.5H5.5A2.5 2.5 0 0 0 3 10.8v-3.3Z"
        />
        <path
            d="M3 10.8A2.5 2.5 0 0 1 5.5 8.3H20a2 2 0 0 1 2 2v6.2a2 2 0 0 1-2 2H5.5A2.5 2.5 0 0 1 3 16.2v-5.4Z"
        />
        <path d="M15 13.5h4.5" />
        <circle cx="16.5" cy="13.5" r="1.2" />
        <path d="M7 12h.01" />
    </svg>
</template>
