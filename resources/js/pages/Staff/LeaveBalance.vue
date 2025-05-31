<template>
  <AppLayout 
    title="Manage Leave Balances"
    :breadcrumbs="breadcrumbs"
  >
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <!-- Staff Info Header -->
            <div class="mb-6">
              <h2 class="text-lg font-medium text-gray-900">{{ staff.firstname }} {{ staff.lastname }}</h2>
              <p class="text-sm text-gray-500">Staff ID: {{ staff.staff_id }}</p>
            </div>

            <!-- Leave Balances Form -->
            <form @submit.prevent="submit" class="space-y-6">
              <div v-for="(balance, index) in form.balances" :key="balance.id" class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between items-start mb-4">
                  <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ balance.leave_type }}</h3>
                    <p class="text-sm text-gray-500">Year: {{ balance.year }}</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm font-medium" :class="{
                      'text-green-600': balance.days_remaining > 0,
                      'text-red-600': balance.days_remaining <= 0
                    }">
                      {{ balance.total_entitled_days && balance.total_entitled_days > 0 
                          ? Math.round((balance.days_remaining / balance.total_entitled_days) * 100) 
                          : 0 }}% Available ({{balance.days_remaining}} days remaining)
                    </p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Total Entitled Days</label>
                    <input
                      type="number"
                      v-model="form.balances[index].total_entitled_days"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      min="0"
                      step="0.5"
                    />
                    <p class="mt-1 text-sm text-red-600" v-if="form.errors[`balances.${index}.total_entitled_days`]">
                      {{ form.errors[`balances.${index}.total_entitled_days`] }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Days Taken</label>
                    <input
                      type="number"
                      v-model="form.balances[index].days_taken"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      min="0"
                      step="0.5"
                    />
                    <p class="mt-1 text-sm text-red-600" v-if="form.errors[`balances.${index}.days_taken`]">
                      {{ form.errors[`balances.${index}.days_taken`] }}
                    </p>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700">Days Carried Forward</label>
                    <input
                      type="number"
                      v-model="form.balances[index].days_carried_forward"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                      min="0"
                      step="0.5"
                    />
                    <p class="mt-1 text-sm text-red-600" v-if="form.errors[`balances.${index}.days_carried_forward`]">
                      {{ form.errors[`balances.${index}.days_carried_forward`] }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Adjustment Reason -->
              <div>
                <label class="block text-sm font-medium text-gray-700">Reason for Adjustment</label>
                <textarea
                  v-model="form.reason"
                  rows="3"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                  :class="{ 'border-red-300': form.errors.reason }"
                  placeholder="Please provide a reason for these adjustments..."
                ></textarea>
                <p class="mt-1 text-sm text-red-600" v-if="form.errors.reason">
                  {{ form.errors.reason }}
                </p>
              </div>

              <!-- Submit Button -->
              <div class="flex justify-end space-x-3">
                <Link
                  :href="route('staff.show', staff.id)"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                  :disabled="form.processing"
                >
                  Save Changes
                </button>
              </div>
            </form>

            <!-- Audit Log -->
            <div class="mt-8 border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Adjustment History</h3>
              <div class="space-y-4">
                <div v-for="log in auditLogs" :key="log.id" class="bg-white p-4 rounded-lg border border-gray-200">
                  <div class="flex justify-between items-start">
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ log.adjusted_by.firstname }} {{ log.adjusted_by.lastname }} {{ log.adjusted_by.email }}</p>
                      <p class="text-sm text-gray-500">{{ log.reason }}</p>
                    </div>
                    <p class="text-sm text-gray-500">{{ formatDate(log.created_at) }}</p>
                  </div>
                  <div class="mt-2 text-sm text-gray-500">
                    <p class="font-medium text-gray-900">{{ log.leave_balance.leave_type.name }}</p>
                    <p>Previous Balance: {{ log.previous_balance }}</p>
                    <p>New Balance: {{ log.new_balance }}</p>
                  </div>
                </div>
                <div v-if="!auditLogs.length" class="text-center py-4 text-gray-500">
                  No adjustment history found
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <ConfirmationModal
      :show="showConfirmModal"
      type="warning"
      title="Update Leave Balances"
      message="Are you sure you want to update these leave balances?"
      confirm-text="Yes, Update"
      cancel-text="No, Cancel"
      @close="closeConfirmModal"
      @confirm="confirmSubmit"
    />
  </AppLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { useDateFormat } from '@/composables/useDateFormat'
import { computed, ref } from 'vue'
import ConfirmationModal from '@/components/ConfirmationModal.vue'
//import { useToast } from '@/composables/useToast'

const props = defineProps({
  staff: Object,
  auditLogs: Array,
})

const { formatDate } = useDateFormat()
// const toast = useToast()

const showConfirmModal = ref(false)

const form = useForm({
  balances: props.staff.leave_balances.map(balance => ({
    id: balance.id,
    leave_type : balance.leave_type.name,
    total_entitled_days: balance.total_entitled_days,
    days_taken: balance.days_taken,
    days_carried_forward: balance.days_carried_forward,
    days_remaining: balance.days_remaining,
    year : balance.year
  })),
  reason: '',
})

const breadcrumbs = computed(() => [
  { id: 1, title: 'Staff', path: 'staff.list' },
  { id: 2, title: props.staff.full_name, path: `staff.show?id=${props.staff.id}` },
  { id: 3, title: 'Leave Balances', path: '' }
])

const validateForm = () => {
  let isValid = true;
  form.errors = {};

  // Validate reason
  if (!form.reason.trim()) {
    form.errors.reason = 'Please provide a reason for the adjustment';
    isValid = false;
  } else if (form.reason.trim().length < 10) {
    form.errors.reason = 'Reason must be at least 10 characters long';
    isValid = false;
  }

  return isValid;
};

const submit = () => {
  if (!validateForm()) {
    return;
  }
  showConfirmModal.value = true;
};

const closeConfirmModal = () => {
  showConfirmModal.value = false;
};

const confirmSubmit = () => {
  form.post(route('staff.leave-balances.update', props.staff.id), {
    preserveScroll: true,
    onSuccess: () => {
      form.reason = '';
      // toast.success('Leave balances updated successfully');
      // Refresh the page to show updated data
      window.location.reload();
    },
    onError: (errors) => {
      //toast.error('Failed to update leave balances');
      console.error('Form submission errors:', errors);
    },
  });
  closeConfirmModal();
};
</script> 