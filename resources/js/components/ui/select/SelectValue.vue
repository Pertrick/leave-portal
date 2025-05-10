<template>
    <span class="block truncate">
        <slot v-if="!select?.selectedValue.value">{{ placeholder }}</slot>
        <slot v-else />
    </span>
</template>

<script setup lang="ts">
import { inject } from 'vue';

interface SelectContext {
    selectedValue: { value: string | number };
}

const select = inject<SelectContext>('select');
const props = defineProps<{
    placeholder?: string;
}>();
</script> 