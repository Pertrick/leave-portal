<template>
    <AppLayout title="Departments">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Departments
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Filters -->
                        <div class="mb-6 flex flex-wrap gap-4">
                            <div class="flex-1 min-w-[200px]">
                                <InputLabel for="search" value="Search" />
                                <TextInput
                                    id="search"
                                    v-model="search"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Search departments..."
                                />
                            </div>
                            <div class="w-[200px]">
                                <InputLabel for="status" value="Status" />
                                <SelectInput
                                    id="status"
                                    v-model="status"
                                    class="mt-1 block w-full"
                                >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </SelectInput>
                            </div>
                        </div>

                        <!-- Add Department Button -->
                        <div class="mb-6">
                            <PrimaryButton @click="openModal()">
                                Add Department
                            </PrimaryButton>
                        </div>

                        <!-- Departments Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="department in departments.data" :key="department.id">
                                        <td class="px-4 py-2 whitespace-nowrap">{{ department.name }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ department.description }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ department.location?.name || 'N/A' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <button
                                                    @click="toggleStatus(department)"
                                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                                    :class="department.status ? 'bg-indigo-600' : 'bg-gray-200'"
                                                    role="switch"
                                                    :aria-checked="department.status"
                                                >
                                                    <span
                                                        aria-hidden="true"
                                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                        :class="department.status ? 'translate-x-5' : 'translate-x-0'"
                                                    />
                                                </button>
                                                <span class="ml-2 text-sm text-gray-500">
                                                    {{ department.status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                            <button
                                                @click="openModal(department)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="confirmDelete(department)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            <Pagination :links="departments.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Department Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ editingDepartment ? 'Edit Department' : 'Add Department' }}
                </h2>

                <form @submit.prevent="submit">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <Textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="location" value="Location" />
                            <SelectInput
                                id="location"
                                v-model="form.location_id"
                                class="mt-1 block w-full"
                            >
                                <option value="">Select Location</option>
                                <option v-for="location in locations" :key="location.id" :value="location.id">
                                    {{ location.name }}
                                </option>
                            </SelectInput>
                            <InputError :message="form.errors.location_id" class="mt-2" />
                        </div>

                        <div>
                            <label class="flex items-center">
                                <Checkbox v-model:checked="form.status" />
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton type="button" @click="closeModal" class="mr-3">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ editingDepartment ? 'Update' : 'Create' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <ConfirmationModal
            :show="showDeleteModal"
            type="danger"
            title="Delete Department"
            message="Are you sure you want to delete this department? This action cannot be undone."
            confirm-text="Yes, Delete It"
            cancel-text="No, Keep It"
            @close="closeDeleteModal"
            @confirm="deleteDepartment"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import SelectInput from '@/Components/SelectInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import InputError from '@/Components/InputError.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

interface Department {
    id: number;
    name: string;
    description: string | null;
    status: boolean;
    location_id: string;
}

const props = defineProps<{
    departments: {
        data: Department[];
        links: any[];
    };
    filters: {
        search: string;
        status: string;
    };
    locations: {
        id: number;
        name: string;
    }[];
}>();

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const showModal = ref(false);
const showDeleteModal = ref(false);
const editingDepartment = ref<Department | null>(null);
const departmentToDelete = ref<Department | null>(null);

const form = useForm({
    name: '',
    description: '',
    location_id: '',
    status: true,
});

watch([search, status], () => {
    router.get(
        route('admin.departments.index'),
        {
            search: search.value,
            status: status.value
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true
        }
    );
});

const openModal = (department?: Department) => {
    editingDepartment.value = department || null;
    if (department) {
        form.name = department.name;
        form.description = department.description || '';
        form.location_id = department.location_id || '';
        form.status = department.status;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingDepartment.value = null;
    form.reset();
};

const submit = () => {
    if (editingDepartment.value) {
        form.put(route('admin.departments.update', editingDepartment.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal()
        });
    } else {
        form.post(route('admin.departments.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal()
        });
    }
};

const confirmDelete = (department: Department) => {
    departmentToDelete.value = department;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    departmentToDelete.value = null;
};

const deleteDepartment = () => {
    if (departmentToDelete.value) {
        router.delete(route('admin.departments.destroy', departmentToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => closeDeleteModal()
        });
    }
};

const toggleStatus = (department: Department) => {
    router.put(route('admin.departments.toggle-status', department.id), {
        preserveScroll: true
    });
};
</script> 