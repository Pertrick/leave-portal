<template>
  <AppLayout title="New Leave Application">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          New Leave Application
        </h2>
        <Link :href="route('leaves.index')"
          class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <ArrowLeftIcon class="w-4 h-4 mr-2" />
        Back to List
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Pending Leaves Info -->
        <div v-if="pendingLeaves > 0" class="mb-6">
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-700">
                  You have {{ pendingLeaves }} pending leave {{ pendingLeaves === 1 ? 'request' : 'requests' }}. 
                  You can only save as draft until your pending requests are approved.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Leave Type -->
              <div>
                <InputLabel for="leave_type_id" value="Leave Type" />
                <SelectInput id="leave_type_id" v-model="form.leave_type_id" class="mt-1 block w-full" required>
                  <option value="">Select Leave Type</option>
                  <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
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
                        <div class="h-1.5 rounded-full transition-all duration-300" :class="{
                          'bg-green-600': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 > 60,
                          'bg-yellow-500': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 <= 60 && (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 > 30,
                          'bg-red-600': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 <= 30
                        }"
                          :style="{ width: `${(selectedLeaveBalance?.days_taken / selectedLeaveBalance?.total_entitled_days) * 100 || 0}%` }">
                        </div>
                      </div>
                      <span class="text-xs text-gray-500 mt-1 block text-right">
                        {{ Math.round((selectedLeaveBalance?.days_taken / selectedLeaveBalance?.total_entitled_days) *
                        100)
                        || 0 }}% used
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Start Date -->
              <div>
                <InputLabel for="start_date" value="Start Date" />
                <TextInput id="start_date" type="date" v-model="form.start_date" class="mt-1 block w-full" required
                  :min="minDate" @change="updateDateRange" />
                <InputError :message="form.errors.start_date" class="mt-2" />
              </div>

              <!-- End Date -->
              <div>
                <InputLabel for="end_date" value="End Date" />
                <TextInput id="end_date" type="date" v-model="form.end_date" class="mt-1 block w-full" required
                  :min="form.start_date || minDate" @change="updateDateRange" />
                <InputError :message="form.errors.end_date" class="mt-2" />
              </div>

              <!-- Duration Preview -->
              <div class="md:col-span-2">
                <div class="bg-gray-50 p-4 rounded-lg">
                  <p class="text-sm text-gray-600">
                    Duration:

                  <div v-if="durationLoading" class="inline-block">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                      viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 01-8 8z">
                      </path>
                    </svg>
                  </div>

                  <span v-else class="font-semibold text-sm">
                    {{ duration }}
                  </span>
                  </p>
                </div>
              </div>

              <!-- Reason -->
              <div class="md:col-span-2">
                <InputLabel for="reason" value="Reason" />
                <TextArea id="reason" v-model="form.reason" class="mt-1 block w-full" rows="4"
                  placeholder="Please provide a detailed reason for your leave request..." />
                <InputError :message="form.errors.reason" class="mt-2" />
              </div>

              <div class="md:col-span-2">
                <InputLabel for="applicant_comment" value="Additional Comments (Optional)" />
                <TextArea id="applicant_comment" v-model="form.applicant_comment" class="mt-1 block w-full" rows="3"
                  placeholder="Any additional information you'd like to provide..." />
                <InputError :message="form.errors.applicant_comment" class="mt-2" />
              </div>

              <!-- Replacement Staff -->
              <div>
                <InputLabel for="replacement_staff_name" value="Replacement Staff Name" />
                <TextInput id="replacement_staff_name" v-model="form.replacement_staff_name" class="mt-1 block w-full"
                  placeholder="Name of staff covering your duties" />
                <InputError :message="form.errors.replacement_staff_name" class="mt-2" />
              </div>

              <div>
                <InputLabel for="replacement_staff_phone" value="Replacement Staff Phone" />
                <TextInput id="replacement_staff_phone" v-model="form.replacement_staff_phone" class="mt-1 block w-full"
                  placeholder="Contact number of replacement staff" />
                <InputError :message="form.errors.replacement_staff_phone" class="mt-2" />
              </div>

              <!-- Attachment -->
              <div class="md:col-span-2">
                <InputLabel for="attachment" value="Supporting Document"
                  :required="selectedLeaveType?.requires_medical_proof" />
                <FileInput id="attachment" v-model="form.attachment" class="mt-1 block w-full"
                  accept=".pdf,.jpg,.jpeg,.png" :disabled="!selectedLeaveType?.requires_medical_proof"
                  :required="selectedLeaveType?.requires_medical_proof" />
                <p class="mt-1 text-sm text-gray-500">
                  <template v-if="selectedLeaveType?.requires_medical_proof">
                    Medical proof is required for this leave type. Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)
                  </template>
                  <template v-else>
                    Supporting documents are not required for this leave type.
                  </template>
                </p>
                <InputError :message="form.errors.attachment" class="mt-2" />
              </div>

              <!-- Holidays Section -->
              <div v-if="holidays.length > 0" class="mt-4">
                <h3 class="text-lg font-medium text-gray-900">Holidays in Selected Period</h3>
                <div class="mt-2 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                  <div class="flex">
                    <div class="flex-shrink-0">
                      <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                          clip-rule="evenodd" />
                      </svg>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm text-yellow-700">
                        The following holidays fall within your selected leave period:
                      </p>
                      <ul class="mt-2 text-sm text-yellow-700 list-disc list-inside">
                        <li v-for="holiday in holidays" :key="holiday.date">
                          {{ holiday.name }} ({{ formatDate(holiday.date) }})
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <SecondaryButton type="button" @click="$inertia.visit(route('leaves.index'))" class="mr-3">
                Cancel
              </SecondaryButton>
              <SecondaryButton type="button" @click="saveDraft" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing" class="mr-3">
                Save as Draft
              </SecondaryButton>
              <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="isSubmitDisabled">
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
import { ref, computed, watch, getCurrentInstance } from 'vue'
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
import { debounce } from 'lodash'
import { useFlash } from '@/composables/useFlash'

