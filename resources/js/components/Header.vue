<script setup lang="ts">
import { type BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';

defineProps<{
    breadcrumbs: BreadcrumbItem[];
    title: string;
}>();
</script>

<template>
    <header class="border-b border-sidebar-border/70 bg-background">
        <div class="flex h-16 items-center px-4">
            <h1 class="text-xl font-semibold">{{ title }}</h1>
        </div>
        <nav class="flex items-center space-x-1 px-4 pb-3 text-sm">
            <Link
                v-for="(breadcrumb, index) in breadcrumbs"
                :key="breadcrumb.href"
                :href="breadcrumb.href"
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