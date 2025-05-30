<template>
  <AppLayout :title="`Leave Application - ${leave.firstname} ${leave.lastname}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Leave Application Details
        </h2>
        <Link
          :href="route('admin.leave-applications.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
        >
          <ArrowLeftIcon class="w-4 h-4 mr-2" />
          Back to List
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Application Status -->
            <div class="mb-6">
              <span
                :class="{
                  'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full': true,
                  'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                  'bg-green-100 text-green-800': leave.status === 'approved',
                  'bg-red-100 text-red-800': leave.status === 'rejected',
                  'bg-gray-100 text-gray-800': leave.status === 'cancelled'
                }"
              >
                {{ leave.status }}
              </span>
            </div>

            <!-- Employee Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                <dl class="grid grid-cols-1 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ leave.firstname }} {{ leave.lastname }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ leave.email }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ leave.department_name }}</dd>
                  </div>
                </dl>
              </div>

              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Details</h3>
                <dl class="grid grid-cols-1 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ leave.leave_type_name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}
                      ({{ leave.total_days }} days)
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Applied On</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(leave.created_at) }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Reason and Comments -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Reason</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-900">{{ leave.reason }}</p>
              </div>
            </div>

            <div class="mb-6" v-if="leave.applicant_comment">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Comments</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-900">{{ leave.applicant_comment }}</p>
              </div>
            </div>

            <!-- Replacement Staff -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Replacement Staff</h3>
              <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Name</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ leave.replacement_staff_name }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Contact</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ leave.replacement_staff_phone }}</dd>
                </div>
              </dl>
            </div>

            <!-- Approval Chain -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Chain</h3>
              <div class="grid grid-cols-1 gap-6">
                <div v-if="leave.approvals && leave.approvals.length > 0">
                  <div v-for="approval in leave.approvals" :key="approval.id" class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">
                      {{ approval.approval_level && approval.approval_level.name ? approval.approval_level.name : 'Unknown Level' }}
                    </h4>
                    <dl class="grid grid-cols-1 gap-2">
                      <div>
                        <dt class="text-sm font-medium text-gray-500">Approver</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                          {{ approval.approver ? `${approval.approver.firstname || ''} ${approval.approver.lastname || ''}` : 'Not Assigned' }}
                        </dd>
                      </div>
                      <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                          <span
                            :class="{
                              'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                              'bg-yellow-100 text-yellow-800': !approval.status || approval.status === 'pending',
                              'bg-green-100 text-green-800': approval.status === 'approved',
                              'bg-red-100 text-red-800': approval.status === 'rejected'
                            }"
                          >
                            {{ approval.status || 'Pending' }}
                          </span>
                        </dd>
                      </div>
                      <div v-if="approval.comment">
                        <dt class="text-sm font-medium text-gray-500">Comment</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ approval.comment }}</dd>
                      </div>
                    </dl>
                  </div>
                </div>
                <div v-else class="bg-gray-50 rounded-lg p-4">
                  <p class="text-sm text-gray-500">No approvals yet</p>
                </div>
              </div>
            </div>

            <!-- Supporting Documents -->
            <div v-if="leave.attachment" class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Supporting Documents</h3>
              <div class="bg-gray-50 rounded-lg p-4">
                <a
                  :href="route('leaves.attachment', leave.id)"
                  target="_blank"
                  class="inline-flex items-center text-indigo-600 hover:text-indigo-900"
                >
                  <DocumentIcon class="w-5 h-5 mr-2" />
                  View Attachment
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { ArrowLeftIcon, DocumentIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  leave: {
    type: Object,
    required: true
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}
</script> 