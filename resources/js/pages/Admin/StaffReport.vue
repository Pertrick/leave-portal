<template>
    <AppLayout title="Staff Leave Report">
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Staff Leave Report
                </h2>
                <div class="flex items-center space-x-4">
                    <button 
                        @click="exportReport"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Report
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Date Range Filter -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                                <div class="flex space-x-4">
                                    <div class="flex-1">
                                        <input 
                                            type="date" 
                                            v-model="filters.startDate"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                    </div>
                                    <div class="flex-1">
                                        <input 
                                            type="date" 
                                            v-model="filters.endDate"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <select 
                                    v-model="filters.department"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Departments</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                        {{ dept.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Leave Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                                <select 
                                    v-model="filters.leaveType"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Types</option>
                                    <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select 
                                    v-model="filters.status"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <!-- Search Filter -->
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input 
                                    type="text" 
                                    v-model="filters.search"
                                    placeholder="Search by name, staff ID, or department..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <button 
                                @click="resetFilters"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Reset Filters
                            </button>
                            <button 
                                @click="applyFilters"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Report Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Role-based Title -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ userRole === 'admin' ? 'All Staff Leave Report' : 
                                   userRole === 'hod' ? 'Department Leave Report' : 
                                   'Supervised Staff Leave Report' }}
                            </h3>
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
                                        <td colspan="6" class="px-6 py-4 text-sm text-gray-500">
                                            No staff records found
                                        </td>
                                    </tr>
                                    <tr v-else v-for="user in users.data" :key="user.id">
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
                                            <div v-if="userLeaveStats[user.id]" class="space-y-1">
                                                <div v-for="entitlement in userLeaveStats[user.id].entitlements" :key="entitlement.type" class="flex items-center">
                                                    <span class="text-sm text-gray-900 mr-2">{{ entitlement.type }}:</span>
                                                    <span class="text-sm text-gray-500">{{ entitlement.remaining }}/{{ entitlement.total }}</span>
                                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 ml-2">
                                                        <div class="bg-indigo-600 h-1.5 rounded-full" :style="{ width: `${entitlement.percentage_used}%` }"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <span v-else class="text-sm text-gray-500">No entitlements</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ userLeaveStats[user.id]?.total_used || 0 }} days
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ userLeaveStats[user.id]?.leave_count?.total || 0 }} leaves
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <button 
                                                @click="viewDetails(user)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <Pagination :links="users.links" />
                        </div>
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

                            <!-- Leave Type Breakdown -->
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Leave Type Breakdown</h4>
                                <div class="space-y-2">
                                    <div v-for="(stats, typeId) in userLeaveStats[selectedUser.id].leave_count.by_type" :key="typeId" class="flex justify-between items-center">
                                        <p class="text-sm text-gray-900">{{ getLeaveTypeName(typeId) }}</p>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900">{{ stats.count }} leaves</p>
                                            <p class="text-xs text-gray-500">{{ stats.days }} days</p>
                                        </div>
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
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import { format } from 'date-fns';

interface Filters {
    startDate: string;
    endDate: string;
    department: string;
    leaveType: string;
    status: string;
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
    leaveTypes: any[];
    filters: Filters;
    userLeaveStats: Record<number, UserLeaveStats>;
    userRole: string;
    currentYear: number;
}>();

const filters = ref<Filters>(props.filters);
const showDetails = ref(false);
const selectedUser = ref<User | null>(null);

const formatDate = (date: string) => {
    return format(new Date(date), 'MMM dd, yyyy');
};

const getStatusClass = (status: string) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800'
    };
    return `px-2 py-1 text-xs font-medium rounded-full ${classes[status.toLowerCase()] || classes.pending}`;
};

const resetFilters = () => {
    filters.value = {
        department: '',
        search: ''
    };
    applyFilters();
};

const applyFilters = () => {
    router.get(route('admin.staff-report.index'), filters.value, {
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
    const params = new URLSearchParams();
    Object.entries(filters.value).forEach(([key, value]) => {
        if (value) params.append(key, value);
    });

    window.location.href = route('admin.staff-report.export') + '?' + params.toString();
};

const getLeaveTypeName = (typeId: number) => {
    const type = props.leaveTypes.find(t => t.id === typeId);
    return type ? type.name : 'Unknown Type';
};

onMounted(() => {
    if (!filters.value.startDate || !filters.value.endDate) {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

        filters.value.startDate = format(firstDay, 'yyyy-MM-dd');
        filters.value.endDate = format(lastDay, 'yyyy-MM-dd');
        applyFilters();
    }
});
</script> 