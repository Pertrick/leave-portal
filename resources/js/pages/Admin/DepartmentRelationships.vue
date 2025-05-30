<template>
  <AppLayout title="Department Relationships">
    <template #header>
      <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Department Relationships
      </h2>
        <div class="flex items-center space-x-4">
          <button
            @click="exportData"
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Export Data
          </button>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Department Selection Card -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <div class="flex-1">
              <h3 class="text-lg font-medium text-gray-900">Select Department</h3>
                <p class="mt-1 text-sm text-gray-500">Choose a department to manage its relationships</p>
              </div>
              <div class="w-64">
              <select
                v-model="selectedDepartment"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                @change="loadDepartmentData"
              >
                <option value="">Select a department</option>
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
              </div>
            </div>
            </div>
          </div>

        <div v-if="selectedDepartment" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Department Head Card -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
              <h3 class="text-lg font-medium text-gray-900">Department Head</h3>
                  <p class="mt-1 text-sm text-gray-500">Manage the department head and their responsibilities</p>
                </div>
                <div class="flex space-x-2">
                  <button
                    v-if="departmentHead"
                    @click="showAssignHeadModal = true"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Update
                  </button>
                  <button
                    v-if="!departmentHead"
                    @click="showAssignHeadModal = true"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Assign Head
                  </button>
                </div>
            </div>

              <div v-if="departmentHead" class="space-y-4">
                <div class="flex items-start space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                      <span class="text-xl font-medium text-indigo-600">
                        {{ departmentHead.user.firstname[0] }}{{ departmentHead.user.lastname[0] }}
                      </span>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-sm font-medium text-gray-900">
                          <span v-if="departmentHead.is_acting" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Acting
                          </span>
                        </p>
                        <p class="text-sm text-gray-500">{{ departmentHead.user.email }}</p>
                        <p class="text-sm text-gray-500">{{ departmentHead.user.designation }}</p>
                      </div>
                      <button
                        @click="deactivateHead(departmentHead)"
                        class="text-red-600 hover:text-red-900"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                      <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      Since {{ formatDate(departmentHead.start_date) }}
                    </div>
                    <div v-if="departmentHead.notes" class="mt-2 text-sm text-gray-500">
                      <p class="font-medium">Notes:</p>
                      <p>{{ departmentHead.notes }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="bg-yellow-50 rounded-lg p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">No Active Department Head</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                      <p>This department currently has no active head. Please assign a department head to ensure proper management.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Supervisors Card -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
              <h3 class="text-lg font-medium text-gray-900">Supervisors</h3>
                  <p class="mt-1 text-sm text-gray-500">Manage supervisors and their supervisees</p>
                </div>
              <button
                @click="showAssignSupervisorModal = true"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                Assign Supervisor
              </button>
            </div>

              <div class="space-y-4">
              <div
                v-for="supervisor in supervisors"
                :key="supervisor.id"
                  class="bg-gray-50 rounded-lg p-4"
                >
                  <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                      <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                        <span class="text-xl font-medium text-indigo-600">
                          {{ supervisor.supervisor.firstname[0] }}{{ supervisor.supervisor.lastname[0] }}
                        </span>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <div>
                          <p class="text-sm font-medium text-gray-900">
                            {{ supervisor.supervisor.firstname }} {{ supervisor.supervisor.lastname }}
                    </p>
                          <p class="text-sm text-gray-500">{{ supervisor.supervisor.email }}</p>
                          <p class="text-sm text-gray-500">{{ supervisor.supervisor.designation }}</p>
                  </div>
                  <div class="flex space-x-2">
                          <button
                            @click="viewSupervisedUsers(supervisor)"
                            class="text-indigo-600 hover:text-indigo-900"
                            title="View Details"
                          >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                          </button>
                    <button
                      @click="updateSupervisor(supervisor)"
                      class="text-indigo-600 hover:text-indigo-900"
                            title="Update"
                    >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                    </button>
                    <button
                      @click="deactivateSupervisor(supervisor)"
                      class="text-red-600 hover:text-red-900"
                            title="Deactivate"
                    >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                    </button>
                        </div>
                      </div>
                      <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center">
                          <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                          Since {{ formatDate(supervisor.start_date) }}
                        </div>
                        <div class="flex items-center">
                          <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          {{ supervisor.is_primary ? 'Primary' : 'Secondary' }} Supervisor
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Assign Head Modal -->
    <Modal :show="showAssignHeadModal" @close="showAssignHeadModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Assign Department Head</h2>
        <form @submit.prevent="assignHead">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Select Department Head</label>
              <div class="mt-1">
                <Multiselect
              v-model="newHead.user_id"
                  :options="availableUsers.map(user => ({
                    value: user.id,
                    label: `${user.firstname} ${user.lastname}`,
                    description: user.designation
                  }))"
                  :searchable="true"
                  :multiple="false"
                  track-by="value"
                  label="label"
                  placeholder="Search and select a department head"
                  class="multiselect-blue"
                >
                  <template v-slot:option="{ option }">
                    <div class="flex items-center">
                      <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ option.label }}</div>
                        <div class="text-xs text-gray-500">{{ option.description }}</div>
                      </div>
                    </div>
                  </template>
                </Multiselect>
              </div>
              <div v-if="newHead.errors.user_id" class="mt-1 text-sm text-red-600">
                {{ newHead.errors.user_id }}
              </div>
          </div>

            <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
              <div class="mt-1">
            <input
              type="date"
              v-model="newHead.start_date"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div v-if="newHead.errors.start_date" class="mt-1 text-sm text-red-600">
                {{ newHead.errors.start_date }}
              </div>
            </div>

            <div>
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="newHead.is_acting"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <span class="ml-2 text-sm text-gray-600">Acting Department Head</span>
              </label>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Notes</label>
              <div class="mt-1">
                <textarea
                  v-model="newHead.notes"
                  rows="3"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Add any additional notes about this assignment"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="showAssignHeadModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="!newHead.user_id || !newHead.start_date || newHead.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="newHead.processing">Assigning...</span>
              <span v-else>Assign Head</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- Assign Supervisor Modal -->
    <Modal :show="showAssignSupervisorModal" @close="showAssignSupervisorModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Assign Supervisor</h2>
        <form @submit.prevent="assignSupervisor">
          <div class="space-y-4">
            <div>
            <label class="block text-sm font-medium text-gray-700">Select Supervisor</label>
              <div class="mt-1">
                <Multiselect
              v-model="newSupervisor.supervisor_id"
                  :options="availableSupervisors.map(user => ({
                    value: user.id,
                    label: `${user.firstname} ${user.lastname}`,
                    description: user.designation
                  }))"
                  :searchable="true"
                  :multiple="false"
                  track-by="value"
                  label="label"
                  placeholder="Search and select a supervisor"
                  class="multiselect-blue"
                >
                  <template v-slot:option="{ option }">
                    <div class="flex items-center">
                      <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ option.label }}</div>
                        <div class="text-xs text-gray-500">{{ option.description }}</div>
                      </div>
                    </div>
                  </template>
                </Multiselect>
              </div>
              <div v-if="newSupervisor.errors.supervisor_id" class="mt-1 text-sm text-red-600">
                {{ newSupervisor.errors.supervisor_id }}
              </div>
          </div>

            <div>
            <label class="block text-sm font-medium text-gray-700">Select Users to Supervise</label>
              <div class="mt-1">
                <Multiselect
              v-model="newSupervisor.user_ids"
                  :options="availableUsers.map(user => ({
                    value: user.id,
                    label: `${user.firstname} ${user.lastname}`,
                    description: user.designation
                  }))"
                  mode="multiple"
                  :searchable="true"
                  track-by="value"
                  label="label"
                  placeholder="Select users to supervise"
                  class="multiselect-blue"
                >
                  <template v-slot:option="{ option }">
                    <div class="flex items-center">
                      <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ option.label }}</div>
                        <div class="text-xs text-gray-500">{{ option.description }}</div>
                      </div>
                    </div>
                  </template>
                </Multiselect>
              </div>
              <div v-if="newSupervisor.errors.user_ids" class="mt-1 text-sm text-red-600">
                {{ newSupervisor.errors.user_ids }}
              </div>
          </div>

            <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
              <div class="mt-1">
            <input
              type="date"
              v-model="newSupervisor.start_date"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            />
          </div>
              <div v-if="newSupervisor.errors.start_date" class="mt-1 text-sm text-red-600">
                {{ newSupervisor.errors.start_date }}
              </div>
            </div>

            <div>
            <label class="flex items-center">
              <input
                type="checkbox"
                v-model="newSupervisor.is_primary"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
              <span class="ml-2 text-sm text-gray-600">Primary Supervisor</span>
            </label>
          </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="showAssignSupervisorModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="!newSupervisor.supervisor_id || !newSupervisor.user_ids || newSupervisor.user_ids.length === 0 || !newSupervisor.start_date || newSupervisor.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="newSupervisor.processing">Assigning...</span>
              <span v-else>Assign Supervisor</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>

    <!-- View Users Modal -->
    <Modal :show="showViewUsersModal" @close="showViewUsersModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Supervisor Details</h2>
        <div v-if="selectedSupervisor">
          <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                  <span class="text-xl font-medium text-indigo-600">
                    {{ selectedSupervisor.supervisor.firstname[0] }}{{ selectedSupervisor.supervisor.lastname[0] }}
                  </span>
                </div>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-900">
                  {{ selectedSupervisor.supervisor.firstname }} {{ selectedSupervisor.supervisor.lastname }}
                </p>
                <p class="text-sm text-gray-500">{{ selectedSupervisor.supervisor.email }}</p>
                <p class="text-sm text-gray-500">{{ selectedSupervisor.supervisor.designation }}</p>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Since {{ formatDate(selectedSupervisor.start_date) }}
                  </div>
                  <div class="flex items-center">
                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ selectedSupervisor.is_primary ? 'Primary' : 'Secondary' }} Supervisor
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div>
            <h3 class="text-md font-medium text-gray-900 mb-3">Supervised Users</h3>
            <div v-if="loadingUsers" class="text-center py-4">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
              <p class="mt-2 text-sm text-gray-600">Loading users...</p>
            </div>
            <div v-else-if="selectedSupervisor.users && selectedSupervisor.users.length > 0" class="space-y-4">
              <div v-for="user in selectedSupervisor.users" :key="user.id" class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                      <span class="text-lg font-medium text-indigo-600">
                        {{ user.firstname[0] }}{{ user.lastname[0] }}
                      </span>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ user.firstname }} {{ user.lastname }}</p>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                    <p class="text-sm text-gray-500">{{ user.designation }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-4">
              <p class="text-sm text-gray-600">No users are currently under this supervisor.</p>
            </div>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button
            type="button"
            @click="showViewUsersModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Close
          </button>
        </div>
      </div>
    </Modal>

    <!-- Update Head Modal -->
    <Modal :show="showUpdateHeadModal" @close="showUpdateHeadModal = false">
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Update Department Head</h2>
        <form @submit.prevent="handleUpdateHead">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Select Department Head</label>
              <div class="mt-1">
                <Multiselect
                  v-model="updateHeadForm.user_id"
                  :options="availableUsers.map(user => ({
                    value: user.id,
                    label: `${user.firstname} ${user.lastname}`,
                    description: user.designation
                  }))"
                  :searchable="true"
                  :multiple="false"
                  track-by="value"
                  label="label"
                  placeholder="Search and select a department head"
                  class="multiselect-blue"
                >
                  <template v-slot:option="{ option }">
                    <div class="flex items-center">
                      <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ option.label }}</div>
                        <div class="text-xs text-gray-500">{{ option.description }}</div>
                      </div>
                    </div>
                  </template>
                </Multiselect>
              </div>
              <div v-if="updateHeadForm.errors.user_id" class="mt-1 text-sm text-red-600">
                {{ updateHeadForm.errors.user_id }}
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Start Date</label>
              <div class="mt-1">
                <input
                  type="date"
                  v-model="updateHeadForm.start_date"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div v-if="updateHeadForm.errors.start_date" class="mt-1 text-sm text-red-600">
                {{ updateHeadForm.errors.start_date }}
              </div>
            </div>

            <div>
              <label class="flex items-center">
                <input
                  type="checkbox"
                  v-model="updateHeadForm.is_acting"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <span class="ml-2 text-sm text-gray-600">Acting Department Head</span>
              </label>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Notes</label>
              <div class="mt-1">
                <textarea
                  v-model="updateHeadForm.notes"
                  rows="3"
                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Add any additional notes about this assignment"
                ></textarea>
              </div>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="showUpdateHeadModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="!updateHeadForm.user_id || !updateHeadForm.start_date || updateHeadForm.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="updateHeadForm.processing">Updating...</span>
              <span v-else>Update Head</span>
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Modal from '@/components/Modal.vue'
import { useForm, router } from '@inertiajs/vue3'
import { format } from 'date-fns'
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import axios from 'axios'

