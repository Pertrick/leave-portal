<script setup lang="ts">
import { type NavigationItem } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ChevronRight, LogOut, UserCircle, Key, ChevronDown } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';
import {
    ArrowRightOnRectangleIcon,
    UserCircleIcon,
    KeyIcon,
    ChevronDownIcon
} from '@heroicons/vue/24/outline';
import UserAvatar from '@/Components/UserAvatar.vue';

const props = defineProps<{
    breadcrumbs: NavigationItem[];
    title: string;
}>();

const user = usePage().props.auth.user;
const isUserMenuOpen = ref(false);

const getFullPath = (item: NavigationItem): string => {
    return item.path || '';
};

const handleLogout = () => {
    router.post(route('logout'));
};

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
    isUserMenuOpen.value = false;
};

defineExpose({});
</script>

<template>
    <header class="border-b border-sidebar-border/70 bg-background">
        <div class="flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-semibold">{{ title }}</h1>
                <slot name="actions" />
            </div>
            <div class="flex items-center gap-4">
                <!-- User Profile Dropdown -->
                <div class="relative" v-click-outside="closeUserMenu">
                    <button
                        @click="toggleUserMenu"
                        class="flex items-center gap-2 px-3 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors duration-200"
                    >
                        <UserAvatar
                            :src="user.profile_photo_url"
                            :name="user.name || ''"
                            size="sm"
                            :show-status="true"
                            :ring="true"
                        />
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium">{{ user.name }}</p>
                            <p class="text-xs text-muted-foreground">{{ user.role }}</p>
                        </div>
                        <ChevronDown
                            class="h-4 w-4 text-muted-foreground transition-transform duration-200"
                            :class="{ 'rotate-180': isUserMenuOpen }"
                        />
                    </button>

                    <!-- Dropdown Menu -->
                    <div
                        v-show="isUserMenuOpen"
                        class="absolute right-0 mt-2 w-72 rounded-lg bg-background py-2 shadow-lg ring-1 ring-border z-50"
                        role="menu"
                        aria-orientation="vertical"
                        aria-labelledby="user-menu"
                    >
                        <div class="px-4 py-3 border-b border-border">
                            <p class="text-sm font-medium">{{ user.name }}</p>
                            <p class="text-xs text-muted-foreground truncate">{{ user.email }}</p>
                        </div>

                        <div class="py-1">
                            <Link
                                :href="route('profile')"
                                class="flex items-center px-4 py-2 text-sm text-muted-foreground hover:text-foreground hover:bg-accent"
                                :class="{ 'bg-accent': route().current('profile') }"
                            >
                                <UserCircle class="mr-3 h-5 w-5" />
                                My Profile
                            </Link>
                        </div>

                        <div class="border-t border-border py-1">
                            <Button
                                variant="ghost"
                                class="w-full justify-start px-4 py-2 text-sm text-muted-foreground hover:text-destructive hover:bg-destructive/10"
                                @click="handleLogout"
                            >
                                <LogOut class="mr-3 h-5 w-5" />
                                Sign Out
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
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

<style scoped>
.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.hover\:bg-gray-50:hover {
    background-color: rgb(249 250 251);
}

.ring-2 {
    --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
    box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
}
</style> 