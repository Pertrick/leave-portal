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
                                :src="user.avatar"
                                :name="`${user.firstname} ${user.lastname}`"
                                size="lg"
                                :ring="true"
                            />
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ user.firstname }} {{ user.lastname }}</h3>
                                <p class="text-sm text-gray-500">{{ user.username }}</p>
                                <p class="text-sm text-gray-500">{{ user.email }}</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Role</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.roles? user.roles[0].name : 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Department</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.department?.name || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Employee ID</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.staff_id || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">User Level</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.user_level ? user.user_level.name : 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Designation</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.designation || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Gender</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.gender || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Date of Birth</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(user.dob) }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Join Date</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(user.join_date) }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.phone || 'Not assigned' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Address</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.address || 'Not assigned' }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Supervisor</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ user.active_supervisors?.[0]?.supervisor?.firstname || 'Not assigned' }}</p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Head of Department</h4>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ user.department?.active_head ? `${user.department.active_head.user.firstname} ${user.department.active_head.user.lastname}` : 'Not assigned' }}
                                </p>
                            </div>

                        </div>

                        <!-- Leave Balances Section -->
                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Balances</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div v-for="balance in user.leave_balances" :key="balance.id" class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-base font-semibold text-gray-900 mb-2">{{ balance.leave_type?.name }}</div>
                                            <div class="space-y-1">
                                                <div class="text-sm text-gray-500">
                                                    Year: {{ balance.year }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Entitled: {{ balance.total_entitled_days }} days
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Taken: {{ balance.days_taken }} days
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Remaining: {{ balance.days_remaining }} days
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Carried Forward: {{ balance.days_carried_forward }} days
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium" :class="{
                                                'text-green-600': balance.days_remaining > 0,
                                                'text-red-600': balance.days_remaining <= 0
                                            }">
                                                {{ balance.total_entitled_days > 0 
                                                    ? Math.round((balance.days_remaining / balance.total_entitled_days) * 100) 
                                                    : 0 }}% Available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!user.leave_balances?.length" class="col-span-3 text-center py-4 text-gray-500">
                                    No leave balances found
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import UserAvatar from '@/components/UserAvatar.vue';
import { useDateFormat } from '@/composables/useDateFormat';

const user = usePage().props.auth.user;

const { formatDate } = useDateFormat();
</script> 