const props = defineProps({
  departments: {
    type: Array,
    default: null
  },
  departmentHead: {
    type: Object,
    default: () => []
  },
  supervisors: {
    type: Array,
    default: () => []
  },
  availableUsers: {
    type: Array,
    default: () => []
  },
  availableSupervisors: {
    type: Array,
    default: () => []
  },
  currentDepartment: {
    type: Object,
    default: null
  }
})

const selectedDepartment = ref(props.currentDepartment?.id || '')
const showAssignHeadModal = ref(false)
const showUpdateHeadModal = ref(false)
const showAssignSupervisorModal = ref(false)
const showViewUsersModal = ref(false)
const selectedSupervisor = ref(null)
const loadingUsers = ref(false)

const newHead = useForm({
  user_id: null,
  start_date: format(new Date(), 'yyyy-MM-dd'),
  is_acting: false,
  notes: null
})

const newSupervisor = useForm({
  supervisor_id: null,
  user_ids: [],
  start_date: format(new Date(), 'yyyy-MM-dd'),
  is_primary: false
})

const updateHeadForm = useForm({
  user_id: null,
  start_date: format(new Date(), 'yyyy-MM-dd'),
  is_acting: false,
  notes: null
})

const formatDate = (date) => {
  return format(new Date(date), 'MMM d, yyyy')
}

