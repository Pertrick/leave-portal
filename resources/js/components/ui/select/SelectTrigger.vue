<template>
    <button
        type="button"
        class="flex h-10 w-full items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 ring-offset-white dark:ring-offset-gray-900 placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        :class="{ 'opacity-50': disabled }"
        :disabled="disabled"
        @click="handleClick"
        :aria-expanded="select?.isOpen"
    >
        <slot />
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="h-4 w-4 opacity-50"
        >
            <path d="m6 9 6 6 6-6" />
        </svg>
    </button>
</template>

<script setup lang="ts">
import { inject } from 'vue';

interface SelectContext {
    isOpen: boolean;
    toggle: () => void;
}

const props = defineProps<{
    disabled?: boolean;
}>();

const select = inject<SelectContext>('select');

const handleClick = () => {
    if (!props.disabled && select) {
        select.toggle();
    }
};
</script> 