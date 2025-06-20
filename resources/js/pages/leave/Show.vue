<template>
  <AppLayout :title="`Leave Application #${leave.id}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Leave Application Details
        </h2>
        <div class="flex space-x-2">
          <Link
            v-if="leave.status === 'pending'"
            :href="route('leaves.edit', leave.id)"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <PencilIcon class="w-4 h-4 mr-2" />
            Edit
          </Link>
          <Link
            :href="route('leaves.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-2" />
            Back to List
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <!-- Status Badge -->
            <div class="mb-6">
              <span
                :class="{
                  'px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full': true,
                  'bg-gray-100 text-gray-800': leave.status === 'cancelled',
                  'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                  'bg-green-100 text-green-800': leave.status === 'approved',
                  'bg-red-100 text-red-800': leave.status === 'rejected'
                }"
              >
                {{ leave.status }}
              </span>
            </div>

            <!-- Application Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Leave Type</h3>
                  <p class="mt-1 text-sm text-gray-900">{{ leave.leave_type.name }}</p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                    ({{ leave.calendar_days }} calendar days, {{ leave.working_days }} working days)
                  </p>
                </div>

                <div>
                  <h3 class="text-sm font-medium text-gray-500">Reason</h3>
                  <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ leave.reason }}</p>
                </div>

                <div v-if="leave.applicant_comment">
                  <h3 class="text-sm font-medium text-gray-500">Additional Comments</h3>
                  <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ leave.applicant_comment }}</p>
                </div>

                <div v-if="leave.replacement_staff_name || leave.replacement_staff_phone">
                  <h3 class="text-sm font-medium text-gray-500">Replacement Staff</h3>
                  <p class="mt-1 text-sm text-gray-900">
                    {{ leave.replacement_staff_name }}
                    <span v-if="leave.replacement_staff_phone" class="block text-gray-600">
                      {{ leave.replacement_staff_phone }}
                    </span>
                  </p>
                </div>

                <div v-if="leave.attachment">
                  <h3 class="text-sm font-medium text-gray-500">Supporting Document</h3>
                  <a
                    :href="leave.attachment"
                    target="_blank"
                    class="mt-1 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900"
                  >
                    <DocumentIcon class="w-4 h-4 mr-1" />
                    View Document
                  </a>
                </div>
              </div>

              <!-- Approval Details -->
              <div class="space-y-4">
                <div>
                  <h3 class="text-sm font-medium text-gray-500">Applicant</h3>
                  <p class="mt-1 text-sm text-gray-900">{{ leave.user.name }}</p>
                </div>

                <div v-if="leave.approvals.length > 0 || leave.status === 'cancelled'">
                  <h3 class="text-sm font-medium text-gray-500">Approval History</h3>
                  <div class="mt-2 space-y-3">
                    <!-- Cancellation Entry -->
                    <div v-if="leave.status === 'cancelled'" class="flex items-start space-x-3">
                      <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full bg-gray-400"></div>
                      <div>
                        <p class="text-sm text-gray-900">
                          {{ leave.user.name }}
                          <span class="ml-2 text-xs font-medium text-gray-600">
                            Cancelled
                          </span>
                        </p>
                        <p class="text-xs text-gray-500">
                          {{ formatDateTime(leave.updated_at) }}
                        </p>
                        <p v-if="leave.cancellation_reason" class="mt-1 text-sm text-gray-600">
                          {{ leave.cancellation_reason }}
                        </p>
                      </div>
                    </div>
                    
                    <!-- Regular Approval Entries -->
                    <div
                      v-for="approval in leave.approvals.filter(a => a.status !== 'pending')"
                      :key="approval.id"
                      class="flex items-start space-x-3"
                    >
                      <div
                        :class="{
                          'flex-shrink-0 w-2 h-2 mt-2 rounded-full': true,
                          'bg-yellow-400': approval.status === 'pending',
                          'bg-green-400': approval.status === 'approved',
                          'bg-red-400': approval.status === 'rejected'
                        }"
                      ></div>
                      <div>
                        <p class="text-sm text-gray-900">
                          {{ approval.user.name }}
                          <span
                            :class="{
                              'ml-2 text-xs font-medium': true,
                              'text-yellow-600': approval.status === 'pending',
                              'text-green-600': approval.status === 'approved',
                              'text-red-600': approval.status === 'rejected'
                            }"
                          >
                            {{ approval.status }}
                          </span>
                        </p>
                        <p class="text-xs text-gray-500">
                          {{ formatDateTime(approval.created_at) }}
                        </p>
                        <p
                          v-if="approval.rejection_reason"
                          class="mt-1 text-sm text-gray-600"
                        >
                          {{ approval.rejection_reason }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div
              v-if="leave.status === 'pending' && canApprove"
              class="mt-6 flex justify-end space-x-3"
            >
              <SecondaryButton @click="showRejectModal = true">
                Reject
              </SecondaryButton>
              <PrimaryButton @click="approve">
                Approve
              </PrimaryButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <ConfirmationModal
      :show="showRejectModal"
      type="danger"
      title="Reject Leave Application"
      message="Please provide a reason for rejecting this leave application."
      confirm-text="Reject Application"
      :disabled="!form.rejection_reason"
      @close="closeRejectModal"
      @confirm="reject"
    >
      <template #content>
        <div class="mt-4">
          <InputLabel for="rejection_reason" value="Reason" />
          <TextArea
            id="rejection_reason"
            v-model="form.rejection_reason"
            class="mt-1 block w-full"
            rows="4"
            required
          />
          <InputError :message="form.errors.rejection_reason" class="mt-2" />
        </div>
      </template>
    </ConfirmationModal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextArea from '@/Components/TextArea.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import {
  ArrowLeftIcon,
  PencilIcon,
  DocumentIcon,
} from '@heroicons/vue/24/outline/index.js'
import ConfirmationModal from '@/Components/ConfirmationModal.vue'

const props = defineProps({
  leave: Object,
  canApprove: Boolean,
})

const showRejectModal = ref(false)
const form = useForm({
  rejection_reason: '',
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatDateTime = (date) => {
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const approve = () => {
  router.post(route('leave.approvals.approve', props.leave.id))
}

const reject = () => {
  form.post(route('leave.approvals.reject', props.leave.id), {
    onSuccess: () => {
      closeRejectModal()
    },
  })
}

const closeRejectModal = () => {
  showRejectModal.value = false
  form.reset()
}
</script> 