const loadDepartmentData = () => {
  if (selectedDepartment.value) {
    router.get(route('admin.departments.relationships.show', selectedDepartment.value), {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: (page) => {
        console.log('Department data loaded:', page.props)
        newHead.reset()
        newSupervisor.reset()
      },
      onError: (errors) => {
        console.error('Error loading department data:', errors)
      }
    })
  }
}

watch(selectedDepartment, (newValue) => {
  if (newValue) {
    loadDepartmentData()
  }
})

const assignHead = () => {
  if (!newHead.user_id || !newHead.start_date) {
    return
  }

  const formData = {
    user_id: newHead.user_id,
    start_date: newHead.start_date,
    is_acting: newHead.is_acting,
    notes: newHead.notes
  }

  router.post(route('admin.departments.head.assign', selectedDepartment.value), formData, {
    onStart: () => {
      newHead.processing = true
    },
    onSuccess: () => {
      showAssignHeadModal.value = false
      newHead.reset()
      window.location.reload()
    },
    onError: (errors) => {
      console.error('Form submission failed:', errors)
      alert('Failed to assign department head: ' + (errors.user_id || errors.start_date || 'Please try again.'))
    },
    onFinish: () => {
      newHead.processing = false
    }
  })
}

const deactivateHead = (head) => {
  if (confirm('Are you sure you want to deactivate this department head?')) {
    router.delete(route('admin.departments.head.deactivate', selectedDepartment.value), {
      onSuccess: () => {
        window.location.reload()
      },
      onError: (errors) => {
        console.error('Error deactivating head:', errors)
        alert('Failed to deactivate department head. Please try again.')
      }
      })
  }
}

