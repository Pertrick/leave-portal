<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { type NavigationItem } from '@/types';
import Sidebar from '@/components/Sidebar.vue';
import Header from '@/components/Header.vue';
import Alert from '@/components/Alert.vue';

const props = defineProps<{
    title?: string;
}>();

const navigation = computed<NavigationItem[]>(() => {
    const page = usePage();
    return (page.props.navigation as NavigationItem[]) || [];
});

const breadcrumbs = computed<NavigationItem[]>(() => {
    const page = usePage();
    return (page.props.breadcrumbs as NavigationItem[]) || [];
});

const currentTitle = computed(() => {
    if (props.title) return props.title;
    
    const path = usePage().url;
    
    // Check children first
    for (const item of navigation.value) {
        if (item.children) {
            const child = item.children.find(child => 
                path.startsWith(`/${child.path}`)
            );
            if (child) return child.title;
        }
    }
    
    // Then check parent items
    const parent = navigation.value.find(item => 
        path.startsWith(`/${item.path}`)
    );
    
    return parent?.title || 'Dashboard';
});
</script>

<template>
    <div class="min-h-screen bg-background">
        <Alert />
        <div class="flex h-screen overflow-hidden">
            <div class="w-64 flex-shrink-0">
                <Sidebar :navigation="navigation" class="h-screen" />
            </div>
            
            <div class="flex-1 flex flex-col h-screen overflow-hidden">
                <Header :breadcrumbs="breadcrumbs" :title="currentTitle">
                    <template #actions>
                        <slot name="header-actions" />
                    </template>
                </Header>
                
                <main class="flex-1 overflow-y-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
