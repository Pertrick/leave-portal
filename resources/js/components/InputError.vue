<script setup lang="ts">
const props = defineProps<{
    message?: string;
}>();
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600 dark:text-red-500">
            {{ message }}
        </p>
    </div>
</template>

<script lang="ts">
export default {
    name: 'InputError'
};
</script>
