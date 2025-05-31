<template>
  <AppLayout title="Edit Holiday">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Holiday
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Basic Information -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
              </div>

              <div>
                <InputLabel for="name" value="Name" />
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

              <div class="col-span-2">
                <InputLabel for="description" value="Description" />
                <TextArea
                  id="description"
                  v-model="form.description"
                  class="mt-1 block w-full"
                  rows="3"
                />
                <InputError :message="form.errors.description" class="mt-2" />
              </div>

              <!-- Recurrence Settings -->
              <div class="col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recurrence Settings</h3>
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

              <!-- Status -->
              <div class="col-span-2">
                <div class="flex items-center">
                  <input
                    id="is_active"
                    v-model="form.is_active"
                    type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                  />
                  <InputLabel for="is_active" value="Active" class="ml-2" />
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <SecondaryButton
                type="button"
                @click="$inertia.visit(route('admin.holidays.index'))"
                class="mr-3"
              >
                Cancel
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Update Holiday
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

const props = defineProps({
  holiday: Object,
  locations: Array
})

const form = useForm({
  name: props.holiday.name,
  date: props.holiday.date.split('T')[0],
  type: props.holiday.type,
  location_id: props.holiday.location_id,
  description: props.holiday.description,
  is_active: props.holiday.is_active,
  is_recurring: props.holiday.is_recurring,
  recurrence_type: props.holiday.recurrence_type || 'none',
  recurrence_day: props.holiday.recurrence_day,
  recurrence_month: props.holiday.recurrence_month,
  easter_offset: props.holiday.easter_offset,
  custom_rule: props.holiday.custom_rule
})

const handleRecurrenceTypeChange = () => {
  if (form.recurrence_type === 'none') {
    form.is_recurring = false
    form.recurrence_day = null
    form.recurrence_month = null
    form.easter_offset = null
    form.custom_rule = null
  } else {
    form.is_recurring = true
    if (form.recurrence_type === 'custom' && !form.custom_rule) {
      form.custom_rule = {
        type: 'first',
        month: 1,
        day: 1,
        n: 1
      }
    }
  }
}

const submit = () => {
  form.put(route('admin.holidays.update', props.holiday.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Handle success
    }
  })
}
</script> 