const { flash } = useFlash()
const { proxy } = getCurrentInstance()

const props = defineProps({
  leaveTypes: {
    type: Array,
    required: true,
    default: () => []
  },
  leaveBalances: {
    type: Array,
    required: true,
    default: () => []
  },
  pendingLeaves: {
    type: Number,
    required: true,
    default: 0
  }
})

const holidays = ref([])
const durationLoading = ref(false)

const form = useForm({
  leave_type_id: '',
  start_date: '',
  end_date: '',
  reason: '',
  medical_proof: null,
  status: 'draft',
  calendar_days: 0,
  working_days: 0,
  applicant_comment: '',
  replacement_staff_name: '',
  replacement_staff_phone: '',
})

const minDate = new Date().toISOString().split('T')[0]

const duration = computed(() => {
  if (!form.start_date || !form.end_date) return 0
  return `${form.working_days} working days (${form.calendar_days} calendar days)`
})

const workingDays = computed(() => {
  if (!form.start_date || !form.end_date) return 0
  return form.working_days || 0
})

const selectedLeaveBalance = computed(() => {
  if (!form.leave_type_id || !props.leaveBalances) return null
  return props.leaveBalances.find(balance => balance.leave_type_id === parseInt(form.leave_type_id))
})

const selectedLeaveType = computed(() => {
  if (!form.leave_type_id || !props.leaveTypes) return null
  return props.leaveTypes.find(type => type.id === parseInt(form.leave_type_id))
})

const isSubmitDisabled = computed(() => {
  return form.processing || 
         !form.start_date || 
         !form.end_date || 
         !form.leave_type_id || 
         props.pendingLeaves > 0
})

const submit = () => {
  // Set status to pending for submission
  form.status = 'pending'

  // Validate required fields for submission
  if (!form.reason) {
    form.errors.reason = 'Please provide a reason for your leave request.'
    return
  }

  if (!form.replacement_staff_name) {
    form.errors.replacement_staff_name = 'Please provide a valid replacement name.'
    return
  }

  if (!form.replacement_staff_phone) {
    form.errors.replacement_staff_phone = 'Please provide a valid replacement phone number.'
    return
  }

  form.post(route('leaves.store'), {
    preserveScroll: true,
    onSuccess: () => {
      if (flash.value?.success) {
        proxy.$toast.success(flash.value.success)
      }
    },
    onError: (errors) => {
      if (flash.value?.error) {
        proxy.$toast.error(flash.value.error)
      }
    }
  })
}

const saveDraft = () => {
  // Set status to draft
  form.status = 'draft'
  
  form.post(route('leaves.draft'), {
    preserveScroll: true,
    onSuccess: () => {
      if (flash.value?.success) {
        proxy.$toast.success(flash.value.success)
      }
    },
    onError: (errors) => {
      if (flash.value?.error) {
        proxy.$toast.error(flash.value.error)
      }
    }
  })
}

const updateDateRange = async () => {
  if (form.start_date && form.end_date) {
    try {
      const response = await axios.get('admin/api/holidays', {
        params: {
          start_date: form.start_date,
          end_date: form.end_date
        }
      })
      holidays.value = response.data
    } catch (error) {
      console.error('Error fetching holidays:', error)
    }
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const calculateDuration = debounce((start, end, type) => {
  durationLoading.value = true
  axios.post(route('leaves.calculate-duration'), {
    start_date: start,
    end_date: end,
    leave_type_id: type
  })
    .then(response => {
      form.calendar_days = response.data.calendar_days
      form.working_days = response.data.working_days
    })
    .catch(error => {
      proxy.$toast.error(error.response?.data?.message || 'Failed to calculate leave duration')
      console.error(error)
    })
    .finally(() => {
      durationLoading.value = false
    })
}, 300)

watch([() => form.start_date, () => form.end_date, () => form.leave_type_id],
  ([newStartDate, newEndDate, newLeaveType]) => {
    if (newStartDate && newEndDate && newLeaveType) {
      calculateDuration(newStartDate, newEndDate, newLeaveType)
    }
  }
)

watch(() => form.leave_type_id, (newTypeId) => {
  const selectedType = props.leaveTypes.find(type => type.id === newTypeId)
  if (!selectedType?.requires_medical_proof) {
    form.attachment = null
  }
})

watch(() => form.reason, () => {
  if (form.errors.reason) {
    form.errors.reason = ''
  }
})

watch(() => form.replacement_staff_name, () => {
  if (form.errors.replacement_staff_name) {
    form.errors.replacement_staff_name = ''
  }
})

watch(() => form.replacement_staff_phone, () => {
  if (form.errors.replacement_staff_phone) {
    form.errors.replacement_staff_phone = ''
  }
})
</script>