<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Support Request #{{ request.id }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6 flex justify-end">
          <Link
            :href="route('admin.contact-support.index')"
            class="inline-flex items-center px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800 transition-colors"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to List
          </Link>
        </div>

        <!-- Request Details -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="flex justify-between items-start mb-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">{{ request.subject }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                  Submitted by {{ request.user?.firstname }} {{ request.user?.lastname }} on {{ formatDate(request.created_at) }}
                </p>
              </div>
              <div class="flex space-x-2">
                <span
                  :class="getPriorityClass(request.priority)"
                  class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                >
                  {{ priorities[request.priority] }}
                </span>
                <span
                  :class="getStatusClass(request.status)"
                  class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                >
                  {{ statuses[request.status] }}
                </span>
              </div>
            </div>

            <!-- User Information -->
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
              <h4 class="font-medium text-gray-900 mb-2">User Information</h4>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-gray-600">Name:</span>
                  <span class="ml-2 font-medium">{{ request.user?.firstname }} {{ request.user?.lastname }}</span>
                </div>
                <div>
                  <span class="text-gray-600">Email:</span>
                  <span class="ml-2 font-medium">{{ request.user?.email }}</span>
                </div>
                <div>
                  <span class="text-gray-600">Department:</span>
                  <span class="ml-2 font-medium">{{ request.user?.department?.name || 'N/A' }}</span>
                </div>
                <div>
                  <span class="text-gray-600">Category:</span>
                  <span class="ml-2 font-medium">{{ categories[request.category] }}</span>
                </div>
              </div>
            </div>

            <!-- Request Message -->
            <div class="mb-6">
              <h4 class="font-medium text-gray-900 mb-2">Request Message</h4>
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700 whitespace-pre-wrap">{{ request.message }}</p>
              </div>
            </div>

            <!-- Admin Response (if exists) -->
            <div v-if="request.admin_response" class="mb-6">
              <h4 class="font-medium text-gray-900 mb-2">Admin Response</h4>
              <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-gray-700 whitespace-pre-wrap">{{ request.admin_response }}</p>
                <p class="text-sm text-gray-500 mt-2">
                  Responded by {{ request.responder?.firstname }} {{ request.responder?.lastname }} on {{ formatDate(request.responded_at) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Admin Actions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Admin Actions</h3>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <button
                v-if="request.status === 'open'"
                @click="updateStatus('in_progress')"
                class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors"
              >
                Mark In Progress
              </button>
              <button
                v-if="request.status === 'in_progress'"
                @click="updateStatus('resolved')"
                class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors"
              >
                Mark Resolved
              </button>
              <button
                v-if="request.status !== 'closed'"
                @click="updateStatus('closed')"
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors"
              >
                Close Request
              </button>
              <button
                v-if="request.status === 'closed'"
                @click="updateStatus('open')"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors"
              >
                Reopen Request
              </button>
            </div>

            <!-- Priority Update -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Update Priority</label>
              <div class="flex space-x-2">
                <button
                  v-for="(label, value) in priorities"
                  :key="value"
                  @click="updatePriority(value)"
                  :class="[
                    'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                    request.priority === value
                      ? 'bg-indigo-600 text-white'
                      : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                  ]"
                >
                  {{ label }}
                </button>
              </div>
            </div>

            <!-- Response Form -->
            <form @submit.prevent="submitResponse" class="space-y-4">
              <div>
                <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">
                  Admin Response
                </label>
                <textarea
                  id="admin_response"
                  v-model="form.admin_response"
                  rows="4"
                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                  placeholder="Enter your response to the user..."
                  required
                ></textarea>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Update Status
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  >
                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                    Update Priority
                  </label>
                  <select
                    id="priority"
                    v-model="form.priority"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  >
                    <option v-for="(label, value) in priorities" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="flex justify-end space-x-3">
                <button
                  type="button"
                  @click="resetForm"
                  class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors"
                >
                  Reset
                </button>
                <button
                  type="submit"
                  class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
                >
                  Send Response
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  request: Object,
  categories: Object,
  priorities: Object,
  statuses: Object,
})

const form = ref({
  admin_response: '',
  status: props.request.status,
  priority: props.request.priority,
})

const submitResponse = () => {
  router.post(route('admin.contact-support.respond', props.request.id), form.value)
}

const updateStatus = (status) => {
  router.patch(route('admin.contact-support.update-status', props.request.id), {
    status: status
  })
}

const updatePriority = (priority) => {
  router.patch(route('admin.contact-support.update-priority', props.request.id), {
    priority: priority
  })
}

const resetForm = () => {
  form.value = {
    admin_response: '',
    status: props.request.status,
    priority: props.request.priority,
  }
}

const getPriorityClass = (priority) => {
  const classes = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800',
  }
  return classes[priority] || 'bg-gray-100 text-gray-800'
}

const getStatusClass = (status) => {
  const classes = {
    open: 'bg-blue-100 text-blue-800',
    in_progress: 'bg-yellow-100 text-yellow-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script> 