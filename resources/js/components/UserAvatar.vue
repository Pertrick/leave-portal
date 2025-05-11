<template>
    <div class="relative">
        <div v-if="!src" class="flex items-center justify-center h-full w-full rounded-full bg-primary text-white font-medium" :class="sizeClasses">
            {{ initials }}
        </div>
        <img
            v-else
            :src="src"
            :alt="name"
            class="h-full w-full rounded-full object-cover"
            :class="[sizeClasses, ringClasses]"
        />
        <div v-if="showStatus" class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full bg-green-400 ring-2 ring-background"></div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue';

const props = defineProps({
    src: {
        type: String,
        default: null
    },
    name: {
        type: String,
        required: true
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    showStatus: {
        type: Boolean,
        default: false
    },
    ring: {
        type: Boolean,
        default: false
    }
});

const sizeClasses = computed(() => ({
    'sm': 'h-8 w-8 text-sm',
    'md': 'h-10 w-10 text-base',
    'lg': 'h-12 w-12 text-lg',
    'xl': 'h-16 w-16 text-xl'
}[props.size]));

const ringClasses = computed(() => props.ring ? 'ring-2 ring-background' : '');

const initials = computed(() => {
    console.log('Name prop:', props.name);
    
    if (!props.name) {
        console.log('No name provided');
        return '??';
    }
    
    const words = props.name.trim().split(/\s+/);
    console.log('Split words:', words);
    
    if (words.length === 1) {
        console.log('Single word, returning:', words[0][0].toUpperCase());
        return words[0][0].toUpperCase();
    }
    
    const result = (words[0][0] + words[words.length - 1][0]).toUpperCase();
    console.log('Final initials:', result);
    return result;
});

// Add a watch to debug prop changes
watch(() => props.name, (newName) => {
    console.log('Name prop changed:', newName);
}, { immediate: true });
</script> 