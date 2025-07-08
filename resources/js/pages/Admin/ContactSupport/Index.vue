<template>
  <AdminLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Support Requests Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <!-- Filters Section -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
              <h3 class="text-lg font-medium mb-4">Filters</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <!-- Search -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                  <input
                    v-model="filters.search"
                    type="text"
                    placeholder="Search subject or message..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    @input="debounceSearch"
                  />
                </div>

                <!-- Status Filter -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                  <select
                    v-model="filters.status"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    @change="applyFilters"
                  >
                    <option value="">All Statuses</option>
                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>

                <!-- Priority Filter -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                  <select
                    v-model="filters.priority"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    @change="applyFilters"
                  >
                    <option value="">All Priorities</option>
                    <option v-for="(label, value) in priorities" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>

                <!-- Category Filter -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                  <select
                    v-model="filters.category"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    @change="applyFilters"
                  >
                    <option value="">All Categories</option>
                    <option v-for="(label, value) in categories" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                </div>

                <!-- User Filter -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                  <select
                    v-model="filters.user_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    @change="applyFilters"
                  >
                    <option value="">All Users</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">
                      {{ user.firstname }} {{ user.lastname }}
                    </option>
                  </select>
                </div>

                <!-- Clear Filters -->
                <div class="flex items-end">
                  <button
                    @click="clearFilters"
                    class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors"
                  >
                    Clear Filters
                  </button>
                </div>
              </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ stats.open }}</div>
                <div class="text-sm text-blue-800">Open Requests</div>
              </div>
              <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ stats.in_progress }}</div>
                <div class="text-sm text-yellow-800">In Progress</div>
              </div>
              <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ stats.resolved }}</div>
                <div class="text-sm text-green-800">Resolved</div>
              </div>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-2xl font-bold text-gray-600">{{ stats.closed }}</div>
                <div class="text-sm text-gray-800">Closed</div>
              </div>
            </div>

            <!-- Requests Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      ID
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Subject
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Category
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Priority
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Created
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="request in requests.data" :key="request.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      #{{ request.id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ request.user?.firstname }} {{ request.user?.lastname }}
                      </div>
                      <div class="text-sm text-gray-500">{{ request.user?.email }}</div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="text-sm text-gray-900 font-medium">{{ request.subject }}</div>
                      <div class="text-sm text-gray-500 truncate max-w-xs">{{ request.message }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ categories[request.category] }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getPriorityClass(request.priority)"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      >
                        {{ priorities[request.priority] }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span
                        :class="getStatusClass(request.status)"
                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      >
                        {{ statuses[request.status] }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(request.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link
                        :href="route('admin.contact-support.show', request.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        View
                      </Link>
                      <button
                        v-if="request.status === 'open'"
                        @click="closeRequest(request.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Close
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="requests.links" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  requests: Object,
  categories: Object,
  priorities: Object,
  statuses: Object,
  users: Array,
  filters: Object,
})

const filters = ref({
  search: props.filters.search || '',
  status: props.filters.status || '',
  priority: props.filters.priority || '',
  category: props.filters.category || '',
  user_id: props.filters.user_id || '',
})

// Statistics
const stats = computed(() => {
  const stats = { open: 0, in_progress: 0, resolved: 0, closed: 0 }
  props.requests.data.forEach(request => {
    if (request.status === 'open') stats.open++
    else if (request.status === 'in_progress') stats.in_progress++
    else if (request.status === 'resolved') stats.resolved++
    else if (request.status === 'closed') stats.closed++
  })
  return stats
})

// Debounced search
let searchTimeout
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    applyFilters()
  }, 500)
}

const applyFilters = () => {
  router.get(route('admin.contact-support.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const clearFilters = () => {
  filters.value = {
    search: '',
    status: '',
    priority: '',
    category: '',
    user_id: '',
  }
  applyFilters()
}

const closeRequest = (id) => {
  if (confirm('Are you sure you want to close this request?')) {
    router.delete(route('admin.contact-support.close', id))
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
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script> 