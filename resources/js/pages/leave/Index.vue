<template>
  <AppLayout title="Leave Applications">


    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <ClockIcon class="w-6 h-6" />
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending</h3>
                <p class="text-2xl font-bold text-blue-600">
                  {{ leaves.filter(l => l.status === 'pending').length }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-green-100 text-green-600">
                <CheckCircleIcon class="w-6 h-6" />
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Approved</h3>
                <p class="text-2xl font-bold text-green-600">
                  {{ leaves.filter(l => l.status === 'approved').length }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-red-100 text-red-600">
                <XCircleIcon class="w-6 h-6" />
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Rejected</h3>
                <p class="text-2xl font-bold text-red-600">
                  {{ leaves.filter(l => l.status === 'rejected').length }}
                </p>
              </div>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                <XCircleIcon class="w-6 h-6" />
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Cancelled</h3>
                <p class="text-2xl font-bold text-gray-600">
                  {{ leaves.filter(l => l.status === 'cancelled').length }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center space-x-3">
            <button
              @click="showAllManualAdjustments"
              class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md"
            >
              <DocumentIcon class="w-4 h-4 mr-2" />
              Manual Adjustments ({{ leaves.filter(l => l.type === 'manual_adjustment').length }})
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <Link
              :href="route('leaves.drafts')"
              class="inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm"
              title="Drafts"
            >
              <DocumentIcon class="w-5 h-5" />
            </Link>
            <Link
              :href="route('leaves.create')"
              class="inline-flex items-center justify-center w-10 h-10 bg-indigo-600 border border-transparent rounded-md text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm"
              title="New Application"
            >
              <PlusIcon class="w-5 h-5" />
            </Link>
          </div>
        </div>

        <!-- Leave Applications Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
              <div class="flex space-x-2">
                <select
                  v-model="statusFilter"
                  class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                  <option value="cancelled">Cancelled</option>
                  <option value="completed">Manual Adjustments</option>
                </select>
                <select
                  v-model="typeFilter"
                  class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Types</option>
                  <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Dates
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Duration
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="leave in filteredLeaves" :key="leave.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">
                        <!-- Handle both regular leaves and manual adjustments -->
                        <div v-if="leave.type === 'manual_adjustment'" class="flex items-center">
                          <span class="text-orange-600 font-semibold">{{ leave.title }}</span>
                          <span class="ml-2 text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full">Manual</span>
                        </div>
                        <div v-else>
                          {{ leave.leave_type.name }}
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        <!-- Show dates for regular leaves, adjustment date for manual adjustments -->
                        <div v-if="leave.type === 'manual_adjustment'">
                          {{ formatDate(leave.created_at) }}
                        </div>
                        <div v-else>
                          {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        <!-- Show days for both types -->
                        <div v-if="leave.type === 'manual_adjustment'" class="flex items-center">
                          <span :class="leave.is_deduction ? 'text-red-600' : 'text-green-600'">
                            {{ leave.is_deduction ? '-' : '+' }}{{ leave.days }} days
                          </span>
                          <span class="ml-2 text-xs text-gray-500">
                            ({{ leave.leave_type }})
                          </span>
                        </div>
                        <div v-else>
                          {{ leave.calendar_days }} calendar days
                          <span class="text-gray-500">({{ leave.working_days }} working days)</span>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                          'bg-gray-100 text-gray-800': leave.status === 'cancelled',
                          'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                          'bg-green-100 text-green-800': leave.status === 'approved',
                          'bg-red-100 text-red-800': leave.status === 'rejected',
                          'bg-orange-100 text-orange-800': leave.type === 'manual_adjustment'
                        }"
                      >
                        {{ leave.type === 'manual_adjustment' ? 'Completed' : leave.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <!-- Show different actions based on type -->
                        <div v-if="leave.type === 'manual_adjustment'" class="text-sm text-gray-500">
                          <div>By: {{ leave.adjusted_by }}</div>
                          <div class="text-xs">{{ leave.reason }}</div>
                          <button
                            @click="openManualAdjustmentModal(leave)"
                            class="text-indigo-600 hover:text-indigo-900 text-xs"
                          >
                            View Details
                          </button>
                        </div>
                        <div v-else class="flex space-x-2">
                          <Link
                            :href="route('leaves.show', leave.uuid)"
                            class="text-indigo-600 hover:text-indigo-900"
                          >
                            View
                          </Link>
                          <Link
                            v-if="leave.status === 'pending'"
                            :href="route('leaves.edit', leave.uuid)"
                            class="text-blue-600 hover:text-blue-900"
                          >
                            Edit
                          </Link>
                          <button
                            v-if="leave.status === 'pending'"
                            @click="confirmDelete(leave)"
                            class="text-red-600 hover:text-red-900"
                          >
                            Cancel
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      :show="showDeleteModal"
      type="danger"
      title="Cancel Leave Application"
      message="Are you sure you want to cancel this leave application? This action cannot be undone."
      confirm-text="Yes, Cancel It"
      cancel-text="No, Keep It"
      @close="closeDeleteModal"
      @confirm="deleteLeave"
    />

    <!-- Manual Adjustment Details Modal -->
    <Modal :show="showManualAdjustmentModal" @close="closeManualAdjustmentModal" max-width="2xl">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-lg font-semibold text-gray-900">{{ selectedManualAdjustment?.title }}</h3>
          <button
            @click="closeManualAdjustmentModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>

        <div v-if="selectedManualAdjustment" class="space-y-6">
          <!-- Adjustment Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Leave Type</h4>
              <p class="text-lg font-semibold text-gray-900">{{ selectedManualAdjustment.leave_type }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Days Adjusted</h4>
              <p class="text-lg font-semibold" :class="selectedManualAdjustment.is_deduction ? 'text-red-600' : 'text-green-600'">
                {{ selectedManualAdjustment.is_deduction ? '-' : '+' }}{{ selectedManualAdjustment.days }} days
              </p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Previous Balance</h4>
              <p class="text-lg font-semibold text-gray-900">{{ selectedManualAdjustment.previous_balance }} days</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
              <h4 class="text-sm font-medium text-gray-700 mb-2">New Balance</h4>
              <p class="text-lg font-semibold text-gray-900">{{ selectedManualAdjustment.new_balance }} days</p>
            </div>
          </div>

          <!-- Adjusted By -->
          <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Adjusted By</h4>
            <p class="text-lg font-semibold text-gray-900">{{ selectedManualAdjustment.adjusted_by }}</p>
          </div>

          <!-- Date -->
          <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Date</h4>
            <p class="text-lg font-semibold text-gray-900">{{ formatDate(selectedManualAdjustment.created_at) }}</p>
          </div>

          <!-- Reason -->
          <div>
            <h4 class="text-sm font-medium text-gray-700 mb-2">Reason for Adjustment</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
              <p class="text-gray-900">{{ selectedManualAdjustment.reason }}</p>
            </div>
          </div>

          <!-- Impact Summary -->
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Impact Summary</h4>
            <p class="text-sm text-blue-700">
              This adjustment {{ selectedManualAdjustment.is_deduction ? 'reduced' : 'increased' }} your 
              <strong>{{ selectedManualAdjustment.leave_type }}</strong> balance by 
              <strong>{{ selectedManualAdjustment.days }} days</strong>.
              {{ selectedManualAdjustment.is_deduction 
                ? 'This means you have fewer days available for this leave type.' 
                : 'This means you have more days available for this leave type.' 
              }}
            </p>
          </div>
        </div>

        <div class="flex justify-end mt-6">
          <SecondaryButton @click="closeManualAdjustmentModal">
            Close
          </SecondaryButton>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, computed, getCurrentInstance } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { PlusIcon, ClockIcon, CheckCircleIcon, XCircleIcon, DocumentIcon, XMarkIcon } from '@heroicons/vue/24/outline/index.js'
import { useFlash } from '@/composables/useFlash'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const { flash } = useFlash()
const { proxy } = getCurrentInstance()

const props = defineProps({
  leaves: Array,
  leaveTypes: Array,
})

const statusFilter = ref('')
const typeFilter = ref('')
const showDeleteModal = ref(false)
const leaveToDelete = ref(null)
const showManualAdjustmentModal = ref(false)
const selectedManualAdjustment = ref(null)

const filteredLeaves = computed(() => {
  return props.leaves.filter(leave => {
    // Handle both regular leaves and manual adjustments
    let statusMatch = true;
    let typeMatch = true;
    
    if (statusFilter.value) {
      if (leave.type === 'manual_adjustment') {
        // Manual adjustments are always "completed"
        statusMatch = statusFilter.value === 'completed';
      } else {
        statusMatch = leave.status === statusFilter.value;
      }
    }
    
    if (typeFilter.value) {
      if (leave.type === 'manual_adjustment') {
        // For manual adjustments, we need to match the leave type name
        const selectedType = props.leaveTypes.find(t => t.id === parseInt(typeFilter.value));
        typeMatch = selectedType && leave.leave_type === selectedType.name;
      } else {
        typeMatch = leave.leave_type_id === parseInt(typeFilter.value);
      }
    }
    
    return statusMatch && typeMatch;
  })
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const confirmDelete = (leave) => {
  leaveToDelete.value = leave
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  leaveToDelete.value = null
}

const openManualAdjustmentModal = (adjustment) => {
  selectedManualAdjustment.value = adjustment
  showManualAdjustmentModal.value = true
}

const closeManualAdjustmentModal = () => {
  showManualAdjustmentModal.value = false
  selectedManualAdjustment.value = null
}

const showAllManualAdjustments = () => {
  // Filter to show only manual adjustments
  statusFilter.value = 'completed'
  typeFilter.value = ''
}

const deleteLeave = () => {
  router.delete(route('leaves.destroy', leaveToDelete.value.uuid), {
    onSuccess: () => {
      closeDeleteModal()
    }
  })
}
</script> 