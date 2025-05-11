<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { type NavigationItem } from '@/types';
import { 
    Home,
    Calendar,
    Users,
    Building,
    BarChart,
    Settings,
    User,
    FilePlus,
    History,
    CheckCircle,
    List,
    UserPlus,
    ChevronLeft,
    ChevronRight,
    ChevronDown
} from 'lucide-vue-next';

const props = defineProps<{
    navigation: NavigationItem[];
}>();

const isCollapsed = ref(false);
const openDropdowns = ref(new Set<number>());

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};

const toggleDropdown = (id: number) => {
    if (openDropdowns.value.has(id)) {
        openDropdowns.value.delete(id);
    } else {
        openDropdowns.value.add(id);
    }
};

const isDropdownOpen = (id: number) => {
    return openDropdowns.value.has(id);
};

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
    user: User,
    'file-plus': FilePlus,
    history: History,
    'check-circle': CheckCircle,
    list: List,
    'user-plus': UserPlus
} as const;

const getIcon = (iconName?: string) => {
    if (!iconName) return null;
    return iconMap[iconName as keyof typeof iconMap] || null;
};

const getFullPath = (item: NavigationItem): string => {
    if (!item.path) return '#';
    if (item.path.startsWith('/')) {
        return item.path;
    }
    return `/${item.path}`;
};

const getChildItems = (parentId: number) => {
    const parent = props.navigation.find(item => item.id === parentId);
    return parent?.children || [];
};

defineExpose({});
</script>

<template>
    <aside 
        class="relative border-r border-sidebar-border/70 bg-sidebar transition-all duration-300"
        :class="[isCollapsed ? 'w-16' : 'w-64']"
    >
        <!-- Toggle Button -->
        <button
            @click="toggleSidebar"
            class="absolute -right-3 top-6 z-50 flex h-6 w-6 items-center justify-center rounded-full border border-sidebar-border/70 bg-sidebar shadow-md hover:bg-accent"
        >
            <component
                :is="isCollapsed ? ChevronRight : ChevronLeft"
                class="h-4 w-4"
            />
        </button>

        <div class="flex h-16 items-center border-b border-sidebar-border/70 px-4">
            <h1 
                class="text-xl font-semibold transition-opacity duration-300"
                :class="{ 'opacity-0': isCollapsed }"
            >
                Leave Portal
            </h1>
        </div>
        
        <nav class="space-y-1 p-2">
            <template v-for="item in navigation" :key="item.id">
                <!-- Parent Item with Dropdown -->
                <div v-if="item.children && item.children.length > 0">
                    <button
                        @click="toggleDropdown(item.id)"
                        class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                        :class="[isDropdownOpen(item.id) ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground']"
                    >
                        <div class="flex items-center">
                            <component
                                v-if="getIcon(item.icon)"
                                :is="getIcon(item.icon)"
                                class="h-5 w-5"
                                :class="{ 'mr-3': !isCollapsed }"
                            />
                            <span 
                                class="transition-opacity duration-300"
                                :class="{ 'opacity-0': isCollapsed }"
                            >
                                {{ item.title }}
                            </span>
                        </div>
                        <ChevronDown
                            v-if="!isCollapsed"
                            class="h-4 w-4 transition-transform duration-200"
                            :class="{ 'transform rotate-180': isDropdownOpen(item.id) }"
                        />
                    </button>

                    <!-- Child Items -->
                    <div 
                        v-if="isDropdownOpen(item.id) && !isCollapsed" 
                        class="ml-4 mt-1 space-y-1"
                    >
                        <Link
                            v-for="child in item.children"
                            :key="child.id"
                            :href="getFullPath(child)"
                            class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                            :class="[currentPath.startsWith(getFullPath(child)) ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground']"
                        >
                            <component
                                v-if="getIcon(child.icon)"
                                :is="getIcon(child.icon)"
                                class="mr-3 h-5 w-5"
                            />
                            <span>{{ child.title }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Regular Navigation Item -->
                <Link
                    v-else
                    :href="getFullPath(item)"
                    class="flex items-center rounded-lg px-3 py-2 text-sm font-medium transition-colors"
                    :class="[currentPath.startsWith(getFullPath(item)) ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground']"
                >
                    <component
                        v-if="getIcon(item.icon)"
                        :is="getIcon(item.icon)"
                        class="h-5 w-5"
                        :class="{ 'mr-3': !isCollapsed }"
                    />
                    <span 
                        class="transition-opacity duration-300"
                        :class="{ 'opacity-0': isCollapsed }"
                    >
                        {{ item.title }}
                    </span>
                </Link>
            </template>
        </nav>
    </aside>
</template>

<style scoped>
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}
</style>
