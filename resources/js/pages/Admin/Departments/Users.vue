<template>
    <AppLayout :title="`${department.name} - Users`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ department.name }} - Users
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Actions -->
                <div class="mb-6 flex items-center justify-between">
                    <SecondaryButton @click="router.visit(route('admin.departments.index'))">
                        ← Back to Departments
                    </SecondaryButton>
                    <PrimaryButton @click="exportUsers">
                        Export Users
                    </PrimaryButton>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Department Info -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Department Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Name:</span>
                                    <span class="ml-2 text-gray-900">{{ department.name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Location:</span>
                                    <span class="ml-2 text-gray-900">{{ department.location?.name || 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Status:</span>
                                    <span class="ml-2" :class="department.status ? 'text-green-600' : 'text-red-600'">
                                        {{ department.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div v-if="department.description" class="mt-2">
                                <span class="font-medium text-gray-700">Description:</span>
                                <span class="ml-2 text-gray-900">{{ department.description }}</span>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 flex flex-wrap gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <InputLabel for="search" value="Search Users" />
                                <TextInput
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Search by name, email, or staff ID..."
                                />
                            </div>
                            <div class="w-[200px]">
                                <InputLabel for="user_level" value="User Level" />
                                <SelectInput
                                    id="user_level"
                                    v-model="userLevelId"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Levels</option>
                                    <option v-for="level in userLevels" :key="level.id" :value="level.id">
                                        {{ level.name }} (Level {{ level.level }})
                                    </option>
                                </SelectInput>
                            </div>
                            <div class="w-[200px]">
                                <InputLabel for="status" value="Status" />
                                <SelectInput
                                    id="status"
                                    v-model="status"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </SelectInput>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ users.total }}</div>
                                <div class="text-sm text-blue-700">Total Users</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ users.data.filter(u => u.is_active).length }}
                                </div>
                                <div class="text-sm text-green-700">Active Users</div>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ users.data.filter(u => !u.is_active).length }}
                                </div>
                                <div class="text-sm text-yellow-700">Inactive Users</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ userLevels.length }}
                                </div>
                                <div class="text-sm text-purple-700">User Levels</div>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Designation</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ user.staff_id }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                                    <span class="text-sm font-medium text-indigo-600">
                                                        {{ user.firstname[0] }}{{ user.lastname[0] }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ user.firstname }} {{ user.lastname }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ user.username }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ user.email }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                      :class="getLevelBadgeClass(user.user_level?.level)">
                                                    {{ user.user_level?.name || 'N/A' }}
                                                </span>
                                                <span class="ml-1 text-xs text-gray-500">({{ user.user_level?.level || 'N/A' }})</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ user.designation || 'N/A' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ formatDate(user.join_date) }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                {{ user.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                @click="viewUserDetails(user)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                View
                                            </button>
                                            <button
                                                @click="editUser(user)"
                                                class="text-blue-600 hover:text-blue-900 mr-3"
                                            >
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Empty State -->
                        <div v-if="users.data.length === 0" class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                            <p class="text-gray-500">No users match your current filters.</p>
                        </div>

                        <!-- Pagination -->
                        <div v-if="users.data.length > 0" class="mt-6">
                            <Pagination :links="users.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details Modal -->
        <Modal :show="showUserModal" @close="closeUserModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">User Details</h2>
                <div v-if="selectedUser" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedUser.firstname }} {{ selectedUser.lastname }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Staff ID</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedUser.staff_id }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedUser.email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedUser.phone || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">User Level</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedUser.user_level?.name }} (Level {{ selectedUser.user_level?.level }})</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Designation</label>
                            <p class="mt-1 text-sm text-gray-900">{{ selectedUser.designation || 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Join Date</label>
                            <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedUser.join_date) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <p class="mt-1 text-sm" :class="selectedUser.is_active ? 'text-green-600' : 'text-red-600'">
                                {{ selectedUser.is_active ? 'Active' : 'Inactive' }}
                            </p>
                        </div>
                    </div>
                    <div v-if="selectedUser.address">
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ selectedUser.address }}</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeUserModal">Close</SecondaryButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import SelectInput from '@/components/SelectInput.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import Modal from '@/components/Modal.vue';
import Pagination from '@/components/Pagination.vue';

interface User {
    id: number;
    staff_id: string;
    username: string;
    firstname: string;
    lastname: string;
    email: string;
    phone: string | null;
    address: string | null;
    designation: string | null;
    join_date: string;
    is_active: boolean;
    user_level: {
        id: number;
        name: string;
        level: number;
    } | null;
}

interface Department {
    id: number;
    name: string;
    description: string | null;
    status: boolean;
    location: {
        id: number;
        name: string;
    } | null;
}

interface UserLevel {
    id: number;
    name: string;
    level: number;
}

const props = defineProps<{
    department: Department;
    users: {
        data: User[];
        links: any[];
        total: number;
    };
    userLevels: UserLevel[];
    filters: {
        search: string;
        user_level_id: string;
        status: string;
    };
}>();

const search = ref(props.filters.search || '');
const userLevelId = ref(props.filters.user_level_id || '');
const status = ref(props.filters.status || '');
const showUserModal = ref(false);
const selectedUser = ref<User | null>(null);

watch([search, userLevelId, status], () => {
    router.get(
        route('admin.departments.users', props.department.id),
        {
            search: search.value,
            user_level_id: userLevelId.value,
            status: status.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const getLevelBadgeClass = (level: number | undefined) => {
    if (!level) return 'bg-gray-100 text-gray-800';
    
    switch (level) {
        case 1: return 'bg-blue-100 text-blue-800';
        case 2: return 'bg-green-100 text-green-800';
        case 3: return 'bg-purple-100 text-purple-800';
        case 4: return 'bg-yellow-100 text-yellow-800';
        case 5: return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const viewUserDetails = (user: User) => {
    selectedUser.value = user;
    showUserModal.value = true;
};

const closeUserModal = () => {
    showUserModal.value = false;
    selectedUser.value = null;
};

const editUser = (user: User) => {
    router.visit(route('staff.edit', user.id));
};

const exportUsers = () => {
    const params = new URLSearchParams({
        search: search.value,
        user_level_id: userLevelId.value,
        status: status.value
    });
    
    window.open(`/admin/departments/${props.department.id}/users/export?${params.toString()}`, '_blank');
};
</script> 