<template>
  <AppLayout title="Leave Applications">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Leave Applications
        </h2>
        <div class="flex space-x-4">
          <Link
            :href="route('admin.leave-applications.export', form)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
          >
            <ArrowDownTrayIcon class="w-4 h-4 mr-2" />
            Export
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Filters -->
          <div class="p-6 border-b border-gray-200">
            <form @submit.prevent="submit" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                  <InputLabel for="search" value="Search" />
                  <TextInput
                    id="search"
                    v-model="form.search"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Search by name, email or reason"
                    @input="handleSearch"
                  />
                </div>
                <div>
                  <InputLabel for="status" value="Status" />
                  <SelectInput
                    id="status"
                    v-model="form.status"
                    class="mt-1 block w-full"
                  >
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="cancelled">Cancelled</option>
                  </SelectInput>
                </div>
                <div>
                  <InputLabel for="department" value="Department" />
                  <SelectInput
                    id="department"
                    v-model="form.department_id"
                    class="mt-1 block w-full"
                  >
                    <option value="">All Departments</option>
                    <option
                      v-for="department in departments"
                      :key="department.id"
                      :value="department.id"
                    >
                      {{ department.name }}
                    </option>
                  </SelectInput>
                </div>
                <div>
                  <InputLabel for="leave_type" value="Leave Type" />
                  <SelectInput
                    id="leave_type"
                    v-model="form.leave_type_id"
                    class="mt-1 block w-full"
                  >
                    <option value="">All Types</option>
                    <option
                      v-for="type in leaveTypes"
                      :key="type.id"
                      :value="type.id"
                    >
                      {{ type.name }}
                    </option>
                  </SelectInput>
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <InputLabel for="date_range" value="Date Range" />
                  <DateRangePicker
                    v-model="form.date_range"
                    class="mt-1 block w-full"
                  />
                </div>
                <div>
                  <InputLabel for="approver_status" value="Approver Status" />
                  <SelectInput
                    id="approver_status"
                    v-model="form.approver_status"
                    class="mt-1 block w-full"
                  >
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </SelectInput>
                </div>
              </div>
              <div class="flex justify-end space-x-4">
                <SecondaryButton @click="reset">Reset Filters</SecondaryButton>
                <PrimaryButton type="submit" :disabled="form.processing">
                  Apply Filters
                </PrimaryButton>
              </div>
            </form>
          </div>

          <!-- Applications Table -->
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
                    Duration
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Approvers
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="leave in leaves.data" :key="leave.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div>
                        <div class="text-sm font-medium text-gray-900">
                          {{ leave.firstname }} {{ leave.lastname }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ leave.email }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ leave.department_name }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ leave.leave_type_name }}</div>
                    <div class="text-sm text-gray-500">{{ leave.reason }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                      {{ formatDate(leave.start_date) }} to {{ formatDate(leave.end_date) }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ leave.total_days }} days
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                        'bg-yellow-100 text-yellow-800': leave.status === 'pending',
                        'bg-green-100 text-green-800': leave.status === 'approved',
                        'bg-red-100 text-red-800': leave.status === 'rejected',
                        'bg-gray-100 text-gray-800': leave.status === 'cancelled'
                      }"
                    >
                      {{ leave.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="leave.approvals && leave.approvals.length > 0">
                      <div v-for="approval in leave.approvals" :key="approval.id">
                        <div class="font-medium">{{ approval.approval_level?.name || 'Unknown Level' }}:</div>
                        <div>{{ approval.approver?.firstname || '' }} {{ approval.approver?.lastname || '' }}</div>
                        <div class="text-xs">{{ approval.status || 'pending' }}</div>
                      </div>
                    </div>
                    <div v-else class="text-gray-400">
                      No approvals yet
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link
                      :href="route('admin.leave-applications.show', leave.uuid)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      View Details
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 border-t border-gray-200">
            <Pagination :links="leaves.links" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, router, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import DateRangePicker from '@/Components/DateRangePicker.vue'
import Pagination from '@/Components/Pagination.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ArrowDownTrayIcon } from '@heroicons/vue/24/outline'
import { debounce } from 'lodash'

const props = defineProps({
  leaves: Object,
  departments: Array,
  leaveTypes: Array,
  filters: Object
})

const form = useForm({
  search: props.filters.search || '',
  status: props.filters.status || '',
  department_id: props.filters.department_id || '',
  leave_type_id: props.filters.leave_type_id || '',
  date_range: props.filters.date_range || '',
  approver_status: props.filters.approver_status || ''
})

const debouncedSearch = debounce(() => {
  router.get(
    route('admin.leave-applications.index'),
    {
      search: form.search,
      status: form.status,
      department_id: form.department_id,
      leave_type_id: form.leave_type_id,
      date_range: form.date_range,
      approver_status: form.approver_status
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  )
}, 300)

const handleSearch = () => {
  debouncedSearch()
}

const submit = () => {
  router.get(
    route('admin.leave-applications.index'),
    {
      search: form.search,
      status: form.status,
      department_id: form.department_id,
      leave_type_id: form.leave_type_id,
      date_range: form.date_range,
      approver_status: form.approver_status
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  )
}

const reset = () => {
  form.reset()
  submit()
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script> 