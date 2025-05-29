<template>
  <AppLayout title="Staff List">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Staff List
        </h2>
        <Link
          v-if="can('manage-users')"
          :href="route('register')"
          class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
        >
          <PlusCircle class="w-4 h-4 mr-2" />
          Add Staff
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Filters -->
          <div class="p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                  type="text"
                  v-model="search"
                  placeholder="Search by name, staff ID, or email"
                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                <SearchSelect
                  v-model="filters.department_id"
                  :options="departments.map(dept => ({ value: dept.id, label: dept.name }))"
                  placeholder="All Departments"
                  searchable
                  clearable
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User Level</label>
                <SearchSelect
                  v-model="filters.user_level_id"
                  :options="userLevels.map(level => ({ value: level.id, label: level.name }))"
                  placeholder="All Levels"
                  searchable
                  clearable
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <SearchSelect
                  v-model="filters.is_active"
                  :options="[
                    { value: true, label: 'Active' },
                    { value: false, label: 'Inactive' }
                  ]"
                  placeholder="All Status"
                  clearable
                />
              </div>
            </div>
          </div>

          <!-- Staff Table -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Staff
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Department
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Level
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Role
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
                <tr v-for="staff in staff" :key="staff.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <UserAvatar
                          :src="staff.avatar"
                          :name="`${staff.firstname} ${staff.lastname}`"
                          size="md"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ staff.firstname }} {{ staff.lastname }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ staff.staff_id }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ staff.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ staff.department?.name }}</div>
                    <div class="text-sm text-gray-500">{{ staff.designation }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ staff.user_level?.name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ staff.roles?.[0]?.name || 'No Role' }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="[
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        staff.is_active
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ staff.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                      <Link
                        :href="route('staff.show', staff.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        View
                      </Link>
                      <button
                        v-if="can('manage-users')"
                        @click="toggleStatus(staff)"
                        class="text-gray-600 hover:text-gray-900"
                      >
                        {{ staff.is_active ? 'Deactivate' : 'Activate' }}
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="staff.links" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { SearchSelect } from '@/components/ui/select'
import { PlusCircle } from 'lucide-vue-next'
import Pagination from '@/components/Pagination.vue'
import { usePermission } from '@/composables/usePermission'
import UserAvatar from '@/components/UserAvatar.vue'

const { can } = usePermission()

const props = defineProps({
  staff: Object,
  departments: Array,
  userLevels: Array,
})

const search = ref('')
const filters = ref({
  department_id: null,
  user_level_id: null,
  is_active: null,
})

// Watch for changes in search and filters
watch([search, filters], ([newSearch, newFilters]) => {
  router.get(route('staff.list'), {
    search: newSearch,
    department_id: newFilters.department_id,
    user_level_id: newFilters.user_level_id,
    is_active: newFilters.is_active
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  })
}, { 
  deep: true,
  debounce: 500
})

// Remove the filteredStaff computed property since we're using backend filtering
const staff = computed(() => props.staff.data)

const toggleStatus = (staff) => {
  if (confirm(`Are you sure you want to ${staff.is_active ? 'deactivate' : 'activate'} this staff?`)) {
    router.put(route('staff.toggle-status', staff.id), {
      is_active: !staff.is_active
    })
  }
}
</script> 