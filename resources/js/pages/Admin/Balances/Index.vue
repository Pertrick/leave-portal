<template>
    <AppLayout title="Leave Balances">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Leave Balances
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <InputLabel for="search" value="Search" />
                                <TextInput
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Search by name or staff ID..."
                                />
                            </div>
                            <div>
                                <InputLabel for="department" value="Department" />
                                <SelectInput
                                    id="department"
                                    v-model="department"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Departments</option>
                                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                        {{ dept.name }}
                                    </option>
                                </SelectInput>
                            </div>
                            <div>
                                <InputLabel for="leaveType" value="Leave Type" />
                                <SelectInput
                                    id="leaveType"
                                    v-model="leaveType"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Leave Types</option>
                                    <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </SelectInput>
                            </div>
                            <div>
                                <InputLabel for="year" value="Year" />
                                <SelectInput
                                    id="year"
                                    v-model="year"
                                    class="mt-1 block w-full"
                                >
                                    <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                                </SelectInput>
                            </div>
                        </div>

                        <!-- Quick Filters and Reset -->
                        <div class="mb-4 flex flex-wrap gap-2 items-center">
                            <div class="flex gap-2">
                                <button 
                                    @click="setStatusFilter('all')"
                                    class="px-3 py-1 text-sm rounded-full"
                                    :class="statusFilter === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'"
                                >
                                    All
                                </button>
                                <button 
                                    @click="setStatusFilter('available')"
                                    class="px-3 py-1 text-sm rounded-full"
                                    :class="statusFilter === 'available' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                >
                                    Available
                                </button>
                                <button 
                                    @click="setStatusFilter('exhausted')"
                                    class="px-3 py-1 text-sm rounded-full"
                                    :class="statusFilter === 'exhausted' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'"
                                >
                                    Exhausted
                                </button>
                            </div>
                            <button 
                                @click="resetFilters"
                                class="ml-auto px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 flex items-center"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Filters
                            </button>
                        </div>

                        <!-- Export Button -->
                        <div class="mb-6 flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                Showing {{ filteredBalances.length }} of {{ balances.data.length }} records
                            </div>
                            <SecondaryButton @click="exportData" class="mr-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export
                            </SecondaryButton>
                        </div>

                        <!-- Leave Balances Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('name')">
                                            Staff
                                            <span v-if="sortKey === 'name'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('department')">
                                            Department
                                            <span v-if="sortKey === 'department'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('leaveType')">
                                            Leave Type
                                            <span v-if="sortKey === 'leaveType'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('year')">
                                            Year
                                            <span v-if="sortKey === 'year'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('entitled')">
                                            Entitled Days
                                            <span v-if="sortKey === 'entitled'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('taken')">
                                            Days Taken
                                            <span v-if="sortKey === 'taken'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('remaining')">
                                            Remaining Days
                                            <span v-if="sortKey === 'remaining'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            @click="sortBy('status')">
                                            Status
                                            <span v-if="sortKey === 'status'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '↑' : '↓' }}
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="balance in filteredBalances" :key="balance.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <UserAvatar
                                                        :src="balance.user.avatar"
                                                        :user="balance.user"
                                                        size="md"
                                                    />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ balance.user.firstname }} {{ balance.user.lastname }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ balance.user.email }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ balance.user.staff_id }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ balance.user.department?.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ balance.leave_type.name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ balance.year }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ balance.total_entitled_days }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ balance.days_taken }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ getRemainingDays(balance) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="getStatusClass(balance)"
                                            >
                                                {{ getStatusText(balance) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="balances.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import UserAvatar from '@/Components/UserAvatar.vue';

interface User {
    id: number;
    firstname: string;
    lastname: string;
    staff_id: string;
    avatar: string | null;
    department: {
        id: number;
        name: string;
    } | null;
}

interface LeaveType {
    id: number;
    name: string;
}

interface LeaveBalance {
    id: number;
    user: User;
    leave_type: LeaveType;
    year: number;
    total_entitled_days: number;
    days_taken: number;
    total_days: number;
    used_days: number;
}

interface Department {
    id: number;
    name: string;
}

const props = defineProps<{
    balances: {
        data: LeaveBalance[];
        links: any[];
    };
    departments: Department[];
    leaveTypes: LeaveType[];
}>();

const search = ref('');
const department = ref('');
const leaveType = ref('');
const year = ref(new Date().getFullYear());

// Generate years array (current year and 2 years before)
const years = Array.from({ length: 3 }, (_, i) => new Date().getFullYear() - i);

const statusFilter = ref('all');
const sortKey = ref('name');
const sortOrder = ref('asc');

const setStatusFilter = (status: string) => {
    statusFilter.value = status;
};

const sortBy = (key: string) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
    }
};

const filteredBalances = computed(() => {
    let filtered = [...props.balances.data];

    // Apply status filter
    if (statusFilter.value !== 'all') {
        filtered = filtered.filter(balance => {
            const remaining = getRemainingDays(balance);
            return statusFilter.value === 'available' ? remaining > 0 : remaining <= 0;
        });
    }

    // Apply sorting
    filtered.sort((a, b) => {
        let aValue, bValue;
        switch (sortKey.value) {
            case 'name':
                aValue = `${a.user.firstname} ${a.user.lastname}`;
                bValue = `${b.user.firstname} ${b.user.lastname}`;
                break;
            case 'department':
                aValue = a.user.department?.name || '';
                bValue = b.user.department?.name || '';
                break;
            case 'leaveType':
                aValue = a.leave_type.name;
                bValue = b.leave_type.name;
                break;
            case 'year':
                aValue = a.year;
                bValue = b.year;
                break;
            case 'entitled':
                aValue = a.total_entitled_days;
                bValue = b.total_entitled_days;
                break;
            case 'taken':
                aValue = a.days_taken;
                bValue = b.days_taken;
                break;
            case 'remaining':
                aValue = getRemainingDays(a);
                bValue = getRemainingDays(b);
                break;
            case 'status':
                aValue = getRemainingDays(a) > 0 ? 'Available' : 'Exhausted';
                bValue = getRemainingDays(b) > 0 ? 'Available' : 'Exhausted';
                break;
            default:
                return 0;
        }

        if (sortOrder.value === 'asc') {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });

    return filtered;
});

watch([search, department, leaveType, year], () => {
    router.get(
        route('leave.balances.index'),
        {
            search: search.value,
            department: department.value,
            leaveType: leaveType.value,
            year: year.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const exportData = () => {
    const params = new URLSearchParams({
        search: search.value,
        department: department.value,
        leaveType: leaveType.value,
        year: year.value.toString()
    });
    
    window.location.href = `${route('leave.balances.export')}?${params.toString()}`;
};

const getRemainingDays = (balance: LeaveBalance) => {
    return balance.total_entitled_days - balance.days_taken;
};

const getStatusText = (balance: LeaveBalance) => {
    return getRemainingDays(balance) > 0 ? 'Available' : 'Exhausted';
};

const getStatusClass = (balance: LeaveBalance) => {
    return {
        'bg-green-100 text-green-800': getRemainingDays(balance) > 0,
        'bg-red-100 text-red-800': getRemainingDays(balance) <= 0
    };
};

const resetFilters = () => {
    search.value = '';
    department.value = '';
    leaveType.value = '';
    year.value = new Date().getFullYear();
    statusFilter.value = 'all';
    sortKey.value = 'name';
    sortOrder.value = 'asc';

    // Trigger the router request to update the data
    router.get(
        route('leave.balances.index'),
        {
            search: '',
            department: '',
            leaveType: '',
            year: new Date().getFullYear()
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
};
</script> 