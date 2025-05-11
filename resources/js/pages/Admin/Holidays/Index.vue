<template>
  <AppLayout title="Holiday Management">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Holiday Management
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Filters -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex flex-wrap gap-4">
              <div class="flex-1 min-w-[200px]">
                <InputLabel for="type" value="Type" />
                <SelectInput
                  id="type"
                  v-model="filters.type"
                  class="mt-1 block w-full"
                  @change="filterHolidays"
                >
                  <option value="">All Types</option>
                  <option value="public">Public</option>
                  <option value="company">Company</option>
                  <option value="location">Location</option>
                </SelectInput>
              </div>

              <div class="flex-1 min-w-[200px]">
                <InputLabel for="location" value="Location" />
                <SelectInput
                  id="location"
                  v-model="filters.location_id"
                  class="mt-1 block w-full"
                  @change="filterHolidays"
                >
                  <option value="">All Locations</option>
                  <option v-for="location in locations" :key="location.id" :value="location.id">
                    {{ location.name }}
                  </option>
                </SelectInput>
              </div>

              <div class="flex-1 min-w-[200px]">
                <InputLabel for="year" value="Year" />
                <SelectInput
                  id="year"
                  v-model="filters.year"
                  class="mt-1 block w-full"
                  @change="filterHolidays"
                >
                  <option value="">All Years</option>
                  <option v-for="year in years" :key="year" :value="year">
                    {{ year }}
                  </option>
                </SelectInput>
              </div>

              <div class="flex-1 min-w-[200px]">
                <InputLabel for="status" value="Status" />
                <SelectInput
                  id="status"
                  v-model="filters.is_active"
                  class="mt-1 block w-full"
                  @change="filterHolidays"
                >
                  <option value="">All Status</option>
                  <option :value="true">Active</option>
                  <option :value="false">Inactive</option>
                </SelectInput>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-medium text-gray-900">Holidays</h3>
              <Link
                :href="route('admin.holidays.create')"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
              >
                Add Holiday
              </Link>
            </div>
          </div>

          <!-- Holiday List -->
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Location
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
                <tr v-for="holiday in holidays" :key="holiday.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                      {{ holiday.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ holiday.description }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                        'bg-blue-100 text-blue-800': holiday.type === 'public',
                        'bg-green-100 text-green-800': holiday.type === 'company',
                        'bg-purple-100 text-purple-800': holiday.type === 'location'
                      }"
                    >
                      {{ holiday.type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(holiday.date) }}
                    <div v-if="holiday.is_recurring" class="text-xs text-gray-400">
                      Recurring
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ holiday.location?.name || 'All Locations' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button
                      @click="toggleStatus(holiday)"
                      :class="{
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full': true,
                        'bg-green-100 text-green-800': holiday.is_active,
                        'bg-red-100 text-red-800': !holiday.is_active
                      }"
                    >
                      {{ holiday.is_active ? 'Active' : 'Inactive' }}
                    </button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link
                      :href="route('admin.holidays.edit', holiday.id)"
                      class="text-indigo-600 hover:text-indigo-900 mr-4"
                    >
                      Edit
                    </Link>
                    <button
                      @click="deleteHoliday(holiday)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import SelectInput from '@/Components/SelectInput.vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  holidays: Array,
  locations: Array,
  filters: Object
})

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  return Array.from({ length: 5 }, (_, i) => currentYear + i)
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const filterHolidays = () => {
  router.get(route('admin.holidays.index'), props.filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const toggleStatus = (holiday) => {
  router.post(route('admin.holidays.toggle', holiday.id), {}, {
    preserveScroll: true
  })
}

const deleteHoliday = (holiday) => {
  if (confirm('Are you sure you want to delete this holiday?')) {
    router.delete(route('admin.holidays.destroy', holiday.id), {
      preserveScroll: true
    })
  }
}
</script> 