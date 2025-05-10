<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { 
    Home,
    Calendar,
    Users,
    Building,
    BarChart,
    Settings,
    User
} from 'lucide-vue-next';

const props = defineProps<{
    navigation: Array<{
        path: string;
        title: string;
        icon?: string;
        children?: Array<{
            path: string;
            title: string;
            icon?: string;
        }>;
    }>;
}>();

const currentPath = computed(() => {
    return usePage().url;
});

const iconMap = {
    home: Home,
    calendar: Calendar,
    users: Users,
    building: Building,
    'bar-chart': BarChart,
    settings: Settings,
    user: User
};

const getIcon = (iconName?: string) => {
    if (!iconName) return null;
    return iconMap[iconName as keyof typeof iconMap] || null;
};
</script>

<template>
    <aside class="w-64 border-r border-sidebar-border/70 bg-sidebar">
        <div class="flex h-16 items-center border-b border-sidebar-border/70 px-4">
            <h1 class="text-xl font-semibold">Leave Portal</h1>
        </div>
        
        <nav class="space-y-1 p-2">
            <template v-for="item in navigation" :key="item.path">
                <!-- Parent Item -->
                <Link
                    :href="`/${item.path}`"
                    class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                    :class="[
                        currentPath.startsWith(`/${item.path}`)
                            ? 'bg-primary text-primary-foreground'
                            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                    ]"
                >
                    <component
                        v-if="getIcon(item.icon)"
                        :is="getIcon(item.icon)"
                        class="mr-3 h-5 w-5"
                    />
                    <span>{{ item.title }}</span>
                </Link>

                <!-- Child Items -->
                <div v-if="item.children" class="ml-4 mt-1 space-y-1">
                    <Link
                        v-for="child in item.children"
                        :key="child.path"
                        :href="`/${item.path}/${child.path}`"
                        class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        :class="[
                            currentPath.startsWith(`/${item.path}/${child.path}`)
                                ? 'bg-primary text-primary-foreground'
                                : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'
                        ]"
                    >
                        <component
                            v-if="getIcon(child.icon)"
                            :is="getIcon(child.icon)"
                            class="mr-3 h-5 w-5"
                        />
                        <span>{{ child.title }}</span>
                    </Link>
                </div>
            </template>
        </nav>
    </aside>
</template> 