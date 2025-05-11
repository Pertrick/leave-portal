<template>
    <AppLayout title="Profile">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center space-x-4">
                            <UserAvatar
                                :src="user.profile_photo_url"
                                :name="user.name || ''"
                                size="lg"
                                :ring="true"
                            />
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ user.name }}</h3>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Role</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.role }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Department</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.department?.name || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Location</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.location?.name || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Employee ID</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.employee_id || 'Not assigned' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import UserAvatar from '@/Components/UserAvatar.vue';

const user = usePage().props.auth.user;
</script> 