<template>
  <AppLayout :title="`Edit Leave Application #${leave.id}`">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Edit Leave Application
        </h2>
        <Link
          :href="route('leaves.show', leave.id)"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
          <ArrowLeftIcon class="w-4 h-4 mr-2" />
          Back to Details
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6">
            <form @submit.prevent="submit">
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
                  
                  <!-- Leave Balance Display -->
                  <div v-if="form.leave_type_id" class="mt-2">
                    <div class="bg-gray-50 p-3 rounded-lg">
                      <p class="text-sm text-gray-600">
                        Leave Balance: 
                        <span class="font-semibold">
                          {{ selectedLeaveBalance?.days_remaining || 0 }} days
                        </span>
                        <span class="text-xs text-gray-500 ml-2">
                          ({{ selectedLeaveBalance?.total_entitled_days || 0 }} days total)
                        </span>
                      </p>
                      <!-- Progress Bar -->
                      <div class="mt-2 relative">
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                          <div 
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="{
                              'bg-green-600': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 > 60,
                              'bg-yellow-500': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 <= 60 && (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 > 30,
                              'bg-red-600': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 <= 30
                            }"
                            :style="{ width: `${(selectedLeaveBalance?.days_taken / selectedLeaveBalance?.total_entitled_days) * 100 || 0}%` }"
                          ></div>
                        </div>
                        <span class="text-xs text-gray-500 mt-1 block text-right">
                          {{ Math.round((selectedLeaveBalance?.days_taken / selectedLeaveBalance?.total_entitled_days) * 100) || 0 }}% used
                        </span>
                      </div>
                    </div>
                  </div>
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
                  <div class="mt-1">
                    <div v-if="leave.attachment" class="mb-2">
                      <a
                        :href="leave.attachment"
                        target="_blank"
                        class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900"
                      >
                        <DocumentIcon class="w-4 h-4 mr-1" />
                        Current Document
                      </a>
                    </div>
                    <FileInput
                      id="attachment"
                      v-model="form.attachment"
                      class="block w-full"
                      accept=".pdf,.jpg,.jpeg,.png"
                    />
                    <p class="mt-1 text-sm text-gray-500">
                      Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)
                    </p>
                  </div>
                  <InputError :message="form.errors.attachment" class="mt-2" />
                </div>
              </div>

              <div class="mt-6 flex justify-end space-x-3">
                <Link
                  :href="route('leaves.show', leave.id)"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Cancel
                </Link>
                <button
                  type="button"
                  @click="saveAsDraft"
                  class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                  :disabled="form.processing"
                >
                  Save as Draft
                </button>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                  :disabled="form.processing"
                >
                  Submit Application
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
import { ref, computed, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import SelectInput from '@/Components/SelectInput.vue'
import FileInput from '@/Components/FileInput.vue'
import InputError from '@/Components/InputError.vue'
import { ArrowLeftIcon, DocumentIcon } from '@heroicons/vue/24/outline/index.js'

const props = defineProps({
  leave: Object,
  leaveTypes: Array,
  leaveBalances: {
    type: Array,
    required: true,
    default: () => []
  }
})

const form = useForm({
  leave_type_id: props.leave.leave_type_id,
  start_date: props.leave.start_date,
  end_date: props.leave.end_date,
  reason: props.leave.reason,
  applicant_comment: props.leave.applicant_comment || '',
  replacement_staff_name: props.leave.replacement_staff_name || '',
  replacement_staff_phone: props.leave.replacement_staff_phone || '',
  attachment: null,
  status: 'pending',
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

const selectedLeaveBalance = computed(() => {
  if (!form.leave_type_id || !props.leaveBalances) return null
  return props.leaveBalances.find(balance => balance.leave_type_id === parseInt(form.leave_type_id))
})

const selectedLeaveType = computed(() => {
  if (!form.leave_type_id || !props.leaveTypes) return null
  return props.leaveTypes.find(type => type.id === parseInt(form.leave_type_id))
})

const submit = () => {
  // Validate attachment for medical leave types
  if (selectedLeaveType.value?.requires_medical_proof && !form.attachment && !props.leave.attachment) {
    form.errors.attachment = 'Medical proof is required for this leave type.'
    return
  }

  // Validate reason for non-draft submissions
  if (form.status !== 'draft' && !form.reason) {
    form.errors.reason = 'Please provide a reason for your leave request.'
    return
  }

  form.put(route('leaves.update', props.leave.id), {
    onSuccess: () => {
      // The success message will be handled by the flash message in the layout
    },
    onError: () => {
      // The error message will be handled by the flash message in the layout
    },
  })
}

const saveAsDraft = () => {
  form.status = 'draft'
  form.put(route('leaves.update', props.leave.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.clearErrors()
    },
  })
}

// Watch for leave type changes to reset attachment if not required
watch(() => form.leave_type_id, (newTypeId) => {
  const selectedType = props.leaveTypes.find(type => type.id === newTypeId)
  if (!selectedType?.requires_medical_proof) {
    form.attachment = null
  }
})
</script> 