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
          <div class="p-6">
            <!-- Filters -->
            <div class="mb-6 flex flex-wrap gap-4">
              <div class="flex-1 min-w-[200px]">
                <InputLabel for="search" value="Search" />
                <TextInput
                  id="search"
                  v-model="search"
                  type="text"
                  class="mt-1 block w-full"
                  placeholder="Search holidays..."
                />
              </div>
              <div class="w-[200px]">
                <InputLabel for="year" value="Year" />
                <SelectInput
                  id="year"
                  v-model="year"
                  class="mt-1 block w-full"
                >
                  <option value="">All Years</option>
                  <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                </SelectInput>
              </div>
              <div class="w-[200px]">
                <InputLabel for="location" value="Location" />
                <SelectInput
                  id="location"
                  v-model="location_id"
                  class="mt-1 block w-full"
                >
                  <option value="">All Locations</option>
                  <option v-for="location in locations" :key="location.id" :value="location.id">
                    {{ location.name }}
                  </option>
                </SelectInput>
              </div>
            </div>

            <!-- Add Holiday Button -->
            <div class="mb-6">
              <PrimaryButton @click="showAddModal = true">
                Add Holiday
              </PrimaryButton>
            </div>

            <!-- Holidays Table -->
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recurrence</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="holiday in holidays.data" :key="holiday.id">
                    <td class="px-4 py-2 whitespace-nowrap">{{ holiday.name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ formatDate(holiday.date) }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      <span :class="{
                        'px-2 py-1 text-xs rounded-full': true,
                        'bg-blue-100 text-blue-800': holiday.type === 'public',
                        'bg-green-100 text-green-800': holiday.type === 'company',
                        'bg-purple-100 text-purple-800': holiday.type === 'special'
                      }">
                        {{ holiday.type }}
                      </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      {{ holiday.location?.name || 'All Locations' }}
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      <span v-if="!holiday.recurrence || holiday.recurrence === 'none'" class="text-gray-500">No Recurrence</span>
                      <span v-else class="text-indigo-600">
                        {{ holiday.recurrence.charAt(0).toUpperCase() + holiday.recurrence.slice(1) }}
                        <span v-if="holiday.recurrence_end_date" class="text-gray-500 text-xs">
                          until {{ formatDate(holiday.recurrence_end_date) }}
                        </span>
                      </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">
                      <span :class="{
                        'px-2 py-1 text-xs rounded-full': true,
                        'bg-green-100 text-green-800': holiday.is_active,
                        'bg-red-100 text-red-800': !holiday.is_active
                      }">
                        {{ holiday.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                      <button
                        @click="editHoliday(holiday)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </button>
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

            <!-- Pagination -->
            <div class="mt-6">
              <Pagination :links="holidays.links" :meta="holidays.meta" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Holiday Modal -->
    <Modal :show="showAddModal" @close="() => { showAddModal = false; resetForm(); }">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">
          {{ editingHoliday ? 'Edit Holiday' : 'Add Holiday' }}
        </h2>

        <form @submit.prevent="submit">
          <div class="space-y-4">
            <div>
              <InputLabel for="name" value="Holiday Name" />
              <TextInput
                id="name"
                v-model="form.name"
                type="text"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div>
              <InputLabel for="description" value="Description" />
              <TextArea
                id="description"
                v-model="form.description"
                class="mt-1 block w-full"
                rows="3"
              />
              <InputError :message="form.errors.description" class="mt-2" />
            </div>

            <div>
              <InputLabel for="date" value="Initial Date" />
              <TextInput
                id="date"
                v-model="form.date"
                type="date"
                class="mt-1 block w-full"
                required
              />
              <InputError :message="form.errors.date" class="mt-2" />
            </div>

            <div>
              <InputLabel for="type" value="Type" />
              <SelectInput
                id="type"
                v-model="form.type"
                class="mt-1 block w-full"
                required
              >
                <option value="public">Public Holiday</option>
                <option value="company">Company Holiday</option>
                <option value="special">Special Event</option>
              </SelectInput>
              <InputError :message="form.errors.type" class="mt-2" />
            </div>

            <div>
              <InputLabel for="recurrence" value="Recurrence" />
              <SelectInput
                id="recurrence_type"
                v-model="form.recurrence_type"
                class="mt-1 block w-full"
                @change="handleRecurrenceTypeChange"
              >
                <option value="none">No Recurrence</option>
                <option value="fixed">Fixed Date (Yearly)</option>
                <option value="easter">Easter-based</option>
                <option value="custom">Custom Rule</option>
              </SelectInput>
              <InputError :message="form.errors.recurrence_type" class="mt-2" />
            </div>

            <div v-if="form.recurrence_type === 'fixed'">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <InputLabel for="recurrence_month" value="Month" />
                  <SelectInput
                    id="recurrence_month"
                    v-model="form.recurrence_month"
                    class="mt-1 block w-full"
                  >
                    <option v-for="month in 12" :key="month" :value="month">
                      {{ new Date(2000, month - 1).toLocaleString('default', { month: 'long' }) }}
                    </option>
                  </SelectInput>
                </div>
                <div>
                  <InputLabel for="recurrence_day" value="Day" />
                  <TextInput
                    id="recurrence_day"
                    v-model="form.recurrence_day"
                    type="number"
                    min="1"
                    max="31"
                    class="mt-1 block w-full"
                  />
                </div>
              </div>
            </div>

            <div v-if="form.recurrence_type === 'easter'">
              <InputLabel for="easter_offset" value="Days from Easter Sunday" />
              <TextInput
                id="easter_offset"
                v-model="form.easter_offset"
                type="number"
                class="mt-1 block w-full"
                placeholder="e.g., -2 for Good Friday, 1 for Easter Monday"
              />
            </div>

            <div v-if="form.recurrence_type === 'custom'">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <InputLabel for="custom_month" value="Month" />
                  <SelectInput
                    id="custom_month"
                    v-model="form.custom_rule.month"
                    class="mt-1 block w-full"
                  >
                    <option v-for="month in 12" :key="month" :value="month">
                      {{ new Date(2000, month - 1).toLocaleString('default', { month: 'long' }) }}
                    </option>
                  </SelectInput>
                </div>
                <div>
                  <InputLabel for="custom_day" value="Day of Week" />
                  <SelectInput
                    id="custom_day"
                    v-model="form.custom_rule.day"
                    class="mt-1 block w-full"
                  >
                    <option value="1">Monday</option>
                    <option value="2">Tuesday</option>
                    <option value="3">Wednesday</option>
                    <option value="4">Thursday</option>
                    <option value="5">Friday</option>
                    <option value="6">Saturday</option>
                    <option value="7">Sunday</option>
                  </SelectInput>
                </div>
              </div>
              <div class="mt-4">
                <InputLabel for="custom_type" value="Occurrence" />
                <SelectInput
                  id="custom_type"
                  v-model="form.custom_rule.type"
                  class="mt-1 block w-full"
                >
                  <option value="first">First</option>
                  <option value="last">Last</option>
                  <option value="nth">Nth</option>
                </SelectInput>
              </div>
              <div v-if="form.custom_rule.type === 'nth'" class="mt-4">
                <InputLabel for="custom_n" value="Nth Occurrence" />
                <TextInput
                  id="custom_n"
                  v-model="form.custom_rule.n"
                  type="number"
                  min="1"
                  max="5"
                  class="mt-1 block w-full"
                />
              </div>
            </div>

            <div>
              <InputLabel for="location_id" value="Location" />
              <SelectInput
                id="location_id"
                v-model="form.location_id"
                class="mt-1 block w-full"
              >
                <option value="">All Locations</option>
                <option v-for="location in locations" :key="location.id" :value="location.id">
                  {{ location.name }}
                </option>
              </SelectInput>
              <InputError :message="form.errors.location_id" class="mt-2" />
            </div>

            <div>
              <label class="flex items-center">
                <Checkbox v-model:checked="form.is_active" />
                <span class="ml-2 text-sm text-gray-600">Active</span>
              </label>
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <SecondaryButton type="button" @click="showAddModal = false" class="mr-3">
              Cancel
            </SecondaryButton>
            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
              {{ editingHoliday ? 'Update' : 'Create' }}
            </PrimaryButton>
          </div>
        </form>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import Checkbox from '@/Components/Checkbox.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import Pagination from '@/Components/Pagination.vue'
import { useToast } from '@/composables/useToast'
import TextArea from '@/Components/TextArea.vue'

const props = defineProps({
  holidays: Object,
  locations: Array,
  years: Array,
  filters: Object
})

const { toast } = useToast()
const showAddModal = ref(false)
const editingHoliday = ref(null)
const search = ref(props.filters.search || '')
const year = ref(props.filters.year || '')
const location_id = ref(props.filters.location_id || '')

const form = useForm({
  name: '',
  date: '',
  type: 'public',
  location_id: '',
  description: '',
  is_active: true,
  is_recurring: true,
  recurrence_type: 'fixed',
  recurrence_month: 10,
  recurrence_day: 1,
  easter_offset: null,
  custom_rule: null
})

watch([search, year, location_id], () => {
  router.get(
    route('admin.holidays.index'),
    {
      search: search.value,
      year: year.value,
      location_id: location_id.value
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true
    }
  )
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const handleRecurrenceTypeChange = () => {
  if (form.recurrence_type === 'none') {
    form.is_recurring = false
    form.recurrence_day = null
    form.recurrence_month = null
    form.easter_offset = null
    form.custom_rule = null
  } else {
    form.is_recurring = true
    if (form.recurrence_type === 'custom') {
      form.custom_rule = {
        type: 'first',
        month: 1,
        day: 1,
        n: 1
      }
    }
  }
}

const resetForm = () => {
  editingHoliday.value = null
  form.reset()
  form.type = 'public'
  form.location_id = ''
  form.description = ''
  form.is_active = true
  form.is_recurring = true
  form.recurrence_type = 'fixed'
  form.recurrence_month = 10
  form.recurrence_day = 1
  form.easter_offset = null
  form.custom_rule = null
}

const editHoliday = (holiday) => {
  resetForm()
  editingHoliday.value = holiday
  form.name = holiday.name
  form.date = holiday.date.split('T')[0]
  form.type = holiday.type
  form.location_id = holiday.location_id
  form.description = holiday.description
  form.is_active = holiday.is_active
  form.is_recurring = holiday.is_recurring
  form.recurrence_type = holiday.recurrence_type || 'none'
  form.recurrence_day = holiday.recurrence_day
  form.recurrence_month = holiday.recurrence_month
  form.easter_offset = holiday.easter_offset
  form.custom_rule = holiday.custom_rule
  showAddModal.value = true
}

const deleteHoliday = (holiday) => {
  if (confirm('Are you sure you want to delete this holiday?')) {
    router.delete(route('admin.holidays.destroy', holiday.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Holiday deleted successfully')
      }
    })
  }
}

const submit = () => {
  if (editingHoliday.value) {
    form.put(route('admin.holidays.update', editingHoliday.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        showAddModal.value = false
        resetForm()
        toast.success('Holiday updated successfully')
      }
    })
  } else {
    form.post(route('admin.holidays.store'), {
      preserveScroll: true,
      onSuccess: () => {
        showAddModal.value = false
        resetForm()
        toast.success('Holiday created successfully')
      }
    })
  }
}
</script> 