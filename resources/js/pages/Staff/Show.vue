<template>
  <AppLayout 
    title="Staff Profile"
    :breadcrumbs="breadcrumbs"
  >
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex justify-end mb-6">
              <div class="flex space-x-2">
                <Link
                  :href="route('staff.edit', staff.id)"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                  Edit
                </Link>
                <Link
                  :href="route('staff.list')"
                  class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >
                  Back to List
                </Link>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Personal Information -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700">Staff ID</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.staff_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Full Name</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.full_name }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Email</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.email }}</div>
                </div>

                <div>
                  <h4 class="text-sm font-medium text-gray-500">Date of Birth</h4>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(staff.dob) }}</p>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-500">Join Date</h4>
                  <p class="mt-1 text-sm text-gray-900">{{ formatDate(staff.join_date) }}</p>
                </div>
              </div>

              <!-- Employment Information -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Employment Information</h3>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Department</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.department?.name }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">User Level</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.user_level?.name }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Designation</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.designation }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Role</label>
                  <div class="mt-1 text-sm text-gray-900">{{ staff.roles?.[0]?.name || 'No Role' }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <div class="mt-1">
                    <span
                      :class="[
                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                        staff.is_active
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      ]"
                    >
                      {{ staff.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Leave Balances -->
            <div class="mt-8 border-t border-gray-200 pt-6">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Leave Balances</h3>
                <Link
                  :href="route('staff.leave-balances', staff.id)"
                  class="inline-flex items-center px-3 py-1.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                >
                  Edit Balances
                </Link>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="balance in staff.leave_balances" :key="balance.id" class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex justify-between items-start">
                    <div>
                      <div class="text-base font-semibold text-gray-900 mb-2">{{ balance.leave_type?.name }}</div>
                      <div class="space-y-1">
                        <div class="text-sm text-gray-500">
                          Year: {{ balance.year }}
                        </div>
                        <div class="text-sm text-gray-500">
                          Entitled: {{ balance.total_entitled_days }} days
                        </div>
                        <div class="text-sm text-gray-500">
                          Taken: {{ balance.days_taken }} days
                        </div>
                        <div class="text-sm text-gray-500">
                          Remaining: {{ balance.days_remaining }} days
                        </div>
                        <div class="text-sm text-gray-500">
                          Carried Forward: {{ balance.days_carried_forward }} days
                        </div>
                      </div>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium" :class="{
                        'text-green-600': balance.days_remaining > 0,
                        'text-red-600': balance.days_remaining <= 0
                      }">
                        {{ balance.total_entitled_days > 0 
                            ? Math.round((balance.days_remaining / balance.total_entitled_days) * 100) 
                            : 0 }}% Available
                      </div>
                    </div>
                  </div>
                </div>
                <div v-if="!staff.leave_balances?.length" class="col-span-3 text-center py-4 text-gray-500">
                  No leave balances found
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { usePermission } from '@/composables/usePermission'
import { useDateFormat } from '@/composables/useDateFormat'
import { computed } from 'vue'

const { can } = usePermission()
const { formatDate } = useDateFormat()

const props = defineProps({
  staff: Object,
})

const breadcrumbs = computed(() => [
  { id: 1, title: 'Staff', path: 'staff.list' },
  { id: 2, title: props.staff.full_name, path: '' }
])
</script> 