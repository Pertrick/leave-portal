<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Calendar, Clock, CalendarCheck, Users, AlertCircle, PieChart, BarChart, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { useDateFormat } from '@/composables/useDateFormat';

const props = defineProps<{
    totalAvailableDays: number;
    pendingRequests: number;
    upcomingLeaves: number;
    teamMembers: Array<{
        id: number;
        name: string;
        designation: string;
        avatar: string | null;
        isOnLeave: boolean;
        leaveDetails?: {
            type: string;
            start_date: string;
            end_date: string;
            days: number;
        };
    }>;
    recentRequests: Array<{
        id: number;
        type: string;
        days: number;
        start_date: string;
        status: 'pending' | 'approved' | 'rejected';
    }>;
    calendarDays: Array<{
        date: string;
        day: number;
        isCurrentMonth: boolean;
        isToday: boolean;
        hasLeave: boolean;
        isHoliday: boolean;
    }>;
    leaveDistribution: Array<{
        type: string;
        used: number;
        total: number;
        remaining: number;
        color: string;
    }>;
    monthlyTrends: Array<{
        month: string;
        leaves: number;
        pending_days: number;
        approved_days: number;
        total_days: number;
    }>;
    statusOverview: Array<{
        status: string;
        count: number;
        color: string;
    }>;
    leaveTypeAnalysis: Array<{
        type: string;
        total_days: number;
        count: number;
        average_duration: number;
        longest_leave: number;
        shortest_leave: number;
        color: string;
        department_comparison?: {
            department_avg_days: number;
            department_avg_duration: number;
            department_total_users: number;
            user_rank: {
                position: number;
                total: number;
            };
        };
    }>;
}>();

const { formatDate } = useDateFormat();

const weekDays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200';
        case 'approved':
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200';
        case 'rejected':
            return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-200';
    }
};

const currentMonth = ref(new Date());

const calendarDays = computed(() => {
    const startOfMonth = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth(), 1);
    const endOfMonth = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 0);
    
    const days = [];
    const currentDate = new Date(startOfMonth);
    
    // Move to the previous Sunday
    const dayOfWeek = currentDate.getDay();
    currentDate.setDate(currentDate.getDate() - dayOfWeek);
    
    // Generate 6 weeks of days (42 days)
    for (let i = 0; i < 42; i++) {
        const isCurrentMonth = currentDate.getMonth() === currentMonth.value.getMonth();
        const isToday = currentDate.toDateString() === new Date().toDateString();
        const dayOfWeek = currentDate.getDay();
        const isSunday = dayOfWeek === 0;
        const isSaturday = dayOfWeek === 6;
        
        // Check for leaves
        const hasLeave = props.recentRequests.some(request => {
            const startDate = new Date(request.start_date);
            const endDate = new Date(request.start_date);
            endDate.setDate(endDate.getDate() + request.days - 1);
            return currentDate >= startDate && currentDate <= endDate && request.status === 'approved';
        });
        
        days.push({
            date: currentDate.toISOString().split('T')[0],
            day: currentDate.getDate(),
            isCurrentMonth,
            isToday,
            hasLeave,
            isHoliday: false,
            isSunday,
            isSaturday
        });
        
        currentDate.setDate(currentDate.getDate() + 1);
    }
    
    return days;
});

const navigateMonth = (direction: 'prev' | 'next') => {
    const newDate = new Date(currentMonth.value);
    if (direction === 'prev') {
        newDate.setMonth(newDate.getMonth() - 1);
    } else {
        newDate.setMonth(newDate.getMonth() + 1);
    }
    currentMonth.value = newDate;
};

const isWeekend = (date: string) => {
    const day = new Date(date).getDay();
    // 0 is Sunday, 6 is Saturday
    return day === 0 || day === 6;
};

const getLeaveTypeColor = (type: string) => {
    const typeLower = type.toLowerCase();
    if (typeLower.includes('annual')) return 'from-sky-100 to-sky-200';
    if (typeLower.includes('sick')) return 'from-rose-100 to-rose-200';
    if (typeLower.includes('maternity')) return 'from-violet-100 to-violet-200';
    if (typeLower.includes('paternity')) return 'from-fuchsia-100 to-fuchsia-200';
    if (typeLower.includes('compassionate')) return 'from-teal-100 to-teal-200';
    if (typeLower.includes('study')) return 'from-amber-100 to-amber-200';
    if (typeLower.includes('unpaid')) return 'from-cyan-100 to-cyan-200';
    return 'from-indigo-100 to-indigo-200';
};

