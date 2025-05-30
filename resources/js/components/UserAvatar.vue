<template>
    <div class="relative">
        <div v-if="!src" class="flex items-center justify-center h-full w-full rounded-full bg-primary text-white font-medium" :class="sizeClasses">
            {{ displayInitials }}
        </div>
        <img
            v-else
            :src="src"
            :alt="displayName"
            class="h-full w-full rounded-full object-cover"
            :class="[sizeClasses, ringClasses]"
        />
        <div v-if="showStatus" class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full bg-green-400 ring-2 ring-background"></div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface User {
    firstname: string;
    lastname: string;
}

interface Props {
    src?: string | null;
    name?: string | null;
    user?: User | null;
    size?: 'sm' | 'md' | 'lg' | 'xl';
    showStatus?: boolean;
    ring?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    src: null,
    name: null,
    user: null,
    size: 'md',
    showStatus: false,
    ring: false
});

const sizeClasses = computed(() => ({
    'sm': 'h-8 w-8 text-sm',
    'md': 'h-10 w-10 text-base',
    'lg': 'h-12 w-12 text-lg',
    'xl': 'h-16 w-16 text-xl'
}[props.size]));

const ringClasses = computed(() => props.ring ? 'ring-2 ring-background' : '');

const displayName = computed(() => {
    if (props.name) return props.name;
    if (props.user) return `${props.user.firstname} ${props.user.lastname}`;
    return '';
});

const displayInitials = computed(() => {
    const name = displayName.value;
    
    if (!name) return '??';
    
    const words = name.trim().split(/\s+/);
    
    if (words.length === 1) {
        return words[0][0].toUpperCase();
    }
    
    return (words[0][0] + words[words.length - 1][0]).toUpperCase();
});

defineExpose({
    displayName,
    displayInitials
});
</script> 