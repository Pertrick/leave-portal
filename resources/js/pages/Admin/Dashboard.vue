<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Leave Management Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Pending Approvals</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.pendingApprovals }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">On Leave Today</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.onLeaveToday }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Leave Balance</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.totalLeaveBalance }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-700">Exhausted Leave</h3>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.exhaustedLeave }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Leave Distribution Chart -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Leave Distribution</h3>
                        <div class="h-64">
                            <canvas ref="leaveDistributionChart"></canvas>
                        </div>
                    </div>

                    <!-- Department-wise Leave Usage -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Department-wise Leave Usage</h3>
                        <div class="h-64">
                            <canvas ref="departmentLeaveChart"></canvas>
                        </div>
                    </div>

                    <!-- Recent Leave Applications -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Leave Applications</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="application in recentApplications" :key="application.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <UserAvatar :src="application.user.avatar" :user="application.user" size="sm" class="mr-3" />
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ application.user.firstname }} {{ application.user.lastname }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ application.user.staff_id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ application.leave_type.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(application.start_date) }} - {{ formatDate(application.end_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="getStatusClass(application.status)">
                                                {{ application.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Upcoming Leave -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Upcoming Leave</h3>
                        <div class="space-y-4">
                            <div v-for="leave in upcomingLeave" :key="leave.id" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <UserAvatar :src="leave.user.avatar" :user="leave.user" size="sm" class="mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ leave.user.firstname }} {{ leave.user.lastname }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ leave.leave_type.name }}</div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import UserAvatar from '@/Components/UserAvatar.vue';
import { Chart, registerables } from 'chart.js';
import { format } from 'date-fns';

Chart.register(...registerables);

interface Stats {
    pendingApprovals: number;
    onLeaveToday: number;
    totalLeaveBalance: number;
    exhaustedLeave: number;
}

interface LeaveApplication {
    id: number;
    user: {
        firstname: string;
        lastname: string;
        staff_id: string;
        avatar: string;
    };
    leave_type: {
        name: string;
    };
    start_date: string;
    end_date: string;
    status: string;
}

const stats = ref<Stats>({
    pendingApprovals: 0,
    onLeaveToday: 0,
    totalLeaveBalance: 0,
    exhaustedLeave: 0
});

const recentApplications = ref<LeaveApplication[]>([]);
const upcomingLeave = ref<LeaveApplication[]>([]);
const leaveDistributionChart = ref<HTMLCanvasElement | null>(null);
const departmentLeaveChart = ref<HTMLCanvasElement | null>(null);

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

onMounted(async () => {
    // Fetch dashboard data
    const response = await fetch('/api/admin/dashboard');
    const data = await response.json();
    
    stats.value = data.stats;
    recentApplications.value = data.recentApplications;
    upcomingLeave.value = data.upcomingLeave;

    // Initialize charts
    if (leaveDistributionChart.value) {
        new Chart(leaveDistributionChart.value, {
            type: 'doughnut',
            data: {
                labels: data.leaveDistribution.labels,
                datasets: [{
                    data: data.leaveDistribution.data,
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    if (departmentLeaveChart.value) {
        new Chart(departmentLeaveChart.value, {
            type: 'bar',
            data: {
                labels: data.departmentLeave.labels,
                datasets: [{
                    label: 'Leave Days Used',
                    data: data.departmentLeave.data,
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script> 