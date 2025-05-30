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
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
        >
          Back to Approvals
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Application Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Left Column -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Application Details</h3>
                <dl class="space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Employee</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ leave.user.firstname }} {{ leave.user.lastname }}
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Leave Type</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ leave.leave_type.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                      <span class="text-gray-500">
                        ({{ leave.calendar_days }} calendar days, {{ leave.working_days }} working days)
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Reason</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ leave.reason }}</dd>
                  </div>
                </dl>
              </div>

              <!-- Right Column -->
              <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Status</h3>
                <dl class="space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                    <dd class="mt-1">
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
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Approval Chain</dt>
                    <dd class="mt-1">
                      <div class="space-y-2">
                        <div
                          v-for="approval in leave.approvals"
                          :key="approval.id"
                          class="flex items-center space-x-2"
                        >
                          <div
                            :class="{
                              'w-2 h-2 rounded-full': true,
                              'bg-yellow-400': approval.status === 'pending',
                              'bg-green-400': approval.status === 'approved',
                              'bg-red-400': approval.status === 'rejected'
                            }"
                          ></div>
                          <span class="text-sm text-gray-900">
                            {{ approval.approval_level.name }}:
                            {{ approval.approver.firstname }} {{ approval.approver.lastname }}
                            <span
                              :class="{
                                'text-yellow-600': approval.status === 'pending',
                                'text-green-600': approval.status === 'approved',
                                'text-red-600': approval.status === 'rejected'
                              }"
                            >
                              ({{ approval.status }})
                            </span>
                          </span>
                        </div>
                      </div>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Approval Actions -->
            <div v-if="isNextApprover && leave.status === 'pending'" class="mt-8 border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Take Action</h3>
              <div class="space-y-4">
                <div>
                  <label for="comment" class="block text-sm font-medium text-gray-700">
                    Comment <span class="text-red-500">*</span>
                  </label>
                  <textarea
                    id="comment"
                    v-model="comment"
                    rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Add your comment"
                  ></textarea>
                  <p v-if="commentError" class="mt-1 text-sm text-red-600">{{ commentError }}</p>
                </div>
                <div class="flex space-x-4">
                  <button
                    @click="showApproveModal = true"
                    :disabled="!comment"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Approve
                  </button>
                  <button
                    @click="showRejectModal = true"
                    :disabled="!comment"
                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Reject
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Approve Modal -->
    <ConfirmationModal
      :show="showApproveModal"
      type="success"
      title="Approve Leave Application"
      message="Are you sure you want to approve this leave application?"
      confirm-text="Approve Application"
      @close="closeApproveModal"
      @confirm="confirmApprove"
    />

    <!-- Reject Modal -->
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

const props = defineProps({
  leave: {
    type: Object,
    required: true
  },
  currentUserApproval: {
    type: Object,
    required: true
  },
  isNextApprover: {
    type: Boolean,
    required: true
  }
})

const comment = ref('')
const commentError = ref('')
const showApproveModal = ref(false)
const showRejectModal = ref(false)
const rejectionReason = ref('')

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const validateComment = () => {
  if (!comment.value.trim()) {
    commentError.value = 'Comment is required'
    return false
  }
  commentError.value = ''
  return true
}

const closeApproveModal = () => {
  showApproveModal.value = false
}

const closeRejectModal = () => {
  showRejectModal.value = false
  rejectionReason.value = ''
}

const confirmApprove = () => {
  if (!validateComment()) {
    return
  }
  
  router.post(route('leave.approvals.approve', props.leave.uuid), {
    comment: comment.value
  })
}

const confirmReject = () => {
  if (!validateComment()) {
    return
  }

  router.post(route('leave.approvals.reject', props.leave.uuid), {
    reason: rejectionReason.value,
    comment: comment.value
  })
}
</script> 