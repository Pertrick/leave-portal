<script setup lang="ts">
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import { computed } from 'vue';

interface Option {
    value: string | number;
    label: string;
}

const props = defineProps<{
    modelValue?: string | number;
    options: Option[];
    placeholder?: string;
    searchable?: boolean;
    disabled?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
}>();

const selectedOption = computed({
    get: () => props.modelValue ? props.options.find(option => option.value === props.modelValue) : null,
    set: (value: Option | null) => emit('update:modelValue', value?.value ?? '')
});

defineOptions({
    name: 'SearchSelect'
});
</script>

<template>
    <Multiselect
        v-model="selectedOption"
        :options="options"
        :searchable="searchable"
        :disabled="disabled"
        :placeholder="placeholder"
        :multiple="false"
        track-by="value"
        label="label"
    />
</template>

<style>
.multiselect-wrapper {
    position: relative;
}

.multiselect {
    min-height: 40px;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
    background-color: white;
    font-size: 0.875rem;
    color: #111827;
}

.dark .multiselect {
    border-color: #374151;
    background-color: #111827;
    color: #f3f4f6;
}

.multiselect.is-active {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px #3b82f6;
}

.multiselect-dropdown {
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

.dark .multiselect-dropdown {
    background-color: #1f2937;
    border-color: #374151;
}

.multiselect-option {
    font-size: 0.875rem;
    color: #111827;
    padding: 0.5rem 1rem;
}

.dark .multiselect-option {
    color: #f3f4f6;
}

.multiselect-option:hover {
    background-color: #f3f4f6;
}

.dark .multiselect-option:hover {
    background-color: #374151;
}

.multiselect-option.is-selected {
    background-color: #3b82f6;
    color: white;
}

.multiselect-option.is-selected:hover {
    background-color: #2563eb;
}

.multiselect-search {
    font-size: 0.875rem;
    color: #111827;
    background-color: transparent;
}

.dark .multiselect-search {
    color: #f3f4f6;
}

.multiselect-search:focus {
    outline: none;
}

.multiselect-placeholder {
    color: #6b7280;
}

.dark .multiselect-placeholder {
    color: #9ca3af;
}

.multiselect-single-label {
    color: #111827;
}

.dark .multiselect-single-label {
    color: #f3f4f6;
}

.multiselect-caret {
    color: #6b7280;
}

.dark .multiselect-caret {
    color: #9ca3af;
}

.multiselect-clear {
    color: #6b7280;
}

.dark .multiselect-clear {
    color: #9ca3af;
}

.multiselect-clear:hover {
    color: #374151;
}

.dark .multiselect-clear:hover {
    color: #d1d5db;
}
</style> 