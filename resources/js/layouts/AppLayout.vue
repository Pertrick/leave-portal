<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import Sidebar from '@/components/Sidebar.vue';
import Header from '@/components/Header.vue';

const props = defineProps<{
    title?: string;
}>();

const pageTitle = computed(() => {
    return props.title || usePage().props.title || 'Leave Portal';
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => {
    const page = usePage();
    return page.props.breadcrumbs || [];
});

const navigation = computed(() => {
    const page = usePage();
    return page.props.navigation || [];
});
</script>

<template>
    <div class="flex h-screen bg-background">
        <Sidebar :navigation="navigation" />
        
        <div class="flex flex-1 flex-col overflow-hidden">
            <Header :breadcrumbs="breadcrumbs" :title="pageTitle" />
            
            <main class="flex-1 overflow-y-auto p-4">
                <slot />
            </main>
        </div>
    </div>
</template>