const assignSupervisor = () => {
  if (!newSupervisor.supervisor_id || !newSupervisor.user_ids || newSupervisor.user_ids.length === 0 || !newSupervisor.start_date) {
    return
  }

  const userIds = newSupervisor.user_ids.map(option => {
    return typeof option === 'object' ? option.value : option
  }).filter(id => id !== null)

  const formData = {
    supervisor_id: newSupervisor.supervisor_id,
    user_ids: userIds,
    start_date: newSupervisor.start_date,
    is_primary: newSupervisor.is_primary
  }

  router.post(route('admin.departments.supervisors.assign', selectedDepartment.value), formData, {
    onStart: () => {
      newSupervisor.processing = true
    },
    onSuccess: () => {
      showAssignSupervisorModal.value = false
      newSupervisor.reset()
      window.location.reload()
    },
    onError: (errors) => {
      alert('Failed to assign supervisor: ' + (errors.supervisor_id || errors.user_ids || errors.start_date || 'Please try again.'))
    },
    onFinish: () => {
      newSupervisor.processing = false
    }
  })
}

const updateSupervisor = (supervisor) => {
  // Implement update logic
}

const deactivateSupervisor = (supervisor) => {
  if (confirm('Are you sure you want to deactivate this supervisor?')) {
    router.delete(route('admin.departments.supervisors.deactivate', supervisor.id), {
      onSuccess: () => {
        window.location.reload()
      },
      onError: (errors) => {
        console.error('Error deactivating supervisor:', errors)
        alert('Failed to deactivate supervisor. Please try again.')
      }
    })
  }
}

