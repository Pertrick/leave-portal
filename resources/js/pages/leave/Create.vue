<template>
  <AppLayout title="New Leave Application">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          New Leave Application
        </h2>
        <Link
          :href="route('leaves.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          <ArrowLeftIcon class="w-4 h-4 mr-2" />
          Back to List
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Leave Type -->
              <div>
                <InputLabel for="leave_type_id" value="Leave Type" />
                <SelectInput
                  id="leave_type_id"
                  v-model="form.leave_type_id"
                  class="mt-1 block w-full"
                  required
                >
                  <option value="">Select Leave Type</option>
                  <option
                    v-for="type in leaveTypes"
                    :key="type.id"
                    :value="type.id"
                  >
                    {{ type.name }}
                  </option>
                </SelectInput>
                <InputError :message="form.errors.leave_type_id" class="mt-2" />
              </div>

              <!-- Start Date -->
              <div>
                <InputLabel for="start_date" value="Start Date" />
                <TextInput
                  id="start_date"
                  type="date"
                  v-model="form.start_date"
                  class="mt-1 block w-full"
                  required
                  :min="minDate"
                />
                <InputError :message="form.errors.start_date" class="mt-2" />
              </div>

              <!-- End Date -->
              <div>
                <InputLabel for="end_date" value="End Date" />
                <TextInput
                  id="end_date"
                  type="date"
                  v-model="form.end_date"
                  class="mt-1 block w-full"
                  required
                  :min="form.start_date || minDate"
                />
                <InputError :message="form.errors.end_date" class="mt-2" />
              </div>

              <!-- Duration Preview -->
              <div class="md:col-span-2">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <p class="text-sm text-gray-600">
                    Duration: <span class="font-semibold">{{ duration }} days</span>
                  </p>
                </div>
              </div>

              <!-- Reason -->
              <div class="md:col-span-2">
                <InputLabel for="reason" value="Reason" />
                <TextArea
                  id="reason"
                  v-model="form.reason"
                  class="mt-1 block w-full"
                  rows="4"
                  required
                  placeholder="Please provide a detailed reason for your leave request..."
                />
                <InputError :message="form.errors.reason" class="mt-2" />
              </div>

              <!-- Applicant Comment -->
              <div class="md:col-span-2">
                <InputLabel for="applicant_comment" value="Additional Comments (Optional)" />
                <TextArea
                  id="applicant_comment"
                  v-model="form.applicant_comment"
                  class="mt-1 block w-full"
                  rows="3"
                  placeholder="Any additional information you'd like to provide..."
                />
                <InputError :message="form.errors.applicant_comment" class="mt-2" />
              </div>

              <!-- Replacement Staff -->
              <div>
                <InputLabel for="replacement_staff_name" value="Replacement Staff Name (Optional)" />
                <TextInput
                  id="replacement_staff_name"
                  v-model="form.replacement_staff_name"
                  class="mt-1 block w-full"
                  placeholder="Name of staff covering your duties"
                />
                <InputError :message="form.errors.replacement_staff_name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="replacement_staff_phone" value="Replacement Staff Phone (Optional)" />
                <TextInput
                  id="replacement_staff_phone"
                  v-model="form.replacement_staff_phone"
                  class="mt-1 block w-full"
                  placeholder="Contact number of replacement staff"
                />
                <InputError :message="form.errors.replacement_staff_phone" class="mt-2" />
              </div>

              <!-- Attachment -->
              <div class="md:col-span-2">
                <InputLabel for="attachment" value="Supporting Document (Optional)" />
                <FileInput
                  id="attachment"
                  v-model="form.attachment"
                  class="mt-1 block w-full"
                  accept=".pdf,.jpg,.jpeg,.png"
                />
                <p class="mt-1 text-sm text-gray-500">
                  Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)
                </p>
                <InputError :message="form.errors.attachment" class="mt-2" />
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <SecondaryButton
                type="button"
                @click="$inertia.visit(route('leaves.index'))"
                class="mr-3"
              >
                Cancel
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
              >
                Submit Application
              </PrimaryButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import SelectInput from '@/Components/SelectInput.vue'
import FileInput from '@/Components/FileInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ArrowLeftIcon } from '@heroicons/vue/24/outline/index.js'

const props = defineProps({
  leaveTypes: Array,
})

const form = useForm({
  leave_type_id: '',
  start_date: '',
  end_date: '',
  reason: '',
  applicant_comment: '',
  replacement_staff_name: '',
  replacement_staff_phone: '',
  attachment: null,
})

const minDate = new Date().toISOString().split('T')[0]

const duration = computed(() => {
  if (!form.start_date || !form.end_date) return 0
  const start = new Date(form.start_date)
  const end = new Date(form.end_date)
  const diffTime = Math.abs(end - start)
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return diffDays + 1 // Include both start and end dates
})

const submit = () => {
  form.post(route('leaves.store'), {
    onSuccess: () => {
      form.reset()
    },
  })
}
</script> 