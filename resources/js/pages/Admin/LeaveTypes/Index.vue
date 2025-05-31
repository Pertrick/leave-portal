<template>
  <AppLayout title="Leave Types">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Leave Types
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
                  placeholder="Search leave types..."
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

            <!-- Add Leave Type Button -->
            <div class="mb-6">
              <PrimaryButton @click="showAddModal = true">
                Add Leave Type
              </PrimaryButton>
            </div>

            <!-- Leave Types Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medical Proof</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weekend Days</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="leaveType in leaveTypes.data" :key="leaveType.id">
                    <td class="px-4 py-2 whitespace-nowrap">{{ leaveType.name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      <span :class="{
                        'px-2 py-1 text-xs rounded-full': true,
                        'bg-green-100 text-green-800': leaveType.requires_medical_proof,
                        'bg-red-100 text-red-800': !leaveType.requires_medical_proof
                      }">
                        {{ leaveType.requires_medical_proof ? 'Required' : 'Not Required' }}
                      </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      <span v-if="leaveType.weekend_days.saturday && leaveType.weekend_days.sunday">Sat, Sun</span>
                      <span v-else-if="leaveType.weekend_days.saturday">Saturday</span>
                      <span v-else-if="leaveType.weekend_days.sunday">Sunday</span>
                      <span v-else>None</span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      <div class="flex items-center">
                        <button
                          @click="toggleStatus(leaveType)"
                          class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                          :class="leaveType.is_active ? 'bg-indigo-600' : 'bg-gray-200'"
                          role="switch"
                          :aria-checked="leaveType.is_active"
                        >
                          <span
                            aria-hidden="true"
                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            :class="leaveType.is_active ? 'translate-x-5' : 'translate-x-0'"
                          />
                        </button>
                        <span class="ml-2 text-sm text-gray-500">
                          {{ leaveType.is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </div>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="editLeaveType(leaveType)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </button>
                      <button
                        @click="deleteLeaveType(leaveType)"
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
              <Pagination :links="leaveTypes.links" :meta="leaveTypes.meta" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Leave Type Modal -->
    <Modal :show="showAddModal" @close="() => { showAddModal = false; resetForm(); }">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingLeaveType ? 'Edit Leave Type' : 'Add Leave Type' }}
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
              <label class="flex items-center">
                <Checkbox v-model:checked="form.requires_medical_proof" />
                <span class="ml-2 text-sm text-gray-600">Requires Medical Proof</span>
              </label>
            </div>

            <div>
              <InputLabel value="Weekend Days" />
              <div class="mt-2 space-y-2">
                <label class="flex items-center">
                  <Checkbox v-model:checked="form.weekend_days.saturday" />
                  <span class="ml-2 text-sm text-gray-600">Saturday</span>
                </label>
                <label class="flex items-center">
                  <Checkbox v-model:checked="form.weekend_days.sunday" />
                  <span class="ml-2 text-sm text-gray-600">Sunday</span>
                </label>
              </div>
            </div>

            <div>
              <label class="flex items-center">
                <Checkbox v-model:checked="form.is_active" />
                <span class="ml-2 text-sm text-gray-600">Active</span>
              </label>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <SecondaryButton type="button" @click="showAddModal = false" class="mr-3">
              Cancel
            </SecondaryButton>
            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              {{ editingLeaveType ? 'Update' : 'Create' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      type="danger"
      title="Delete Leave Type"
      message="Are you sure you want to delete this leave type? This action cannot be undone."
      confirm-text="Yes, Delete It"
      cancel-text="No, Keep It"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import Checkbox from '@/Components/Checkbox.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import { useFlash } from '@/composables/useFlash'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const { flash } = useFlash()

const props = defineProps({
  leaveTypes: Object,
  filters: {
    type: Object,
    default: () => ({
      search: '',
      status: ''
    })
  }
})

const showAddModal = ref(false)
const showDeleteModal = ref(false)
const leaveTypeToDelete = ref(null)
const editingLeaveType = ref(null)
const search = ref(props.filters.search || '')
const status = ref(props.filters.status || '')

const form = useForm({
  name: '',
  requires_medical_proof: false,
  weekend_days: {
    saturday: false,
    sunday: false
  },
  is_active: true
})

watch([search, status], () => {
  router.get(
    route('leave.types.index'),
    {
      search: search.value,
      status: status.value
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  )
})

const resetForm = () => {
  editingLeaveType.value = null
  form.reset()
  form.requires_medical_proof = false
  form.weekend_days = {
    saturday: false,
    sunday: false
  }
  form.is_active = true
}

const editLeaveType = (leaveType) => {
  resetForm()
  editingLeaveType.value = leaveType
  form.name = leaveType.name
  form.requires_medical_proof = leaveType.requires_medical_proof
  form.weekend_days = leaveType.weekend_days
  form.is_active = leaveType.is_active
  showAddModal.value = true
}

const deleteLeaveType = (leaveType) => {
  leaveTypeToDelete.value = leaveType
  showDeleteModal.value = true
}

const confirmDelete = () => {
  router.delete(route('leave.types.destroy', leaveTypeToDelete.value.id), {
    preserveScroll: true
  })
  showDeleteModal.value = false
  leaveTypeToDelete.value = null
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  leaveTypeToDelete.value = null
}

const toggleStatus = (leaveType) => {
  router.put(route('leave.types.update', leaveType.id), {
    ...leaveType,
    is_active: !leaveType.is_active
  }, {
    preserveScroll: true
  })
}

const submit = () => {
  if (editingLeaveType.value) {
    form.put(route('leave.types.update', editingLeaveType.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showAddModal.value = false
        resetForm()
      }
    })
  } else {
    form.post(route('leave.types.store'), {
      preserveScroll: true,
      onSuccess: () => {
        showAddModal.value = false
        resetForm()
      }
    })
  }
}
</script> 