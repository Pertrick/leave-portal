<template>
    <AppLayout title="Staff Leave Report">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page Header with Export Button -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">
                                {{ userRole === 'admin' ? 'All Staff Leave Report' : 
                                   userRole === 'hod' ? 'Department Leave Report' : 
                                   'Supervised Staff Leave Report' }}
                            </h1>
                            <p class="mt-1 text-sm text-gray-600">
                                View and analyze staff leave statistics and entitlements
                            </p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Export Button -->
                            <button 
                                @click="exportReport"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Report
                            </button>
                            
                            <!-- Filter Toggle Button -->
                            <button 
                                @click="toggleFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                                </svg>
                                {{ showFilters ? 'Hide Filters' : 'Show Filters' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Collapsible Filters Section -->
                <div v-show="showFilters" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 transition-all duration-300">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Filters</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Department Filter (Admin Only) -->
                            <div v-if="isAdmin">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select 
                                    v-model="filters.department"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Departments</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id.toString()">
                                        {{ dept.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Search Filter -->
                            <div :class="isAdmin ? 'md:col-span-2' : 'md:col-span-3'">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search Staff</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        v-model="filters.search"
                                        placeholder="Search by name, staff ID, or department..."
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <button 
                                @click="resetFilters"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset
                            </button>
                            <button 
                                @click="applyFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Staff List</h3>
                            <div class="text-sm text-gray-500">
                                {{ users.data?.length || 0 }} staff members
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Entitlements</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Used</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="!users?.data?.length" class="text-center">
                                    <td colspan="6" class="px-6 py-12 text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">No staff records found</p>
                                            <p class="text-gray-500">Try adjusting your filters or search criteria</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <UserAvatar :user="user" size="sm" class="mr-3" />
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ user.firstname }} {{ user.lastname }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ user.staff_id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.department?.name || 'No Department' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-if="userLeaveStats[user.id]" class="space-y-2">
                                            <div v-for="entitlement in userLeaveStats[user.id].entitlements" :key="entitlement.type" class="flex items-center">
                                                <span class="text-sm text-gray-900 mr-2 min-w-0 flex-1 truncate">{{ entitlement.type }}:</span>
                                                <span class="text-sm text-gray-500 mr-2">{{ entitlement.remaining }}/{{ entitlement.total }}</span>
                                                <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                    <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: `${entitlement.percentage_used}%` }"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <span v-else class="text-sm text-gray-500">No entitlements</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ userLeaveStats[user.id]?.total_used || 0 }} days
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ userLeaveStats[user.id]?.leave_count?.total || 0 }} leaves
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button 
                                            @click="viewDetails(user)"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium"
                                        >
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <Pagination :links="users.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Details Modal -->
        <Modal :show="showDetails" @close="showDetails = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Staff Leave Details
                </h2>
                
                <div v-if="selectedUser" class="space-y-4">
                    <!-- Staff Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Staff Information</h3>
                        <div class="flex items-center space-x-4">
                            <UserAvatar :user="selectedUser" size="md" />
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ selectedUser.firstname }} {{ selectedUser.lastname }}
                                </p>
                                <p class="text-sm text-gray-500">{{ selectedUser.staff_id }}</p>
                                <p class="text-sm text-gray-500">{{ selectedUser.department?.name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Entitlements -->
                    <div v-if="userLeaveStats[selectedUser.id]" class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Leave Entitlements ({{ currentYear }})</h3>
                        <div class="space-y-4">
                            <!-- Entitlements by Type -->
                            <div class="space-y-2">
                                <div v-for="entitlement in userLeaveStats[selectedUser.id].entitlements" :key="entitlement.type" class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ entitlement.type }}</p>
                                        <p class="text-xs text-gray-500">Total: {{ entitlement.total }} days</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-900">Used: {{ entitlement.used }} days</p>
                                        <p class="text-sm text-gray-900">Remaining: {{ entitlement.remaining }} days</p>
                                        <div class="w-24 bg-gray-200 rounded-full h-2.5 mt-1">
                                            <div class="bg-indigo-600 h-2.5 rounded-full" :style="{ width: `${entitlement.percentage_used}%` }"></div>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ entitlement.percentage_used }}% used</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Leave Usage Summary -->
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Leave Usage Summary</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Total Leaves Taken</p>
                                        <p class="text-lg font-medium text-gray-900">{{ userLeaveStats[selectedUser.id].leave_count.total }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Total Days Used</p>
                                        <p class="text-lg font-medium text-gray-900">{{ userLeaveStats[selectedUser.id].total_used }} days</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Usage -->
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Monthly Usage</h4>
                                <div class="space-y-2">
                                    <div v-for="(days, month) in userLeaveStats[selectedUser.id].monthly_usage" :key="month" class="flex justify-between items-center">
                                        <p class="text-sm text-gray-900">{{ month }}</p>
                                        <p class="text-sm font-medium text-gray-900">{{ days }} days</p>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import UserAvatar from '@/components/UserAvatar.vue';
import Modal from '@/components/Modal.vue';
import Pagination from '@/components/Pagination.vue';

interface Filters {
    department: string;
    search: string;
}

interface User {
    id: number;
    firstname: string;
    lastname: string;
    staff_id: string;
    department?: {
        name: string;
    };
}

interface LeaveEntitlement {
    type: string;
    total: number;
    used: number;
    remaining: number;
    percentage_used: number;
}

interface UserLeaveStats {
    entitlements: LeaveEntitlement[];
    total_used: number;
    leave_count: {
        total: number;
        by_type: Record<number, { count: number; days: number }>;
    };
    monthly_usage: Record<string, number>;
}

interface UsersResponse {
    data: User[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    }[];
}

const props = defineProps<{
    users: UsersResponse;
    departments: any[];
    filters: Filters;
    userLeaveStats: Record<number, UserLeaveStats>;
    userRole: string;
    currentYear: number;
    userDepartment: any;
}>();

const filters = ref<Filters>({
    ...props.filters,
    department: props.filters.department ? props.filters.department.toString() : ''
});
const showDetails = ref(false);
const selectedUser = ref<User | null>(null);
const showFilters = ref(true);

const isAdmin = computed(() => props.userRole === 'admin');

const resetFilters = () => {
    filters.value = {
        department: '',
        search: ''
    };
    applyFilters();
};

const applyFilters = () => {
    // For security: only pass department in URL for admin users
    const params: any = { ...filters.value };
    if (!isAdmin.value) {
        delete params.department; // Remove department from URL for non-admin users
    }
    
    router.get(route('staff-report.index'), params, {
        preserveState: true,
        preserveScroll: true,
        only: ['users', 'userLeaveStats']
    });
};

const viewDetails = (user: User) => {
    selectedUser.value = user;
    showDetails.value = true;
};

const exportReport = () => {
    console.log('Export button clicked');
    console.log('Current filters:', filters.value);
    console.log('Is admin:', isAdmin.value);
    
    const params = new URLSearchParams();
    Object.entries(filters.value).forEach(([key, value]) => {
        // For security: only pass department in URL for admin users
        if (value && (key !== 'department' || isAdmin.value)) {
            params.append(key, value);
        }
    });

    const exportUrl = route('staff-report.export') + '?' + params.toString();
    console.log('Export URL:', exportUrl);
    
    window.location.href = exportUrl;
};

const getLeaveTypeName = (typeId: number) => {
    // Since we don't have leaveTypes prop anymore, we'll use a fallback
    // The type name should be available in the entitlement data
    return `Leave Type ${typeId}`;
};

const toggleFilters = () => {
    showFilters.value = !showFilters.value;
};

onMounted(() => {
    console.log('StaffReport component mounted');
    console.log('User role:', props.userRole);
    console.log('Is admin:', isAdmin.value);
    console.log('User department:', props.userDepartment);
    console.log('Current filters:', filters.value);
    
    if (!isAdmin.value && props.userDepartment) {
        filters.value.department = props.userDepartment.id.toString();
    }
});
</script> 