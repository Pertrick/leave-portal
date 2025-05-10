<template>
    <div
        v-if="select?.isOpen"
        class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg max-h-60 overflow-auto"
    >
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
            <input
                type="text"
                class="w-full px-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-900 dark:text-gray-100"
                placeholder="Search..."
                v-model="searchInput"
                @input="handleSearch"
            />
        </div>
        <div class="py-1">
            <slot />
        </div>
    </div>
</template>

<script setup lang="ts">
import { inject, ref, watch } from 'vue';

interface SelectContext {
    isOpen: boolean;
    searchQuery: string;
    updateSearch: (query: string) => void;
}

const select = inject<SelectContext>('select');
const searchInput = ref('');

function handleSearch(event: Event) {
    const input = event.target as HTMLInputElement;
    if (select) {
        select.updateSearch(input.value);
    }
}

watch(() => select?.isOpen, (isOpen) => {
    if (!isOpen) {
        searchInput.value = '';
        if (select) {
            select.updateSearch('');
        }
    }
});
</script> 