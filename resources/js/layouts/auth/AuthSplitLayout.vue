<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Alert from '@/components/Alert.vue';

const page = usePage();
const name = page.props.name;
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <Alert />
    <div class="flex min-h-svh">
        <div class="flex w-full flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10 lg:w-1/2">
            <div class="flex w-full max-w-md flex-col gap-6">
                <Link :href="route('home')" class="flex items-center gap-2 self-center font-medium">
                    <div class="flex h-9 w-9 items-center justify-center">
                        <AppLogoIcon class="size-9 fill-current text-black dark:text-white" />
                    </div>
                </Link>

                <div class="flex flex-col gap-6">
                    <div class="text-center">
                        <h1 class="text-xl font-semibold">{{ title }}</h1>
                        <p class="text-sm text-muted-foreground">{{ description }}</p>
                    </div>
                    <slot />
                </div>
            </div>
        </div>
        <div class="hidden w-1/2 bg-muted lg:block">
            <div class="flex h-full items-center justify-center">
                <div class="text-center">
                    <h2 class="text-2xl font-semibold">Welcome to Leave Portal</h2>
                    <p class="text-muted-foreground">Manage your leave applications efficiently</p>
                </div>
            </div>
        </div>
    </div>
</template>
