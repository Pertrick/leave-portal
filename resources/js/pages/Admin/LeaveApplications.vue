<template>
  <AppLayout title="Leave Applications">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Leave Applications
        </h2>
        <div class="flex items-center space-x-4">
          <button
            @click="exportData"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export Data
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <!-- Department Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Department</label>
                <select
                  v-model="filters.department"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @change="loadApplications"
                >
                  <option value="">All Departments</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                    {{ dept.name }}
                  </option>
                </select>
              </div>

              <!-- Status Filter -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select
                  v-model="filters.status"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @change="loadApplications"
                >
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>

              <!-- Date Range -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                <input
                  type="date"
                  v-model="filters.start_date"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @change="loadApplications"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">End Date</label>
                <input
                  type="date"
                  v-model="filters.end_date"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  @change="loadApplications"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Applications Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="application in applications" :key="application.id">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-xl font-medium text-indigo-600">
                              {{ application.user.firstname[0] }}{{ application.user.lastname[0] }}
                            </span>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ application.user.firstname }} {{ application.user.lastname }}
                          </div>
                          <div class="text-sm text-gray-500">{{ application.user.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ application.user.department.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ application.leave_type.name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ formatDate(application.start_date) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ formatDate(application.end_date) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="{
                          'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                          'bg-yellow-100 text-yellow-800': application.status === 'pending',
                          'bg-green-100 text-green-800': application.status === 'approved',
                          'bg-red-100 text-red-800': application.status === 'rejected'
                        }"
                      >
                        {{ application.status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <button
                        @click="viewDetails(application)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        View
                      </button>
                      <button
                        v-if="application.status === 'pending'"
                        @click="approveApplication(application)"
                        class="text-green-600 hover:text-green-900 mr-3"
                      >
                        Approve
                      </button>
                      <button
                        v-if="application.status === 'pending'"
                        @click="rejectApplication(application)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Reject
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
              <Pagination :links="pagination.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- View Details Modal -->
    <Modal :show="showDetailsModal" @close="showDetailsModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Leave Application Details</h2>
        <div v-if="selectedApplication" class="space-y-4">
          <!-- Application details here -->
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Modal from '@/components/Modal.vue'
import Pagination from '@/components/Pagination.vue'
import { useForm, router } from '@inertiajs/vue3'
import { format } from 'date-fns'

const props = defineProps({
  departments: {
    type: Array,
    default: () => []
  },
  applications: {
    type: Array,
    default: () => []
  },
  pagination: {
    type: Object,
    default: () => ({})
  }
})

const filters = ref({
  department: '',
  status: '',
  start_date: '',
  end_date: ''
})

const showDetailsModal = ref(false)
const selectedApplication = ref(null)

const formatDate = (date) => {
  return format(new Date(date), 'MMM d, yyyy')
}

const loadApplications = () => {
  router.get(route('admin.leave.applications'), filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

const viewDetails = (application) => {
  selectedApplication.value = application
  showDetailsModal.value = true
}

const approveApplication = (application) => {
  if (confirm('Are you sure you want to approve this leave application?')) {
    router.post(route('admin.leave.approve', application.id), {}, {
      onSuccess: () => {
        loadApplications()
      }
    })
  }
}

const rejectApplication = (application) => {
  if (confirm('Are you sure you want to reject this leave application?')) {
    router.post(route('admin.leave.reject', application.id), {}, {
      onSuccess: () => {
        loadApplications()
      }
    })
  }
}

const exportData = () => {
  // Implement export functionality
  router.get(route('admin.leave.export'), filters.value)
}

onMounted(() => {
  loadApplications()
})
</script> 