const viewSupervisedUsers = async (supervisor) => {
  selectedSupervisor.value = supervisor
  showViewUsersModal.value = true
  loadingUsers.value = true

  try {
    const response = await axios.get(`/api/supervisors/${supervisor.id}/users`)
    selectedSupervisor.value = {
      ...supervisor,
      users: response.data
    }
  } catch (error) {
    console.error('Error fetching supervised users:', error)
  } finally {
    loadingUsers.value = false
  }
}

const exportData = () => {
  // Implement export functionality
}

const handleUpdateHead = () => {
  if (!updateHeadForm.user_id || !updateHeadForm.start_date) {
    return
  }

  const formData = {
    user_id: updateHeadForm.user_id,
    start_date: updateHeadForm.start_date,
    is_acting: updateHeadForm.is_acting,
    notes: updateHeadForm.notes
  }

  router.put(route('admin.departments.head.update', selectedDepartment.value), formData, {
    onStart: () => {
      updateHeadForm.processing = true
    },
    onSuccess: () => {
      showUpdateHeadModal.value = false
      updateHeadForm.reset()
      window.location.reload()
    },
    onError: (errors) => {
      console.error('Form submission failed:', errors)
      alert('Failed to update department head: ' + (errors.user_id || errors.start_date || 'Please try again.'))
    },
    onFinish: () => {
      updateHeadForm.processing = false
    }
  })
}

const handleUpdateHeadClick = () => {
  if (props.departmentHeads && props.departmentHeads.length > 0) {
    updateHeadForm.user_id = props.departmentHeads[0].user.id
    updateHeadForm.start_date = format(new Date(props.departmentHeads[0].start_date), 'yyyy-MM-dd')
    updateHeadForm.is_acting = props.departmentHeads[0].is_acting
    updateHeadForm.notes = props.departmentHeads[0].notes
    showUpdateHeadModal.value = true
  }
}

onMounted(() => {
  if (props.currentDepartment?.id) {
    selectedDepartment.value = props.currentDepartment.id
    loadDepartmentData()
  }
})
</script> 

