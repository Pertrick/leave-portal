<template>
  <AppLayout title="Leave Application Details">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Leave Application Details
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Review leave application details and take action
          </p>
        </div>
        <Link
          :href="route('leave.approvals.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
        >
          Back to List
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Employee Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                  <img class="h-16 w-16 rounded-full" :src="leave.user.profile_photo_url" :alt="leave.user.name">
                </div>
                <div class="ml-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ leave.user.firstname }} {{ leave.user.lastname }}
                  </h3>
                  <p class="text-sm text-gray-500">{{ leave.user.email }}</p>
                  <p class="text-sm text-gray-500">{{ leave.user.department?.name }}</p>
                  <p class="text-sm text-gray-500">Employee ID: {{ leave.user.employee_id }}</p>
                  <p class="text-sm text-gray-500">Position: {{ leave.user.position }}</p>
                </div>
              </div>
            </div>

            <!-- Leave Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 class="text-sm font-medium text-gray-500">Leave Type</h4>
                <p class="mt-1 text-sm text-gray-900">{{ leave.leave_type.name }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                <p class="mt-1">
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
                  <span v-if="leave.status !== 'pending'" class="ml-2 text-sm text-gray-500">
                    on {{ formatDate(getActionDate()) }}
                  </span>
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Start Date</h4>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(leave.start_date) }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">End Date</h4>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(leave.end_date) }}</p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Duration</h4>
                <p class="mt-1 text-sm text-gray-900">
                  {{ leave.calendar_days }} calendar days
                  <span class="text-gray-500">({{ leave.working_days }} working days)</span>
                </p>
              </div>
              <div>
                <h4 class="text-sm font-medium text-gray-500">Applied On</h4>
                <p class="mt-1 text-sm text-gray-900">{{ formatDate(leave.created_at) }}</p>
              </div>
            </div>

            <!-- Reason -->
            <div class="mt-6">
              <h4 class="text-sm font-medium text-gray-500">Reason</h4>
              <p class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ leave.reason }}</p>
            </div>

            <!-- Approval History -->
            <div class="mt-8">
              <h4 class="text-sm font-medium text-gray-500 mb-4">Approval History</h4>
              <div class="space-y-4">
                <div v-for="approval in leave.approvals" :key="approval.id" class="flex items-start">
                  <div class="flex-shrink-0">
                    <img class="h-8 w-8 rounded-full" :src="approval.approver.avatar" :alt="approval.approver.firstname">
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">
                      {{ approval.approver.firstname }} {{ approval.approver.lastname }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ approval.approvalLevel?.name }} - 
                      <span
                        :class="{
                          'text-yellow-600': approval.status === 'pending',
                          'text-green-600': approval.status === 'approved',
                          'text-red-600': approval.status === 'rejected'
                        }"
                      >
                        {{ approval.status }}
                      </span>
                    </div>
                    <div v-if="approval.remark" class="mt-1 text-sm text-gray-500">
                      {{ approval.remark }}
                    </div>
                    <div class="text-xs text-gray-400">
                      {{ formatDate(approval.action_date) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Approver's Comment and Actions -->
            <div v-if="leave.status === 'pending'" class="mt-8 border-t border-gray-200 pt-6">
              <h4 class="text-sm font-medium text-gray-500 mb-4">Your Response</h4>
              <div class="space-y-4">
                <div>
                  <label for="comment" class="block text-sm font-medium text-gray-700">Comment (Optional)</label>
                  <textarea
                    id="comment"
                    v-model="approverComment"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Add a comment about this leave application..."
                  ></textarea>
                </div>

                <div class="flex space-x-4">
                  <button
                    @click="showApproveModal = true"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <CheckCircleIcon class="h-4 w-4 mr-2" />
                    Approve
                  </button>
                  <button
                    @click="showRejectModal = true"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <XCircleIcon class="h-4 w-4 mr-2" />
                    Reject
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <ConfirmationModal
      :show="showApproveModal"
      type="success"
      title="Approve Leave Application"
      message="Are you sure you want to approve this leave application?"
      confirm-text="Confirm Approval"
      @close="closeApproveModal"
      @confirm="confirmApprove"
    />

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
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
import { XCircleIcon, CheckCircleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
  leave: {
    type: Object,
    required: true
  }
})

const showRejectModal = ref(false)
const showApproveModal = ref(false)
const rejectionReason = ref('')
const approverComment = ref('')

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getActionDate = () => {
  if (props.leave.status === 'rejected') {
    // For rejected status, use rejected_at from the latest rejection
    const rejection = props.leave.approvals
      .filter(a => a.status === 'rejected')
      .sort((a, b) => new Date(b.rejected_at) - new Date(a.rejected_at))[0];
    return rejection?.rejected_at;
  } else if (props.leave.status === 'approved') {
    // For approved status, use action_date from the latest approval
    const approval = props.leave.approvals
      .filter(a => a.status === 'approved')
      .sort((a, b) => new Date(b.action_date) - new Date(a.action_date))[0];
    return approval?.action_date;
  }
  return null;
}

const closeApproveModal = () => {
  showApproveModal.value = false
}

const confirmApprove = () => {
  router.post(route('leave.approvals.approve', props.leave.uuid), {
    comment: approverComment.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      closeApproveModal()
    }
  })
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectionReason.value = ''
}

const confirmReject = () => {
  router.post(route('leave.approvals.reject', props.leave.uuid), {
    reason: rejectionReason.value,
    comment: approverComment.value
  }, {
    preserveScroll: true,
    onSuccess: () => {
      closeRejectModal()
    }
  })
}
</script> 