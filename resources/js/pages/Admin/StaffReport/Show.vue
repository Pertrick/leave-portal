<template>
    <AppLayout :title="`${user?.firstname} ${user?.lastname} - Leave Details`">
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <UserAvatar :user="user" size="lg" />
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">
                                    {{ user?.firstname }} {{ user?.lastname }}
                                </h1>
                                <p class="text-sm text-gray-600">{{ user?.staff_id }}</p>
                                <p class="text-sm text-gray-600">{{ user?.department?.name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button 
                                @click="goBack"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Report
                            </button>
                            <button 
                                @click="exportUserReport"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Export Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Leaves</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ leaveStats.total_leaves }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Days Used</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ leaveStats.total_days_used }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ leaveStats.pending_leaves }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ leaveStats.rejected_leaves }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Entitlements -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Leave Entitlements ({{ currentYear }})</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="entitlement in leaveStats.entitlements" :key="entitlement.type" class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900">{{ entitlement.type }}</h4>
                                    <span class="text-xs text-gray-500">{{ entitlement.percentage_used }}% used</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Total:</span>
                                        <span class="font-medium">{{ entitlement.total }} days</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Used:</span>
                                        <span class="font-medium text-red-600">{{ entitlement.used }} days</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Remaining:</span>
                                        <span class="font-medium text-green-600">{{ entitlement.remaining }} days</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" :style="{ width: `${entitlement.percentage_used}%` }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Leave History</h3>
                            <div class="flex items-center space-x-2">
                                <select v-model="statusFilter" class="text-sm border-gray-300 rounded-md">
                                    <option value="">All Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="pending">Pending</option>
                                    <option value="rejected">Rejected</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied On</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="!filteredLeaves.length" class="text-center">
                                    <td colspan="6" class="px-6 py-12 text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">No leave records found</p>
                                            <p class="text-gray-500">This staff member hasn't applied for any leaves yet</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else v-for="leave in filteredLeaves" :key="leave.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ leave.leave_type?.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ leave.working_days }} days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusClass(leave.status)">
                                            {{ leave.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(leave.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button 
                                            @click="viewLeaveDetails(leave)"
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
                        <Pagination :links="leaves.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Leave Details Modal -->
        <Modal :show="showLeaveDetails" @close="showLeaveDetails = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Leave Application Details
                </h2>
                
                <div v-if="selectedLeave" class="space-y-4">
                    <!-- Leave Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Leave Information</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Leave Type</p>
                                <p class="font-medium">{{ selectedLeave.leave_type?.name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Status</p>
                                <span :class="getStatusClass(selectedLeave.status)">{{ selectedLeave.status }}</span>
                            </div>
                            <div>
                                <p class="text-gray-600">Start Date</p>
                                <p class="font-medium">{{ formatDate(selectedLeave.start_date) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">End Date</p>
                                <p class="font-medium">{{ formatDate(selectedLeave.end_date) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Working Days</p>
                                <p class="font-medium">{{ selectedLeave.working_days }} days</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Applied On</p>
                                <p class="font-medium">{{ formatDate(selectedLeave.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div v-if="selectedLeave.reason" class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Reason</h3>
                        <p class="text-sm text-gray-700">{{ selectedLeave.reason }}</p>
                    </div>

                    <!-- Approval Chain -->
                    <div v-if="selectedLeave.approvals?.length" class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Approval Chain</h3>
                        <div class="space-y-2">
                            <div v-for="approval in selectedLeave.approvals" :key="approval.id" class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">{{ approval.approver?.firstname }} {{ approval.approver?.lastname }}</p>
                                    <p class="text-gray-500">{{ approval.approver?.role }}</p>
                                </div>
                                <div class="text-right">
                                    <span :class="getStatusClass(approval.status)">{{ approval.status }}</span>
                                    <p class="text-gray-500">{{ formatDate(approval.created_at) }}</p>
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
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import UserAvatar from '@/components/UserAvatar.vue';
import Modal from '@/components/Modal.vue';
import Pagination from '@/components/Pagination.vue';

interface User {
    id: number;
    firstname: string;
    lastname: string;
    staff_id: string;
    email: string;
    department?: {
        name: string;
    };
}

interface LeaveType {
    id: number;
    name: string;
}

interface LeaveApproval {
    id: number;
    status: string;
    created_at: string;
    approver?: {
        firstname: string;
        lastname: string;
        role: string;
    };
}

interface Leave {
    id: number;
    leave_type: LeaveType;
    start_date: string;
    end_date: string;
    working_days: number;
    status: string;
    reason?: string;
    created_at: string;
    approvals?: LeaveApproval[];
}

interface LeaveEntitlement {
    type: string;
    total: number;
    used: number;
    remaining: number;
    percentage_used: number;
}

interface LeaveStats {
    total_leaves: number;
    total_days_used: number;
    pending_leaves: number;
    rejected_leaves: number;
    entitlements: LeaveEntitlement[];
}

interface LeavesResponse {
    data: Leave[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    }[];
}

const props = defineProps<{
    user: User;
    leaves: LeavesResponse;
    leaveStats: LeaveStats;
    currentYear: number;
}>();

const statusFilter = ref('');
const showLeaveDetails = ref(false);
const selectedLeave = ref<Leave | null>(null);

const filteredLeaves = computed(() => {
    if (!statusFilter.value) return props.leaves.data;
    return props.leaves.data.filter(leave => leave.status === statusFilter.value);
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusClass = (status: string) => {
    const classes = {
        'pending': 'px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800',
        'approved': 'px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800',
        'rejected': 'px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800',
        'cancelled': 'px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800'
    };
    return classes[status.toLowerCase()] || classes.pending;
};

const goBack = () => {
    router.get(route('staff-report.index'));
};

const exportUserReport = () => {
    const params = new URLSearchParams();
    params.append('user_id', props.user.id.toString());
    
    const exportUrl = route('staff-report.export') + '?' + params.toString();
    window.location.href = exportUrl;
};

const viewLeaveDetails = (leave: Leave) => {
    selectedLeave.value = leave;
    showLeaveDetails.value = true;
};

onMounted(() => {
    console.log('StaffReport Show component mounted');
    console.log('User:', props.user);
    console.log('Leave stats:', props.leaveStats);
});
</script> 