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
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
              <!-- Left Column - Leave Type and Balance -->
              <div class="lg:col-span-4">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                  <InputLabel for="leave_type_id" value="Leave Type" class="text-gray-700 font-medium text-lg mb-2" />
                  <SelectInput
                    id="leave_type_id"
                    v-model="form.leave_type_id"
                    class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base"
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
                  <div v-if="form.leave_type_id" class="mt-6">
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                      <h3 class="text-lg font-semibold text-gray-900 mb-3">Leave Balance</h3>
                      <div class="flex items-center justify-between mb-2">
                        <span class="text-gray-600">Days Remaining</span>
                        <span class="text-2xl font-bold text-indigo-600">
                          {{ selectedLeaveBalance?.days_remaining || 0 }}
                        </span>
                      </div>
                      <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <span>Total Entitled Days</span>
                        <span>{{ selectedLeaveBalance?.total_entitled_days || 0 }}</span>
                      </div>
                      <!-- Progress Bar -->
                      <div class="relative">
                        <div class="w-full bg-gray-200 rounded-full h-3">
                          <div class="h-3 rounded-full transition-all duration-300" 
                            :class="{
                              'bg-green-500': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 > 60,
                              'bg-amber-500': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 <= 60 && (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 > 30,
                              'bg-red-500': (selectedLeaveBalance?.days_remaining / selectedLeaveBalance?.total_entitled_days) * 100 <= 30
                            }"
                            :style="{ width: `${(selectedLeaveBalance?.days_taken / selectedLeaveBalance?.total_entitled_days) * 100 || 0}%` }">
                          </div>
                        </div>
                        <span class="text-xs text-gray-500 mt-2 block text-right">
                          {{ Math.round((selectedLeaveBalance?.days_taken / selectedLeaveBalance?.total_entitled_days) * 100) || 0 }}% used
                        </span>
                      </div>
                      </div>
                    </div>
                  </div>
                </div>

              <!-- Right Column - Calendar and Form -->
              <div class="lg:col-span-8">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-8">
                  <div class="flex items-center justify-between mb-4">
                    <InputLabel value="Leave Period" class="text-gray-700 font-medium text-lg" />
                </div>

                  <DatePicker
                    v-model.range="dateRange"
                    mode="range"
                    :min-date="allowBackdate ? null : new Date()"
                    :max-date="maxDate"
                    :attributes="calendarAttributes"
                    :trim-weeks="false"
                    class="mt-1 border border-gray-300 rounded-xl shadow-sm w-full"
                    :masks="{ weekdays: 'WWW' }"
                    :first-day-of-week="1"
                    :popover="{ visibility: 'click' }"
                    :panes="2"
                    :show-adjacent-months="true"
                    :theme-styles="{
                      wrapper: 'rounded-xl shadow-lg',
                      header: 'bg-indigo-50 p-6',
                      headerTitle: 'text-indigo-900 font-semibold text-xl',
                      navButton: 'text-indigo-600 hover:bg-indigo-100 rounded-lg p-3',
                      navButtonIcon: 'text-indigo-600 w-6 h-6',
                      navButtonPrev: 'text-indigo-600',
                      navButtonNext: 'text-indigo-600',
                      weekdays: 'text-indigo-900 font-medium text-base',
                      day: 'text-gray-700 hover:bg-indigo-50 rounded-lg',
                      dayContent: 'w-12 h-12 flex items-center justify-center text-base',
                      dayContentSelected: 'bg-indigo-600 text-white font-semibold',
                      dayContentToday: 'text-indigo-600 font-semibold',
                      dayContentHoliday: 'text-red-600 font-medium',
                      dayContentWeekend: 'text-gray-400'
                    }"
                  />

                  <InputError :message="form.errors.start_date || form.errors.end_date" class="mt-2" />
                </div>

                <!-- Duration Preview -->
                <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100 mb-8">
                  <p class="text-base text-indigo-700">
                      Duration: 
                      <div v-if="durationLoading" class="inline-block">
                      <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3.5-3.5L12 0v4a8 8 0 01-8 8z"></path>
                      </svg>
                    </div>
                    <span v-else class="font-semibold text-indigo-700 text-lg">
                      {{ duration }}
                    </span>
                    </p>
                  
                  <!-- Leave Balance Error Message -->
                  <div v-if="balanceErrorMessage" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                      <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                      </svg>
                      <p class="text-sm text-red-700 font-medium">
                        {{ balanceErrorMessage }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Reason -->
                <div class="md:col-span-2">
                    <label for="reason" class="block text-sm font-semibold text-gray-900 mb-4">
                      Reason for Leave <span class="text-red-500">*</span>
                    </label>
                  <TextArea
                    id="reason"
                    v-model="form.reason"
                      class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-6 py-4 text-base"
                    rows="4"
                    placeholder="Please provide a detailed reason for your leave request..."
                  />
                    <InputError :message="form.errors.reason" class="mt-3" />
                </div>

                  <!-- Additional Comments -->
                <div class="md:col-span-2">
                    <label for="applicant_comment" class="block text-sm font-semibold text-gray-900 mb-4">
                      Additional Comments
                    </label>
                  <TextArea
                    id="applicant_comment"
                    v-model="form.applicant_comment"
                      class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-6 py-4 text-base"
                    rows="3"
                    placeholder="Any additional information you'd like to provide..."
                  />
                    <InputError :message="form.errors.applicant_comment" class="mt-3" />
                </div>

                <!-- Replacement Staff -->
                <div>
                    <label for="replacement_staff_name" class="block text-sm font-semibold text-gray-900 mb-4">
                      Replacement Staff Name <span class="text-red-500">*</span>
                    </label>
                  <TextInput
                    id="replacement_staff_name"
                    v-model="form.replacement_staff_name"
                      class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-6 py-4 text-base"
                    placeholder="Name of staff covering your duties"
                  />
                    <InputError :message="form.errors.replacement_staff_name" class="mt-3" />
                </div>

                <div>
                    <label for="replacement_staff_phone" class="block text-sm font-semibold text-gray-900 mb-4">
                      Replacement Staff Phone <span class="text-red-500">*</span>
                    </label>
                  <TextInput
                    id="replacement_staff_phone"
                    v-model="form.replacement_staff_phone"
                      class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-6 py-4 text-base"
                    placeholder="Contact number of replacement staff"
                  />
                    <InputError :message="form.errors.replacement_staff_phone" class="mt-3" />
                </div>

                <!-- Attachment -->
                <div class="md:col-span-2">
                    <label for="attachment" class="block text-sm font-semibold text-gray-900 mb-4">
                      Supporting Document
                      <span v-if="selectedLeaveType?.requires_medical_proof" class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                      <div v-if="leave.attachment" class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                      <a
                        :href="leave.attachment"
                        target="_blank"
                        class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900"
                      >
                            <DocumentIcon class="w-4 h-4 mr-2" />
                        Current Document
                      </a>
                      <button
                        type="button"
                        @click="removeAttachment"
                            class="text-sm text-red-600 hover:text-red-900"
                      >
                        Remove
                      </button>
                        </div>
                    </div>
                    <FileInput
                      id="attachment"
                      @change="handleFileChange"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-6 py-4 text-base"
                      accept=".pdf,.jpg,.jpeg,.png"
                    />
                      <p class="text-sm text-gray-500">
                        <template v-if="selectedLeaveType?.requires_medical_proof">
                          Medical proof is required for this leave type. Accepted formats: PDF, JPG, JPEG, PNG (max 2MB)
                        </template>
                        <template v-else>
                          Supporting documents are not required for this leave type.
                        </template>
                      </p>
                    </div>
                    <InputError :message="form.errors.attachment" class="mt-3" />
                  </div>
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
              </div>

            <!-- Form buttons -->
            <div class="mt-8 flex justify-end space-x-4">
              <SecondaryButton 
                type="button" 
                @click="$inertia.visit(route('leaves.show', leave.id))" 
                class="px-6 py-2.5 rounded-lg font-medium"
                >
                  Cancel
              </SecondaryButton>
              <SecondaryButton 
                  type="button"
                  @click="saveAsDraft"
                :class="{ 'opacity-25': form.processing }"
                  :disabled="form.processing"
                class="px-6 py-2.5 rounded-lg font-medium bg-gray-100 hover:bg-gray-200 text-gray-700"
                >
                  Update Draft
              </SecondaryButton>
              <PrimaryButton 
                  type="submit"
                :class="{ 'opacity-25': form.processing }" 
                  :disabled="isSubmitDisabled"
                class="px-6 py-2.5 rounded-lg font-medium bg-indigo-600 hover:bg-indigo-700 text-white"
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
import { ref, computed, watch, getCurrentInstance, onMounted } from 'vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import TextArea from '@/Components/TextArea.vue'
import SelectInput from '@/Components/SelectInput.vue'
import FileInput from '@/Components/FileInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ArrowLeftIcon, DocumentIcon } from '@heroicons/vue/24/outline/index.js'
import { debounce } from 'lodash'
import { useFlash } from '@/composables/useFlash'
import { DatePicker } from 'v-calendar'

const { flash } = useFlash()
const { proxy } = getCurrentInstance()

onMounted(() => {
  fetchAllHolidays();
  if (form.start_date && form.end_date && form.leave_type_id) {
    calculateDuration(form.start_date, form.end_date, form.leave_type_id)
  }
  if (dateRange.value.start && dateRange.value.end) {
    updateDateRange()
  }
});

const props = defineProps({
  leave: Object,
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
  holidays: {
    type: Array,
    default: () => []
  },
  allowBackdate: {
    type: Boolean,
    default: false
  }
})

const allHolidays = ref([])
const holidays = ref([])
const durationLoading = ref(false)

// Add these after your existing refs
const dateRange = ref({
  start: props.leave.start_date ? new Date(props.leave.start_date) : null,
  end: props.leave.end_date ? new Date(props.leave.end_date) : null
});

const maxDate = new Date(new Date().getFullYear() + 1, 11, 31) // End of next year

// Add this computed property
// Update your calendarAttributes computed property
const calendarAttributes = computed(() => {
  const holidayAttrs = allHolidays.value.map(holiday => ({
    key: `holiday-${holiday.date}`,
    dates: new Date(holiday.date),
    dot: {
      color: 'red',
      class: 'holiday-dot'
    },
    highlight: {
      color: 'red',
      fillMode: 'light',
      class: 'holiday-highlight'
    },
    popover: {
      label: holiday.name,
      visibility: 'hover',
      hideIndicator: true
    },
    customData: {
      title: holiday.name
    }
  }));

  const rangeAttrs = (dateRange.value.start && dateRange.value.end)
    ? [{
        key: 'range',
        dates: { start: dateRange.value.start, end: dateRange.value.end },
        highlight: {
          color: 'blue',
          fillMode: 'light'
        }
      }]
    : [];

  return [
    ...holidayAttrs,
    {
      key: 'weekends',
      weekdays: [1, 7],
      highlight: {
        color: 'gray',
        fillMode: 'light'
      }
    },
    ...rangeAttrs
  ];
});

const form = useForm({
  leave_type_id: props.leave.leave_type_id,
  start_date: props.leave.start_date,
  end_date: props.leave.end_date,
  reason: props.leave.reason,
  applicant_comment: props.leave.applicant_comment || '',
  replacement_staff_name: props.leave.replacement_staff_name || '',
  replacement_staff_phone: props.leave.replacement_staff_phone || '',
  attachment: null,
  current_attachment: props.leave.attachment,
  calendar_days: props.leave.calendar_days || 0,
  working_days: props.leave.working_days || 0,
  _method: 'POST'
})

// Watch for date range changes and update form
watch(dateRange, (newRange) => {
  if (newRange.start) {
    form.start_date = newRange.start.toISOString().split('T')[0]
  }
  if (newRange.end) {
    form.end_date = newRange.end.toISOString().split('T')[0]
  }
}, { deep: true })

const updateDateRange = async () => {
  if (dateRange.value.start && dateRange.value.end) {
    try {
      const response = await axios.get('/api/holidays', {
        params: {
          start_date: formatDateToYYYYMMDD(dateRange.value.start),
          end_date: formatDateToYYYYMMDD(dateRange.value.end)
        },
        headers: {
          'Accept': 'application/json'
        },
        withCredentials: true
      });
      holidays.value = response.data;
      
      if (form.leave_type_id) {
        calculateDuration(
          formatDateToYYYYMMDD(dateRange.value.start),
          formatDateToYYYYMMDD(dateRange.value.end),
          form.leave_type_id
        );
      }
    } catch (error) {
      console.error('Error fetching holidays:', error);
    }
  }
};

// Add this helper function
const formatDateToYYYYMMDD = (date) => {
  if (!date) return '';
  // Use local date formatting to avoid timezone issues
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// Add this watch handler
watch(dateRange, (newRange) => {
  if (newRange.start && newRange.end) {
    form.start_date = formatDateToYYYYMMDD(newRange.start);
    form.end_date = formatDateToYYYYMMDD(newRange.end);
    updateDateRange();
  }
}, { deep: true });

const duration = computed(() => {
  if (!form.start_date || !form.end_date) return '0 working days (0 calendar days)'
  return `${form.working_days || 0} working days (${form.calendar_days || 0} calendar days)`
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

const handleFileChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Create a new File object to ensure it's fresh
    form.attachment = new File([file], file.name, {
      type: file.type,
      lastModified: file.lastModified
    })
  }
}

const removeAttachment = () => {
  form.attachment = null
  form.current_attachment = null
}

const isSubmitDisabled = computed(() => {
  return form.processing || 
         !form.start_date || 
         !form.end_date || 
         !form.leave_type_id ||
         (selectedLeaveBalance.value && workingDays.value > selectedLeaveBalance.value.days_remaining)
})

// Add computed property to check if leave balance is insufficient
const isInsufficientBalance = computed(() => {
  return selectedLeaveBalance.value && workingDays.value > selectedLeaveBalance.value.days_remaining
})

// Add computed property for balance error message
const balanceErrorMessage = computed(() => {
  if (isInsufficientBalance.value) {
    return `Insufficient leave balance. You have ${selectedLeaveBalance.value.days_remaining} days remaining but requested ${workingDays.value} days.`
  }
  return null
})

const submit = () => {
  // Check for insufficient leave balance first
  if (isInsufficientBalance.value) {
    proxy.$toast.error(balanceErrorMessage.value)
    return
  }

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

  if(form.status !== 'draft' && !form.replacement_staff_name) {
    form.errors.replacement_staff_name = 'Please provide a valid replacement name.'
    return
  }

  if(form.status !== 'draft' && !form.replacement_staff_phone) {
    form.errors.replacement_staff_phone = 'Please provide a valid replacement phone number.'
    return
  }

  // Validate leave balance
  if (selectedLeaveBalance.value && workingDays.value > selectedLeaveBalance.value.days_remaining) {
    proxy.$toast.error(`Insufficient leave balance. You have ${selectedLeaveBalance.value.days_remaining} days remaining but requested ${workingDays.value} days.`)
    return
  }

  console.log(form);

  // Use Inertia's form handling
  form.put(route('leaves.update', props.leave.uuid), {
    preserveScroll: true,
    onSuccess: () => {
      if (flash.value?.success) {
        proxy.$toast.success(flash.value.success)
      }
      router.visit(route('leaves.index'))
    },
    onError: (errors) => {
      if (flash.value?.error) {
        proxy.$toast.error(flash.value.error)
      }
    }
  })
}

const saveAsDraft = () => {
  form.status = 'draft'
  
  // Use Inertia's form handling
  form.post(route('leaves.draft', props.leave.uuid), {
    preserveScroll: true,
    onSuccess: () => {
      if (flash.value?.success) {
        proxy.$toast.success(flash.value.success)
      }
      router.visit(route('leaves.drafts'))
    },
    onError: (errors) => {
      if (flash.value?.error) {
        proxy.$toast.error(flash.value.error)
      }
    }
  })
}

// Watch for leave type changes to reset attachment if not required
watch(() => form.leave_type_id, (newTypeId) => {
  const selectedType = props.leaveTypes.find(type => type.id === newTypeId)
  if (!selectedType?.requires_medical_proof) {
    form.attachment = null
  }
})

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

// Format date for display
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Fetch all holidays
const fetchAllHolidays = async () => {
  try {
    const currentYear = new Date().getFullYear();
    const startDate = `${currentYear}-01-01`;
    const endDate = `${currentYear}-12-31`;
    
    const response = await axios.get('/api/range/holidays', {
      params: {
        start_date: startDate,
        end_date: endDate
      },
      headers: {
        'Accept': 'application/json'
      },
      withCredentials: true
    });
    allHolidays.value = response.data;
  } catch (error) {
    console.error('Error fetching holidays:', error);
  }
}
</script>
