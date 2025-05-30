<template>
  <AppLayout title="Leave Approvals">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Leave Approvals
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Review and manage leave applications from your team
          </p>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <ClockIcon class="w-6 h-6" />
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900">Pending</h3>
                <p class="text-2xl font-bold text-yellow-600">
                  {{ stats.pending }}
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
                  {{ stats.approved }}
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
                  {{ stats.rejected }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
              <button
                @click="activeTab = 'pending'"
                :class="[
                  'px-6 py-4 text-sm font-medium border-b-2',
                  activeTab === 'pending'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Pending Approvals
              </button>
              <button
                @click="activeTab = 'history'"
                :class="[
                  'px-6 py-4 text-sm font-medium border-b-2',
                  activeTab === 'history'
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                ]"
              >
                Approval History
              </button>
            </nav>
          </div>

          <!-- Tab Content -->
          <div class="p-6">
            <!-- Loading State -->
            <div v-if="isLoading" class="flex justify-center items-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
            </div>

            <!-- Pending Approvals Tab -->
            <div v-else-if="activeTab === 'pending'">
              <!-- Filters -->
              <div class="flex flex-wrap gap-4 mb-6">
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
                  <option value="">All Leave Types</option>
                  <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                    {{ type.name }}
                  </option>
                </select>
                <select
                  v-model="employeeFilter"
                  class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="">All Employees</option>
                  <option v-for="employee in employees" :key="employee.id" :value="employee.id">
                    {{ employee.firstname }} {{ employee.lastname }} ({{ employee.email }})
                  </option>
                </select>
                <div class="flex-1"></div>
                <div class="relative">
                  <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search applications..."
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10"
                  />
                  <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
                </div>
              </div>

              <!-- Empty State -->
              <div v-if="filteredLeaves.length === 0" class="text-center py-12">
                <div class="mx-auto w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center">
                  <DocumentIcon class="h-10 w-10 text-gray-400" />
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">No applications found</h3>
                <p class="mt-2 text-sm text-gray-500">Try adjusting your filters</p>
              </div>

              <!-- Applications Table -->
              <div v-else class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employee
                      </th>
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
                    <tr v-for="leave in filteredLeaves" :key="leave.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10">
                            <img class="h-10 w-10 rounded-full" :src="leave.user.profile_photo_url" :alt="leave.user.name">
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ leave.user.name }}
                            </div>
                            <div class="text-sm text-gray-500">
                              {{ leave.user.email }}
                            </div>
                          </div>
                        </div>
                      </td>
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
                            :href="route('leave.approvals.show', leave.uuid)"
                            class="text-indigo-600 hover:text-indigo-900"
                          >
                            View
                          </Link>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- History Tab -->
            <div v-else>
              <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                  <select
                    v-model="historyFilter"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                  >
                    <option value="all">All Actions</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                  <select
                    v-model="historyTimeFilter"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                  >
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                  </select>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="relative">
                    <input
                      type="text"
                      v-model="historySearchQuery"
                      placeholder="Search history..."
                      class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10"
                    />
                    <MagnifyingGlassIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
                  </div>
                  <button
                    @click="exportHistory"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                  >
                    <ArrowDownTrayIcon class="w-4 h-4 mr-2" />
                    Export
                  </button>
                </div>
              </div>

              <!-- History Table -->
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Employee
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Leave Type
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Dates
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Action
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                      </th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="leave in filteredHistory" :key="leave.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <UserAvatar :user="leave.user" class="h-8 w-8" />
                          <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">
                              {{ leave.user.firstname }} {{ leave.user.lastname }}
                            </div>
                            <div class="text-sm text-gray-500">{{ leave.user.email }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ leave.leave_type.name }}</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                        </div>
                        <div class="text-sm text-gray-500">{{ leave.calendar_days }} days</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <span
                          :class="{
                            'px-2 py-1 text-xs font-medium rounded-full': true,
                            'bg-green-100 text-green-800': leave.status === 'approved',
                            'bg-red-100 text-red-800': leave.status === 'rejected'
                          }"
                        >
                          {{ leave.status }}
                        </span>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(leave.approvals[0]?.action_date) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <Link
                          :href="route('leave.approvals.show', leave.uuid)"
                          class="text-indigo-600 hover:text-indigo-900"
                        >
                          View Details
                        </Link>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Empty State -->
              <div v-if="filteredHistory.length === 0" class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center">
                  <ClockIcon class="h-8 w-8 text-gray-400" />
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No approval history</h3>
                <p class="mt-2 text-sm text-gray-500">You haven't processed any leave applications yet</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <ConfirmationModal
      :show="showRejectModal"
      type="danger"
      title="Reject Leave Application"
      message="Please provide a reason for rejecting this leave application."
      confirm-text="Reject Application"
      :disabled="!rejectionReason"
      @close="closeRejectModal"
      @confirm="confirmReject"
    >
      <template #content>
        <textarea
          v-model="rejectionReason"
          rows="3"
          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
          placeholder="Enter rejection reason..."
        ></textarea>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import Modal from '@/components/Modal.vue'
