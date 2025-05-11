<template>
  <AppLayout title="Draft Leave Applications">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Draft Leave Applications
        </h2>
        <div class="flex space-x-4">
          <Link
            :href="route('leaves.create')"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            New Application
          </Link>
          <Link
            :href="route('leaves.index')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
          >
            <DocumentTextIcon class="w-4 h-4 mr-2" />
            View All Applications
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Empty State -->
        <div v-if="drafts.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-center">
            <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-medium text-gray-900">No draft applications</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new leave application.</p>
            <div class="mt-6">
              <Link
                :href="route('leaves.create')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                <PlusIcon class="w-4 h-4 mr-2" />
                New Application
              </Link>
            </div>
          </div>
        </div>

        <!-- Draft List -->
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="draft in drafts"
            :key="draft.id"
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200"
          >
            <div class="p-6">
              <!-- Header -->
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="text-lg font-medium text-gray-900">
                    {{ getLeaveTypeName(draft.leave_type_id) }}
                  </h3>
                  <p class="mt-1 text-sm text-gray-500">
                    Created {{ formatDate(draft.created_at) }}
                  </p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                  Draft
                </span>
              </div>

              <!-- Content -->
              <div class="mt-4 space-y-3">
                <div class="flex items-center text-sm text-gray-500">
                  <CalendarIcon class="w-4 h-4 mr-2" />
                  <span>{{ formatDate(draft.start_date) }} - {{ formatDate(draft.end_date) }}</span>
                </div>
                <div class="flex items-center text-sm text-gray-500">
                  <ClockIcon class="w-4 h-4 mr-2" />
                  <span>{{ calculateDuration(draft.start_date, draft.end_date) }} days</span>
                </div>
                <p class="text-sm text-gray-600 line-clamp-2">
                  {{ draft.reason }}
                </p>
              </div>

              <!-- Actions -->
              <div class="mt-6 flex justify-end space-x-3">
                <Link
                  :href="route('leaves.edit', draft.id)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <PencilIcon class="w-4 h-4 mr-1" />
                  Edit
                </Link>
                <button
                  @click="submitDraft(draft)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  <PaperAirplaneIcon class="w-4 h-4 mr-1" />
                  Submit
                </button>
                <button
                  @click="deleteDraft(draft)"
                  class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <TrashIcon class="w-4 h-4 mr-1" />
                  Delete
                </button>
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
import {
  PlusIcon,
  DocumentIcon,
  DocumentTextIcon,
  CalendarIcon,
  ClockIcon,
  PencilIcon,
  PaperAirplaneIcon,
  TrashIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
  drafts: Array,
  leaveTypes: Array,
})

const getLeaveTypeName = (leaveTypeId) => {
  const leaveType = props.leaveTypes.find(type => type.id === leaveTypeId)
  return leaveType ? leaveType.name : 'Unknown Leave Type'
}

const formatDate = (date) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' }
  return new Date(date).toLocaleDateString('en-US', options)
}

const calculateDuration = (startDate, endDate) => {
  const start = new Date(startDate)
  const end = new Date(endDate)
  const diffTime = Math.abs(end - start)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays + 1 // Include both start and end dates
}

const submitDraft = (draft) => {
  if (confirm('Are you sure you want to submit this draft?')) {
    // Implement draft submission logic
  }
}

const deleteDraft = (draft) => {
  if (confirm('Are you sure you want to delete this draft?')) {
    // Implement draft deletion logic
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 