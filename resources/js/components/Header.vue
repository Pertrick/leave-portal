<script setup lang="ts">
import { type NavigationItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps<{
    breadcrumbs: NavigationItem[];
    title: string;
}>();

const getFullPath = (item: NavigationItem): string => {
    if (item.path.startsWith('/')) {
        return item.path;
    }
    return `/${item.path}`;
};

defineExpose({});
</script>

<template>
    <header class="border-b border-sidebar-border/70 bg-background">
        <div class="flex h-16 items-center px-4">
            <h1 class="text-xl font-semibold">{{ title }}</h1>
        </div>
        <nav class="flex items-center space-x-1 px-4 pb-3 text-sm">
            <Link
                v-for="(breadcrumb, index) in breadcrumbs"
                :key="breadcrumb.id"
                :href="getFullPath(breadcrumb)"
                class="flex items-center text-muted-foreground hover:text-foreground"
                :class="{ 'text-foreground': index === breadcrumbs.length - 1 }"
            >
                <span>{{ breadcrumb.title }}</span>
                <ChevronRight
                    v-if="index < breadcrumbs.length - 1"
                    class="h-4 w-4 mx-1"
                />
            </Link>
        </nav>
    </header>
</template> 