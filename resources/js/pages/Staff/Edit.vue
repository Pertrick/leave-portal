<template>
  <AppLayout title="Edit Staff">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Edit Staff
        </h2>
        <Link
          :href="route('staff.list')"
          class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
        >
          Back to List
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Personal Information -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700">Staff ID</label>
                  <input
                    type="text"
                    v-model="form.staff_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  />
                  <div v-if="form.errors.staff_id" class="text-red-500 text-sm mt-1">{{ form.errors.staff_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">First Name</label>
                  <input
                    type="text"
                    v-model="form.firstname"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  />
                  <div v-if="form.errors.firstname" class="text-red-500 text-sm mt-1">{{ form.errors.firstname }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Last Name</label>
                  <input
                    type="text"
                    v-model="form.lastname"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  />
                  <div v-if="form.errors.lastname" class="text-red-500 text-sm mt-1">{{ form.errors.lastname }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Email</label>
                  <input
                    type="email"
                    v-model="form.email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  />
                  <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                </div>
              </div>

              <!-- Employment Information -->
              <div class="space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Employment Information</h3>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Department</label>
                  <SearchSelect
                    v-model="form.department_id"
                    :options="departments.map(dept => ({ value: dept.id, label: dept.name }))"
                    placeholder="Select Department"
                    searchable
                    required
                  />
                  <div v-if="form.errors.department_id" class="text-red-500 text-sm mt-1">{{ form.errors.department_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">User Level</label>
                  <SearchSelect
                    v-model="form.user_level_id"
                    :options="userLevels.map(level => ({ value: level.id, label: level.name }))"
                    placeholder="Select User Level"
                    searchable
                    required
                  />
                  <div v-if="form.errors.user_level_id" class="text-red-500 text-sm mt-1">{{ form.errors.user_level_id }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Designation</label>
                  <input
                    type="text"
                    v-model="form.designation"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                  />
                  <div v-if="form.errors.designation" class="text-red-500 text-sm mt-1">{{ form.errors.designation }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Role</label>
                  <SearchSelect
                    v-model="form.role"
                    :options="roles.map(role => ({ value: role.name, label: role.name }))"
                    placeholder="Select Role"
                    searchable
                    required
                  />
                  <div v-if="form.errors.role" class="text-red-500 text-sm mt-1">{{ form.errors.role }}</div>
                </div>
              </div>
            </div>

            <!-- Password Section -->
            <div class="mt-8 border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700">New Password</label>
                  <input
                    type="password"
                    v-model="form.password"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                  <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                  <input
                    type="password"
                    v-model="form.password_confirmation"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  />
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 flex justify-end">
              <button
                type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                :disabled="form.processing"
              >
                Update Staff
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { SearchSelect } from '@/components/ui/select'

const props = defineProps({
  staff: Object,
  departments: Array,
  userLevels: Array,
  roles: Array,
})

const form = useForm({
  staff_id: props.staff.staff_id,
  firstname: props.staff.firstname,
  lastname: props.staff.lastname,
  email: props.staff.email,
  department_id: props.staff.department_id,
  user_level_id: props.staff.user_level_id,
  designation: props.staff.designation,
  role: props.staff.roles?.[0]?.name,
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put(route('staff.update', props.staff.id), {
    onSuccess: () => {
      form.reset('password', 'password_confirmation')
    },
  })
}
</script> 