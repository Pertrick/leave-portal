<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted } from 'vue';
import { type NavigationItem } from '@/types';
import Sidebar from '@/components/Sidebar.vue';
import Header from '@/components/Header.vue';
import Alert from '@/Components/Alert.vue';

const props = defineProps<{
    title?: string;
}>();

const pageTitle = computed(() => {
    return props.title || usePage().props.title as string || 'Leave Portal';
});

const navigation = computed<NavigationItem[]>(() => {
    const page = usePage();
    const nav = (page.props.navigation as NavigationItem[]) || [];
    console.log('Navigation data:', nav);
    return nav;
});

const breadcrumbs = computed<NavigationItem[]>(() => {
    const page = usePage();
    const crumbs = (page.props.breadcrumbs as NavigationItem[]) || [];
    console.log('Breadcrumbs data:', crumbs);
    return crumbs;
});

const currentTitle = computed(() => {
    const path = usePage().url;
    
    // First check children
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

onMounted(() => {
    console.log('AppLayout mounted');
    console.log('Page props:', usePage().props);
});

defineExpose({});
</script>

<template>
    <div>
        <Alert />
        <div class="flex h-screen bg-background">
            <Sidebar :navigation="navigation" />
            
            <div class="flex flex-1 flex-col overflow-hidden">
                <Header :breadcrumbs="breadcrumbs" :title="currentTitle" />
                
                <main class="flex-1 overflow-y-auto p-4">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