const getLeaveTypeIcon = (type: string) => {
    const typeLower = type.toLowerCase();
    if (typeLower.includes('annual')) return 'bg-sky-100 text-sky-600';
    if (typeLower.includes('sick')) return 'bg-rose-100 text-rose-600';
    if (typeLower.includes('maternity')) return 'bg-violet-100 text-violet-600';
    if (typeLower.includes('paternity')) return 'bg-fuchsia-100 text-fuchsia-600';
    if (typeLower.includes('compassionate')) return 'bg-teal-100 text-teal-600';
    if (typeLower.includes('study')) return 'bg-amber-100 text-amber-600';
    if (typeLower.includes('unpaid')) return 'bg-cyan-100 text-cyan-600';
    return 'bg-indigo-100 text-indigo-600';
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Quick Stats -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Leave Balance Card -->
                <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-gradient-to-br from-indigo-50 to-white p-6 shadow-sm dark:from-indigo-900/20 dark:to-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 p-3">
                            <Calendar class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                    Available Leave Days
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ totalAvailableDays }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-emerald-600">
                                        <span>days</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests Card -->
                <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-gradient-to-br from-amber-50 to-white p-6 shadow-sm dark:from-amber-900/20 dark:to-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-lg bg-gradient-to-br from-amber-500 to-amber-600 p-3">
                            <AlertCircle class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-amber-600 dark:text-amber-400">
                                    Pending Requests
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ pendingRequests }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-amber-600">
                                        <span>requests</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Leaves Card -->
                <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-gradient-to-br from-emerald-50 to-white p-6 shadow-sm dark:from-emerald-900/20 dark:to-gray-800 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 p-3">
                            <CalendarCheck class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="truncate text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                    Upcoming Leaves
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                                        {{ upcomingLeaves }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-emerald-600">
                                        <span>days</span>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Breakdown Section -->
            <div class="grid gap-4 md:grid-cols-3">
                <!-- Leave Type Distribution -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-gradient-to-br from-indigo-50 to-white shadow-sm dark:from-indigo-900/20 dark:to-gray-800 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-indigo-900 dark:text-indigo-100">Leave Distribution</h3>
                            <PieChart class="h-5 w-5 text-indigo-500" />
                        </div>
                        <div class="mt-4 max-h-[300px] overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                            <div v-for="item in leaveDistribution" :key="item.type" 
                                class="group space-y-2 rounded-lg p-3 transition-all duration-200 hover:bg-white/50 dark:hover:bg-gray-800/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div :class="[getLeaveTypeIcon(item.type), 'flex h-8 w-8 items-center justify-center rounded-lg']">
                                            <Calendar class="h-4 w-4" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ item.type }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ item.used }}/{{ item.total }}</span>
                                </div>
                                <div class="relative h-2.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                                    <div class="absolute inset-0 h-full rounded-full transition-all duration-500"
                                        :class="getLeaveTypeColor(item.type)"
                                        :style="{ width: `${(item.used / item.total) * 100}%` }">
                                    </div>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-gray-500 dark:text-gray-400">Used:</span>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.used }} days</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span class="text-gray-500 dark:text-gray-400">Remaining:</span>
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.remaining }} days</span>
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ Math.round((item.used / item.total) * 100) }}% utilized
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Type Usage Analysis -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-gradient-to-br from-emerald-50 to-white shadow-sm dark:from-emerald-900/20 dark:to-gray-800 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-emerald-900 dark:text-emerald-100">Leave Usage Analysis</h3>
                            <PieChart class="h-5 w-5 text-emerald-500" />
                        </div>
                        <div class="mt-4 max-h-[300px] overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                            <div v-for="item in leaveTypeAnalysis" :key="item.type" 
                                class="group space-y-2 rounded-lg p-3 transition-all duration-200 hover:bg-white/50 dark:hover:bg-gray-800/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div :class="[item.color, 'flex h-8 w-8 items-center justify-center rounded-lg']">
                                            <Calendar class="h-4 w-4 text-white" />
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ item.type }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                        {{ item.total_days }} days
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="space-y-1">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            Requests: <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.count }}</span>
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            Avg. Duration: <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.average_duration }} days</span>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            Longest: <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.longest_leave }} days</span>
                                        </div>
                                        <div class="text-gray-500 dark:text-gray-400">
                                            Shortest: <span class="font-medium text-gray-700 dark:text-gray-300">{{ item.shortest_leave }} days</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Department Comparison for Annual Leave -->
                                <div v-if="item.type.toLowerCase() === 'annual' && item.department_comparison" 
                                    class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Department Comparison
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <div class="space-y-1">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                Dept. Avg. Days: 
                                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                                    {{ item.department_comparison.department_avg_days }} days
                                                </span>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                Dept. Avg. Duration: 
                                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                                    {{ item.department_comparison.department_avg_duration }} days
                                                </span>
                                            </div>
                                        </div>
                                        <div class="space-y-1">
                                            <div class="text-gray-500 dark:text-gray-400">
                                                Your Rank: 
                                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                                    {{ item.department_comparison.user_rank.position }} of {{ item.department_comparison.user_rank.total }}
                                                </span>
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                Dept. Members: 
                                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                                    {{ item.department_comparison.department_total_users }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="leaveTypeAnalysis.length === 0" class="text-center py-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    No leave data available for this year
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Overview -->
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-gradient-to-br from-amber-50 to-white shadow-sm dark:from-amber-900/20 dark:to-gray-800 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-amber-900 dark:text-amber-100">Status Overview</h3>
                            <AlertCircle class="h-5 w-5 text-amber-500" />
                        </div>
                        <div class="mt-4 max-h-[300px] overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                            <div v-for="item in statusOverview" :key="item.status" class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div :class="[item.color, 'h-3 w-3 rounded-full']"></div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ item.status }}</span>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ item.count }}</span>
                            </div>
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-sm text-amber-600 dark:text-amber-400">
                                    Total Requests: {{ statusOverview.reduce((sum, item) => sum + item.count, 0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid flex-1 gap-4 md:grid-cols-3">
                <!-- Calendar Section -->
                <div class="md:col-span-2">
                    <div class="h-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="p-6">
                            <!-- Calendar Header with Navigation -->
                            <div class="mb-4 flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Leave Calendar</h3>
                                <div class="flex items-center gap-2">
                                    <button @click="navigateMonth('prev')"
                                        class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                                        <ChevronLeft class="h-5 w-5" />
                                    </button>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ currentMonth.toLocaleString('default', { month: 'long', year: 'numeric' }) }}
                                    </span>
                                    <button @click="navigateMonth('next')"
                                        class="rounded-lg border border-gray-200 bg-white p-2 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700">
                                        <ChevronRight class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                <!-- Calendar Header -->
                                <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                                    <div v-for="day in weekDays" :key="day"
                                        class="bg-gray-50 py-2 text-center text-sm font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                        {{ day }}
                                    </div>
                                </div>
                                <!-- Calendar Grid -->
                                <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700">
                                    <div v-for="day in calendarDays" :key="day.date"
                                        class="relative min-h-[100px] bg-white p-2 dark:bg-gray-800"
                                        :class="{
                                            'bg-gray-50 dark:bg-gray-900': !day.isCurrentMonth,
                                            'bg-indigo-50 dark:bg-indigo-900/20': day.hasLeave,
                                            'bg-emerald-50 dark:bg-emerald-900/20': day.isHoliday,
                                            'bg-red-50 dark:bg-red-900/20': day.isSunday && day.isCurrentMonth,
                                            'bg-yellow-50 dark:bg-yellow-900/20': day.isSaturday && day.isCurrentMonth
                                        }">
                                        <span class="text-sm" :class="{
                                            'text-gray-400 dark:text-gray-500': !day.isCurrentMonth,
                                            'font-semibold': day.isToday,
                                            'text-red-600 dark:text-red-400': day.isSunday && day.isCurrentMonth,
                                            'text-yellow-600 dark:text-yellow-400': day.isSaturday && day.isCurrentMonth
                                        }">{{ day.day }}</span>
                                        <div v-if="day.hasLeave" class="mt-1">
                                            <span
                                                class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200">
                                                Leave
                                            </span>
                                        </div>
                                        <div v-if="day.isHoliday" class="mt-1">
                                            <span
                                                class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200">
                                                Holiday
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Members on Leave Section -->
                <div class="md:col-span-1">
                    <div class="h-full overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Team Members on Leave</h3>
                                <Users class="h-5 w-5 text-gray-400" />
                            </div>
                            <div class="mt-4 space-y-4">
                                <div v-for="member in teamMembers.filter(m => m.isOnLeave)" :key="member.id"
                                    class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <img v-if="member.avatar" :src="member.avatar" :alt="member.name"
                                            class="h-10 w-10 rounded-full ring-2 ring-white dark:ring-gray-800" />
                                        <div v-else
                                            class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            {{ member.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                            {{ member.name }}
                                        </p>
                                        <p class="truncate text-sm text-gray-500 dark:text-gray-400">
                                            {{ member.designation }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">
                                            On Leave
                                        </span>
                                    </div>
                                </div>
                                <div v-if="!teamMembers.filter(m => m.isOnLeave).length"
                                    class="text-center py-4">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        No team members are currently on leave
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Requests -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">Recent Leave Requests</h3>
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li v-for="(request, index) in recentRequests" :key="request.id">
                                <div class="relative pb-8">
                                    <span v-if="index !== recentRequests.length - 1"
                                        class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700"
                                        aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span
                                                class="flex h-8 w-8 items-center justify-center rounded-full ring-8 ring-white dark:ring-gray-800"
                                                :class="{
                                                    'bg-amber-500': request.status === 'pending',
                                                    'bg-emerald-500': request.status === 'approved',
                                                    'bg-red-500': request.status === 'rejected'
                                                }">
                                                <Calendar class="h-5 w-5 text-white" />
                                            </span>
                                        </div>
                                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ request.type }} leave for {{ request.days }} days
                                                    <span class="font-medium text-gray-900 dark:text-white">
                                                        {{ formatDate(request.start_date) }}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                                    :class="getStatusColor(request.status)">
                                                    {{ request.status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.5);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(156, 163, 175, 0.7);
}

/* Dark mode scrollbar */
.dark .custom-scrollbar {
    scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: rgba(75, 85, 99, 0.5);
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(75, 85, 99, 0.7);
}
</style>
