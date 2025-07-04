<template>
    <AppLayout title="Leave Entitlements">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Leave Entitlements Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters and Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <InputLabel for="search" value="Search" />
                                <TextInput
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Search by user level or leave type..."
                                />
                            </div>
                            <div>
                                <InputLabel for="user_level" value="User Level" />
                                <SelectInput
                                    id="user_level"
                                    v-model="userLevelFilter"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Levels</option>
                                    <option v-for="level in userLevels" :key="level.id" :value="level.id">
                                        {{ level.name }} (Level {{ level.level }})
                                    </option>
                                </SelectInput>
                            </div>
                            <div>
                                <InputLabel for="leave_type" value="Leave Type" />
                                <SelectInput
                                    id="leave_type"
                                    v-model="leaveTypeFilter"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Types</option>
                                    <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </SelectInput>
                            </div>
                            <div>
                                <InputLabel for="status" value="Status" />
                                <SelectInput
                                    id="status"
                                    v-model="statusFilter"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </SelectInput>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-3">
                                <PrimaryButton @click="openModal()">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Add Entitlement
                                </PrimaryButton>
                                <SecondaryButton @click="openBulkModal()">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Bulk Update
                                </SecondaryButton>
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ entitlements.data?.length || 0 }} entitlements
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entitlements Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days/Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Carry Over</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Carry Over</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-if="!entitlements.data?.length" class="text-center">
                                    <td colspan="7" class="px-6 py-12 text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <p class="text-lg font-medium text-gray-900 mb-2">No entitlements found</p>
                                            <p class="text-gray-500">Try adjusting your filters or create a new entitlement</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-else v-for="entitlement in entitlements.data" :key="entitlement.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-indigo-600">{{ entitlement.user_level.level }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ entitlement.user_level.name }}</div>
                                                <div class="text-sm text-gray-500">Level {{ entitlement.user_level.level }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ entitlement.leave_type.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-medium">{{ entitlement.days_per_year }}</span> days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="entitlement.can_carry_over" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Yes
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            No
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span v-if="entitlement.can_carry_over" class="font-medium">{{ entitlement.max_carry_over_days }}</span>
                                        <span v-else class="text-gray-500">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="entitlement.is_active" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                        <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button @click="openModal(entitlement)" class="text-indigo-600 hover:text-indigo-900">
                                                Edit
                                            </button>
                                            <button @click="toggleStatus(entitlement)" class="text-yellow-600 hover:text-yellow-900">
                                                {{ entitlement.is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                            <button @click="confirmDelete(entitlement)" class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <Pagination :links="entitlements.links" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Entitlement Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingEntitlement ? 'Edit Leave Entitlement' : 'Add Leave Entitlement' }}
                </h2>

                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="user_level_id" value="User Level" />
                                <SelectInput
                                    id="user_level_id"
                                    v-model="form.user_level_id"
                                    class="mt-1 block w-full"
                                    required
                                >
                                    <option value="">Select User Level</option>
                                    <option v-for="level in userLevels" :key="level.id" :value="level.id">
                                        {{ level.name }} (Level {{ level.level }})
                                    </option>
                                </SelectInput>
                                <InputError :message="form.errors.user_level_id" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="leave_type_id" value="Leave Type" />
                                <SelectInput
                                    id="leave_type_id"
                                    v-model="form.leave_type_id"
                                    class="mt-1 block w-full"
                                    required
                                >
                                    <option value="">Select Leave Type</option>
                                    <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </SelectInput>
                                <InputError :message="form.errors.leave_type_id" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="days_per_year" value="Days Per Year" />
                                <TextInput
                                    id="days_per_year"
                                    v-model="form.days_per_year"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.days_per_year" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="max_carry_over_days" value="Max Carry Over Days" />
                                <TextInput
                                    id="max_carry_over_days"
                                    v-model="form.max_carry_over_days"
                                    type="number"
                                    min="0"
                                    class="mt-1 block w-full"
                                    :disabled="!form.can_carry_over"
                                />
                                <InputError :message="form.errors.max_carry_over_days" class="mt-2" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.can_carry_over" />
                                <span class="ml-2 text-sm text-gray-600">Allow Carry Over</span>
                            </label>
                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.is_active" />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton type="button" @click="closeModal" class="mr-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ editingEntitlement ? 'Update' : 'Create' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Bulk Update Modal -->
        <Modal :show="showBulkModal" @close="closeBulkModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Bulk Update Entitlements</h2>

                <form @submit.prevent="submitBulk">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="bulk_user_level_id" value="User Level" />
                            <SelectInput
                                id="bulk_user_level_id"
                                v-model="bulkForm.user_level_id"
                                class="mt-1 block w-full"
                                required
                                @change="loadBulkEntitlements"
                            >
                                <option value="">Select User Level</option>
                                <option v-for="level in userLevels" :key="level.id" :value="level.id">
                                    {{ level.name }} (Level {{ level.level }})
                                </option>
                            </SelectInput>
                            <InputError :message="bulkForm.errors.user_level_id" class="mt-2" />
                        </div>

                        <div v-if="bulkForm.user_level_id" class="space-y-4">
                            <h3 class="text-sm font-medium text-gray-900">Leave Type Entitlements</h3>
                            <div class="space-y-3">
                                <div v-for="type in leaveTypes" :key="type.id" class="border rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900">{{ type.name }}</h4>
                                        <label class="flex items-center">
                                            <Checkbox v-model="bulkForm.entitlements[type.id].is_active" />
                                            <span class="ml-2 text-xs text-gray-600">Active</span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <InputLabel :for="`days_${type.id}`" value="Days/Year" />
                                            <TextInput
                                                :id="`days_${type.id}`"
                                                v-model="bulkForm.entitlements[type.id].days_per_year"
                                                type="number"
                                                min="0"
                                                class="mt-1 block w-full"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel :for="`carry_over_${type.id}`" value="Max Carry Over" />
                                            <TextInput
                                                :id="`carry_over_${type.id}`"
                                                v-model="bulkForm.entitlements[type.id].max_carry_over_days"
                                                type="number"
                                                min="0"
                                                class="mt-1 block w-full"
                                                :disabled="!bulkForm.entitlements[type.id].can_carry_over"
                                            />
                                        </div>
                                        <div class="flex items-end">
                                            <label class="flex items-center">
                                                <Checkbox v-model="bulkForm.entitlements[type.id].can_carry_over" />
                                                <span class="ml-2 text-xs text-gray-600">Allow Carry Over</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton type="button" @click="closeBulkModal" class="mr-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': bulkForm.processing }" :disabled="bulkForm.processing">
                            Update All
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            type="danger"
            title="Delete Leave Entitlement"
            message="Are you sure you want to delete this leave entitlement? This action cannot be undone."
            confirm-text="Yes, Delete It"
            cancel-text="No, Keep It"
            @close="closeDeleteModal"
            @confirm="deleteEntitlement"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import InputLabel from '@/components/InputLabel.vue';
import TextInput from '@/components/TextInput.vue';
import SelectInput from '@/components/SelectInput.vue';
import Checkbox from '@/components/Checkbox.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import Modal from '@/components/Modal.vue';
import Pagination from '@/components/Pagination.vue';
import InputError from '@/components/InputError.vue';
import ConfirmationModal from '@/components/ConfirmationModal.vue';

interface UserLevel {
    id: number;
    name: string;
    level: number;
}

interface LeaveType {
    id: number;
    name: string;
}

interface LeaveEntitlement {
    id: number;
    user_level_id: number;
    leave_type_id: number;
    days_per_year: number;
    can_carry_over: boolean;
    max_carry_over_days: number;
    is_active: boolean;
    user_level: UserLevel;
    leave_type: LeaveType;
}

interface EntitlementsResponse {
    data: LeaveEntitlement[];
    links: any[];
}

const props = defineProps<{
    entitlements: EntitlementsResponse;
    userLevels: UserLevel[];
    leaveTypes: LeaveType[];
    filters: {
        search: string;
        user_level_id: string;
        leave_type_id: string;
        status: string;
    };
}>();

// Filters
const search = ref(props.filters.search || '');
const userLevelFilter = ref(props.filters.user_level_id || '');
const leaveTypeFilter = ref(props.filters.leave_type_id || '');
const statusFilter = ref(props.filters.status || '');

// Modal states
const showModal = ref(false);
const showBulkModal = ref(false);
const showDeleteModal = ref(false);
const editingEntitlement = ref<LeaveEntitlement | null>(null);
const entitlementToDelete = ref<LeaveEntitlement | null>(null);

// Forms
const form = useForm({
    user_level_id: '',
    leave_type_id: '',
    days_per_year: 0,
    can_carry_over: false,
    max_carry_over_days: 0,
    is_active: true,
});

const bulkForm = useForm({
    user_level_id: '',
    entitlements: {} as Record<number, {
        leave_type_id: number;
        days_per_year: number;
        can_carry_over: boolean;
        max_carry_over_days: number;
        is_active: boolean;
    }>,
});

// Watch filters for changes
watch([search, userLevelFilter, leaveTypeFilter, statusFilter], () => {
    router.get(
        route('admin.leave-entitlements.index'),
        {
            search: search.value,
            user_level_id: userLevelFilter.value,
            leave_type_id: leaveTypeFilter.value,
            status: statusFilter.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const openModal = (entitlement?: LeaveEntitlement) => {
    editingEntitlement.value = entitlement || null;
    if (entitlement) {
        form.user_level_id = entitlement.user_level_id.toString();
        form.leave_type_id = entitlement.leave_type_id.toString();
        form.days_per_year = entitlement.days_per_year;
        form.can_carry_over = entitlement.can_carry_over;
        form.max_carry_over_days = entitlement.max_carry_over_days;
        form.is_active = entitlement.is_active;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingEntitlement.value = null;
    form.reset();
};

const openBulkModal = () => {
    showBulkModal.value = true;
};

const closeBulkModal = () => {
    showBulkModal.value = false;
    bulkForm.reset();
};

const loadBulkEntitlements = () => {
    if (!bulkForm.user_level_id) return;

    // Initialize entitlements for all leave types
    bulkForm.entitlements = {};
    props.leaveTypes.forEach(type => {
        const existing = props.entitlements.data.find(e => 
            e.user_level_id.toString() === bulkForm.user_level_id && 
            e.leave_type_id === type.id
        );

        bulkForm.entitlements[type.id] = {
            leave_type_id: type.id,
            days_per_year: existing?.days_per_year || 0,
            can_carry_over: existing?.can_carry_over || false,
            max_carry_over_days: existing?.max_carry_over_days || 0,
            is_active: existing?.is_active || true,
        };
    });
};

const submit = () => {
    if (editingEntitlement.value) {
        form.put(route('admin.leave-entitlements.update', editingEntitlement.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.leave-entitlements.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal()
        });
    }
};

const submitBulk = () => {
    bulkForm.post(route('admin.leave-entitlements.bulk-update'), {
        preserveScroll: true,
        onSuccess: () => closeBulkModal()
    });
};

const toggleStatus = (entitlement: LeaveEntitlement) => {
    router.put(route('admin.leave-entitlements.toggle-status', entitlement.id), {}, {
        preserveScroll: true
    });
};

const confirmDelete = (entitlement: LeaveEntitlement) => {
    entitlementToDelete.value = entitlement;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    entitlementToDelete.value = null;
};

const deleteEntitlement = () => {
    if (entitlementToDelete.value) {
        router.delete(route('admin.leave-entitlements.destroy', entitlementToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeDeleteModal()
        });
    }
};
</script> 