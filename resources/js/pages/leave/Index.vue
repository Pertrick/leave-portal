<template>
  <AppLayout title="Leave Applications">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Leave Applications
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Manage and track your leave requests
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            :href="route('leaves.drafts')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <DocumentIcon class="w-4 h-4 mr-2" />
            Drafts
          </Link>
          <Link
            :href="route('leaves.create')"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm hover:shadow-md"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            New Application
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                        {{ leave.leave_type.name }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ leave.calendar_days }} calendar days
                        <span class="text-gray-500">({{ leave.working_days }} working days)</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                          'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                          'bg-green-100 text-green-800': leave.status === 'approved',
                          'bg-red-100 text-red-800': leave.status === 'rejected'
                        }"
                      >
                        {{ leave.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div class="flex space-x-2">
                        <Link
                          :href="route('leaves.show', leave.id)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          View
                        </Link>
                        <Link
                          v-if="leave.status === 'pending'"
                          :href="route('leaves.edit', leave.id)"
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
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">
          Are you sure you want to cancel this leave application?
        </h2>
        <p class="mt-1 text-sm text-gray-600">
          This action cannot be undone.
        </p>
        <div class="mt-6 flex justify-end space-x-3">
          <SecondaryButton @click="closeDeleteModal">Cancel</SecondaryButton>
          <DangerButton @click="deleteLeave">Delete</DangerButton>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { ToastService } from '@/services/toast'
import { PlusIcon, ClockIcon, CheckCircleIcon, XCircleIcon, DocumentIcon } from '@heroicons/vue/24/outline/index.js'

const props = defineProps({
  leaves: Array,
  leaveTypes: Array,
})

const statusFilter = ref('')
const typeFilter = ref('')
const showDeleteModal = ref(false)
const leaveToDelete = ref(null)

const filteredLeaves = computed(() => {
  return props.leaves.filter(leave => {
    const statusMatch = !statusFilter.value || leave.status === statusFilter.value
    const typeMatch = !typeFilter.value || leave.leave_type_id === parseInt(typeFilter.value)
    return statusMatch && typeMatch
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

const deleteLeave = () => {
  router.delete(route('leaves.destroy', leaveToDelete.value.id), {
    onSuccess: () => {
      closeDeleteModal()
      ToastService.success('Leave application cancelled successfully')
    },
    onError: () => {
      ToastService.error('Failed to cancel leave application')
    }
  })
}
</script> 