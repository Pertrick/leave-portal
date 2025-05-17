<template>
  <AppLayout title="Draft Leave Applications">
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Draft Applications
          </h2>
          <p class="mt-1 text-sm text-gray-600">
            Complete and submit your draft leave requests
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            :href="route('leaves.index')"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-2" />
            Back to Applications
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
        <!-- Empty State -->
        <div v-if="drafts.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-12 text-center">
            <div class="mx-auto w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center">
              <DocumentIcon class="h-10 w-10 text-indigo-500" />
            </div>
            <h3 class="mt-4 text-lg font-semibold text-gray-900">No draft applications</h3>
            <p class="mt-2 text-sm text-gray-500">Get started by creating a new leave application</p>
            <div class="mt-8">
              <Link
                :href="route('leaves.create')"
                class="inline-flex items-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm"
              >
                <PlusIcon class="w-5 h-5 mr-2" />
                Create New Application
              </Link>
            </div>
          </div>
        </div>

        <!-- Draft List -->
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="draft in drafts"
            :key="draft.id"
            class="group bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-all duration-300"
          >
            <!-- Card Header -->
            <div class="p-6 border-b border-gray-100">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900">
                    {{ getLeaveTypeName(draft.leave_type_id) }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-500">
                    Created {{ formatDate(draft.created_at) }} 
                  </p>
                 
                  <p class="mt-1 text-sm text-gray-500">
                     Last Updated {{ formatDate(draft.updated_at) }}
                  </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                  Draft
                </span>
              </div>
            </div>

            <!-- Card Content -->
            <div class="p-6 space-y-4">
              <!-- Leave Type -->
              <div class="flex items-center">
                <span class="px-3 py-1.5 text-sm font-medium rounded-full shadow-sm" 
                      :class="{
                        'bg-blue-50 text-blue-700 border border-blue-200': draft.leave_type.name === 'Annual Leave',
                        'bg-green-50 text-green-700 border border-green-200': draft.leave_type.name === 'Sick Leave',
                        'bg-purple-50 text-purple-700 border border-purple-200': draft.leave_type.name === 'Maternity Leave',
                        'bg-yellow-50 text-yellow-700 border border-yellow-200': draft.leave_type.name === 'Paternity Leave',
                        'bg-gray-50 text-gray-700 border border-gray-200': true
                      }">
                  {{ draft.leave_type.name }}
                </span>
              </div>

              <!-- Date and Duration -->
              <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                  <div class="flex items-center text-sm text-gray-600 mb-1">
                    <CalendarIcon class="w-4 h-4 mr-2 text-gray-500" />
                    <span class="font-medium">Duration</span>
                  </div>
                  <div class="pl-6 text-sm text-gray-700">
                    {{ formatDate(draft.start_date) }} - {{ formatDate(draft.end_date) }}
                  </div>
                </div>
                
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                  <div class="flex items-center text-sm text-gray-600 mb-1">
                    <ClockIcon class="w-4 h-4 mr-2 text-gray-500" />
                    <span class="font-medium">Days</span>
                  </div>
                  <div class="pl-6 text-sm text-gray-700">
                    <span class="font-medium">{{ draft.working_days }}</span> working days
                    <span class="text-gray-500">({{ draft.calendar_days }} calendar days)</span>
                  </div>
                </div>
              </div>

              <!-- Reason -->
              <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                <div class="flex items-center text-sm text-gray-600 mb-1">
                  <DocumentTextIcon class="w-4 h-4 mr-2 text-gray-500" />
                  <span class="font-medium">Reason</span>
                </div>
                <p class="pl-6 text-sm text-gray-700 line-clamp-2">
                  {{ draft.reason }}
                </p>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end space-x-3 pt-2">
                <Link
                  :href="route('leaves.edit', draft.uuid)"
                  class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors border border-gray-300"
                >
                  <PencilIcon class="w-4 h-4 mr-1.5" />
                  Edit
                </Link>
                <button @click="deleteDraft(draft)" 
                        class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors border border-red-200">
                  <TrashIcon class="w-4 h-4 mr-1.5" />
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="closeDeleteModal">
      <div class="p-6 max-w-md mx-auto">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <TrashIcon class="h-6 w-6 text-red-600" />
          </div>
          <h2 class="text-lg font-medium text-gray-900">
            Delete Draft Application
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Are you sure you want to delete this draft application? This action cannot be undone.
          </p>
        </div>
        <div class="mt-6 flex justify-center space-x-3">
          <SecondaryButton @click="closeDeleteModal">No, Keep It</SecondaryButton>
          <DangerButton @click="confirmDelete">Yes, Delete It</DangerButton>
        </div>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, getCurrentInstance } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { useFlash } from '@/composables/useFlash'
import {
  PlusIcon,
  DocumentIcon,
  DocumentTextIcon,
  CalendarIcon,
  ClockIcon,
  PencilIcon,
  TrashIcon,
  ArrowLeftIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  drafts: Array,
  leaveTypes: Array,
})

const { flash } = useFlash()
const { proxy } = getCurrentInstance()

const showDeleteModal = ref(false)
const draftToDelete = ref(null)

const getLeaveTypeName = (leaveTypeId) => {
  const leaveType = props.leaveTypes.find(type => type.id === leaveTypeId)
  return leaveType ? leaveType.name : 'Unknown Leave Type'
}

const formatDate = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(date).toLocaleDateString('en-US', options)
}

const deleteDraft = (draft) => {
  draftToDelete.value = draft
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  draftToDelete.value = null
}

const confirmDelete = () => {
  router.delete(route('leaves.destroy', draftToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      closeDeleteModal()
    }
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Card hover effects */
.group:hover {
  transform: translateY(-2px);
}

/* Button hover effects */
button {
  transition: all 0.2s ease-in-out;
}

button:hover {
  transform: translateY(-1px);
}
</style> 