<style>
.multiselect-blue {
  --ms-option-bg-selected: #4f46e5;
  --ms-option-color-selected: #ffffff;
  --ms-tag-bg: #4f46e5;
  --ms-tag-color: #ffffff;
  --ms-ring-color: #4f46e5;
  --ms-option-bg-selected-pointed: #4f46e5;
  --ms-option-color-selected-pointed: #ffffff;
  --ms-dropdown-bg: #ffffff;
  --ms-dropdown-color: #1f2937;
  --ms-option-bg-pointed: #f3f4f6;
  --ms-option-color-pointed: #1f2937;
  --ms-option-bg-selected: #4f46e5;
  --ms-option-color-selected: #ffffff;
  --ms-option-bg-selected-pointed: #4f46e5;
  --ms-option-color-selected-pointed: #ffffff;
  --ms-tag-bg: #4f46e5;
  --ms-tag-color: #ffffff;
  --ms-tag-bg-disabled: #9ca3af;
  --ms-tag-color-disabled: #ffffff;
  --ms-tag-bg-selected: #4f46e5;
  --ms-tag-color-selected: #ffffff;
  --ms-tag-bg-selected-pointed: #4f46e5;
  --ms-tag-color-selected-pointed: #ffffff;
  --ms-tag-bg-selected-disabled: #9ca3af;
  --ms-tag-color-selected-disabled: #ffffff;
  --ms-tag-bg-selected-pointed-disabled: #9ca3af;
  --ms-tag-color-selected-pointed-disabled: #ffffff;
  --ms-tag-bg-selected-pointed-selected: #4f46e5;
  --ms-tag-color-selected-pointed-selected: #ffffff;
  --ms-tag-bg-selected-pointed-selected-disabled: #9ca3af;
  --ms-tag-color-selected-pointed-selected-disabled: #ffffff;
}

.multiselect-tag {
  background: #4f46e5;
  color: white;
  border-radius: 0.375rem;
  padding: 0.25rem 0.5rem;
  margin: 0.125rem;
  display: inline-flex;
  align-items: center;
}

.multiselect-tag-remove {
  margin-left: 0.5rem;
  cursor: pointer;
  opacity: 0.7;
}

.multiselect-tag-remove:hover {
  opacity: 1;
}

.multiselect-tag-remove-icon {
  display: inline-block;
  width: 0.5rem;
  height: 0.5rem;
  border: 1px solid currentColor;
  border-radius: 50%;
  position: relative;
}

.multiselect-tag-remove-icon::before,
.multiselect-tag-remove-icon::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0.75rem;
  height: 1px;
  background-color: currentColor;
}

.multiselect-tag-remove-icon::before {
  transform: translate(-50%, -50%) rotate(45deg);
}

.multiselect-tag-remove-icon::after {
  transform: translate(-50%, -50%) rotate(-45deg);
}

.multiselect-dropdown {
  background-color: white;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  z-index: 50;
}

.multiselect-option {
  padding: 0.5rem 0.75rem;
  cursor: pointer;
}

.multiselect-option.is-pointed {
  background-color: #f3f4f6;
}

.multiselect-option.is-selected {
  background-color: #4f46e5;
  color: white;
}

.multiselect-option.is-selected.is-pointed {
  background-color: #4f46e5;
  color: white;
}

.multiselect-input {
  min-height: 42px;
  padding: 0.5rem 0.75rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  background-color: white;
  color: #1f2937;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.multiselect-input:focus {
  outline: none;
  border-color: #4f46e5;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
}

.multiselect-single-label {
  color: #1f2937;
  font-size: 0.875rem;
  line-height: 1.25rem;
  padding: 0.5rem 0.75rem;
}

.multiselect-single-label.is-selected {
  color: white;
}

.multiselect-placeholder {
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.25rem;
}

.multiselect-search {
  color: #1f2937;
  font-size: 0.875rem;
  line-height: 1.25rem;
  padding: 0.5rem 0.75rem;
  width: 100%;
  border: none;
  background: transparent;
}

.multiselect-search:focus {
  outline: none;
}

.multiselect-clear {
  color: #6b7280;
  padding: 0.25rem;
  margin-right: 0.5rem;
}

.multiselect-clear:hover {
  color: #1f2937;
}

.multiselect-caret {
  color: #6b7280;
  padding: 0.25rem;
  margin-right: 0.5rem;
}

.multiselect-option.is-selected .text-base {
  color: white;
}

.multiselect-option.is-selected .text-sm {
  color: rgba(255, 255, 255, 0.8);
}
</style> 