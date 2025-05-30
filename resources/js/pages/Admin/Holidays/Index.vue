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
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="holiday in holidays.data" :key="holiday.id">
                    <td class="px-6 py-4 whitespace-nowrap">{{ holiday.name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(holiday.date) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="{
                        'px-2 py-1 text-xs rounded-full': true,
                        'bg-blue-100 text-blue-800': holiday.type === 'public',
                        'bg-green-100 text-green-800': holiday.type === 'company',
                        'bg-purple-100 text-purple-800': holiday.type === 'special'
                      }">
                        {{ holiday.type }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      {{ holiday.location?.name || 'All Locations' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="{
                        'px-2 py-1 text-xs rounded-full': true,
                        'bg-green-100 text-green-800': holiday.is_active,
                        'bg-red-100 text-red-800': !holiday.is_active
                      }">
                        {{ holiday.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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
              <Pagination :links="holidays.links" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Holiday Modal -->
    <Modal :show="showAddModal" @close="showAddModal = false">
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
              <InputLabel for="date" value="Date" />
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
  is_active: true
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

const editHoliday = (holiday) => {
  editingHoliday.value = holiday
  form.name = holiday.name
  form.date = holiday.date
  form.type = holiday.type
  form.location_id = holiday.location_id
  form.is_active = holiday.is_active
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
        editingHoliday.value = null
        form.reset()
        toast.success('Holiday updated successfully')
      }
    })
  } else {
    form.post(route('admin.holidays.store'), {
      preserveScroll: true,
      onSuccess: () => {
        showAddModal.value = false
        form.reset()
        toast.success('Holiday created successfully')
      }
    })
  }
}
</script> 