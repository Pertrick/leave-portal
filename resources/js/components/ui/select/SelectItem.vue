<template>
    <div
        v-if="isVisible"
        class="cursor-pointer px-4 py-2 text-sm text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700"
        :class="{ 'bg-gray-100 dark:bg-gray-700': isSelected }"
        @click="selectItem"
    >
        <slot />
    </div>
</template>

<script setup lang="ts">
import { inject, computed } from 'vue';

interface SelectContext {
    open: boolean;
    selectedValue: { value: string | number };
    searchQuery: string;
    setValue: (value: string | number) => void;
}

const select = inject<SelectContext>('select');
const props = defineProps<{
    value: string | number;
    label?: string;
}>();

const isSelected = computed(() => select?.selectedValue.value === props.value);
const isVisible = computed(() => {
    if (!select?.searchQuery || typeof select.searchQuery !== 'string') return true;
    const searchLower = select.searchQuery.toLowerCase();
    const label = props.label || String(props.value);
    return label.toLowerCase().includes(searchLower);
});

function selectItem() {
    select?.setValue(props.value);
}
</script> 