import SecondaryButton from '@/components/SecondaryButton.vue'
import DangerButton from '@/components/DangerButton.vue'
import {
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  DocumentIcon,
  MagnifyingGlassIcon,
  ArrowDownTrayIcon,
} from '@heroicons/vue/24/outline'
import ConfirmationModal from '@/components/ConfirmationModal.vue'

const props = defineProps({
  leaves: {
    type: Object,
    required: true,
    default: () => ({
      data: [],
      current_page: 1,
      total: 0
    })
  },
  leaveTypes: {
    type: Array,
    required: true,
    default: () => []
  },
  employees: {
    type: Array,
    required: true,
    default: () => []
  },
  filters: {
    type: Object,
    required: true,
    default: () => ({})
  }
})

const statusFilter = ref('')
const typeFilter = ref('')
const employeeFilter = ref('')
const searchQuery = ref('')
const showRejectModal = ref(false)
const leaveToReject = ref(null)
const rejectionReason = ref('')
const historyFilter = ref('all')
const historyTimeFilter = ref('7')
const activeTab = ref(props.filters.tab || 'pending')
const historySearchQuery = ref('')
const isLoading = ref(false)

const filteredLeaves = computed(() => {
  return props.leaves.data.filter(leave => {
    const statusMatch = !statusFilter.value || leave.status === statusFilter.value
    const typeMatch = !typeFilter.value || leave.leave_type_id === parseInt(typeFilter.value)
    const employeeMatch = !employeeFilter.value || leave.user_id === parseInt(employeeFilter.value)
    const searchMatch = !searchQuery.value || 
      leave.user.first_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      leave.leave_type.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      leave.reason?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      leave.user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    return statusMatch && typeMatch && employeeMatch && searchMatch
  })
})

const stats = computed(() => {
  const leaves = props.leaves.data
  return {
    pending: leaves.filter(l => l.status === 'pending').length,
    approved: leaves.filter(l => l.status === 'approved').length,
    rejected: leaves.filter(l => l.status === 'rejected').length
  }
})

const filteredHistory = computed(() => {
  const leaves = props.leaves.data
  const now = new Date()
  const daysAgo = new Date(now.setDate(now.getDate() - parseInt(historyTimeFilter.value)))

  return leaves.filter(leave => {
    const statusMatch = historyFilter.value === 'all' || 
      leave.approvals?.some(a => a.status === historyFilter.value)
    
    const timeMatch = leave.approvals?.some(a => 
      new Date(a.action_date) >= daysAgo
    )

    return statusMatch && timeMatch
  }).sort((a, b) => {
    const aDate = new Date(Math.max(...a.approvals.map(ap => new Date(ap.action_date))))
    const bDate = new Date(Math.max(...b.approvals.map(ap => new Date(ap.action_date))))
    return bDate - aDate
  })
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const approveLeave = (leave) => {
  if (confirm('Are you sure you want to approve this leave application?')) {
    router.put(route('leaves.approve', leave.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        // Show success message
      }
    })
  }
}

const rejectLeave = (leave) => {
  leaveToReject.value = leave
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  leaveToReject.value = null
  rejectionReason.value = ''
}

const confirmReject = () => {
  router.put(route('leaves.reject', leaveToReject.value.id), {
    reason: rejectionReason.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      closeRejectModal()
    }
  })
}

const exportHistory = () => {
  router.get(route('leave.approvals.export'), {
    filter: historyFilter.value,
    time_period: historyTimeFilter.value,
    search: historySearchQuery.value
  }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      // The backend will handle the file download
    }
  })
}

// Add watch for tab changes
watch(activeTab, (newTab) => {
  isLoading.value = true
  router.get(route('leave.approvals.index'), {
    tab: newTab,
    type: typeFilter.value,
    employee: employeeFilter.value,
    search: searchQuery.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onFinish: () => {
      isLoading.value = false
    }
  })
})
</script> 