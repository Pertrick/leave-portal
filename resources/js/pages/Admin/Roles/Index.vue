<template>
  <AppLayout title="Roles Management">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Roles Management</h2>
        <Button @click="showCreateModal = true" class="flex items-center gap-2">
          <PlusIcon class="w-4 h-4" />
          Create Role
        </Button>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <ShieldCheckIcon class="w-6 h-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Total Roles</p>
                  <p class="text-2xl font-bold text-gray-900">{{ roles.length }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                  <KeyIcon class="w-6 h-6 text-green-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Total Permissions</p>
                  <p class="text-2xl font-bold text-gray-900">{{ permissions.length }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <UsersIcon class="w-6 h-6 text-purple-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Active Users</p>
                  <p class="text-2xl font-bold text-gray-900">{{ totalUsers }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Roles Table -->
        <Card>
          <CardHeader>
            <CardTitle>Roles</CardTitle>
            <CardDescription>Manage system roles and their permissions</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Role Name</TableHead>
                    <TableHead>Description</TableHead>
                    <TableHead>Permissions</TableHead>
                    <TableHead>Users</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="role in roles" :key="role.id">
                    <TableCell>
                      <div class="flex items-center">
                        <ShieldCheckIcon class="w-5 h-5 text-blue-500 mr-2" />
                        <span class="font-medium">{{ role.name }}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <span class="text-gray-600">{{ role.description || 'No description' }}</span>
                    </TableCell>
                    <TableCell>
                      <div class="flex flex-wrap gap-1">
                        <Badge 
                          v-for="permission in role.permissions" 
                          :key="permission.id"
                          variant="secondary"
                          class="text-xs"
                        >
                          {{ permission.name }}
                        </Badge>
                        <span v-if="role.permissions.length === 0" class="text-gray-400 text-sm">
                          No permissions
                        </span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline">
                        {{ role.users_count }} users
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <div class="flex items-center gap-2">
                        <Button
                          variant="outline"
                          size="sm"
                          @click="editRole(role)"
                        >
                          <PencilIcon class="w-4 h-4" />
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          @click="viewRole(role)"
                        >
                          <EyeIcon class="w-4 h-4" />
                        </Button>
                        <Button
                          variant="destructive"
                          size="sm"
                          @click="deleteRole(role)"
                          :disabled="role.users_count > 0"
                        >
                          <TrashIcon class="w-4 h-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <Dialog :open="showCreateModal || showEditModal" @update:open="closeModal">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>{{ showEditModal ? 'Edit Role' : 'Create New Role' }}</DialogTitle>
          <DialogDescription>
            {{ showEditModal ? 'Update role details and permissions' : 'Create a new role and assign permissions' }}
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="showEditModal ? updateRole() : createRole()" class="space-y-4">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <Label for="name">Role Name</Label>
              <Input
                id="name"
                v-model="form.name"
                placeholder="Enter role name"
                required
              />
            </div>

            <div>
              <Label for="description">Description</Label>
              <Textarea
                id="description"
                v-model="form.description"
                placeholder="Enter role description"
                rows="3"
              />
            </div>

            <div>
              <Label>Permissions</Label>
              <div class="mt-2 space-y-2 max-h-60 overflow-y-auto border rounded-md p-3">
                <div
                  v-for="permission in permissions"
                  :key="permission.id"
                  class="flex items-center space-x-2"
                >
                  <Checkbox
                    :id="`permission-${permission.id}`"
                    v-model="form.permissions"
                    :value="permission.id"
                  />
                  <Label :for="`permission-${permission.id}`" class="text-sm">
                    {{ permission.name }}
                  </Label>
                </div>
              </div>
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeModal">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing">
              {{ showEditModal ? 'Update Role' : 'Create Role' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- View Role Modal -->
    <Dialog :open="showViewModal" @update:open="showViewModal = false">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Role Details</DialogTitle>
        </DialogHeader>

        <div v-if="selectedRole" class="space-y-4">
          <div>
            <Label class="text-sm font-medium text-gray-700">Role Name</Label>
            <p class="mt-1 text-lg font-semibold">{{ selectedRole.name }}</p>
          </div>

          <div>
            <Label class="text-sm font-medium text-gray-700">Description</Label>
            <p class="mt-1 text-gray-600">{{ selectedRole.description || 'No description provided' }}</p>
          </div>

          <div>
            <Label class="text-sm font-medium text-gray-700">Permissions</Label>
            <div class="mt-2 flex flex-wrap gap-2">
              <Badge
                v-for="permission in selectedRole.permissions"
                :key="permission.id"
                variant="secondary"
              >
                {{ permission.name }}
              </Badge>
              <span v-if="selectedRole.permissions.length === 0" class="text-gray-400">
                No permissions assigned
              </span>
            </div>
          </div>

          <div>
            <Label class="text-sm font-medium text-gray-700">Users with this role</Label>
            <p class="mt-1 text-gray-600">{{ selectedRole.users_count }} users</p>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showViewModal = false">
            Close
          </Button>
          <Button @click="editRole(selectedRole)">
            Edit Role
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import {
  PlusIcon,
  PencilIcon,
  EyeIcon,
  TrashIcon,
  ShieldCheckIcon,
  KeyIcon,
  UsersIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  roles: Array,
  permissions: Array
})

// Reactive state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedRole = ref(null)

// Form handling
const form = useForm({
  name: '',
  description: '',
  permissions: []
})

// Computed
const totalUsers = computed(() => {
  return props.roles.reduce((total, role) => total + role.users_count, 0)
})

// Methods
const createRole = () => {
  form.post(route('admin.roles.store'), {
    onSuccess: () => {
      closeModal()
      form.reset()
    }
  })
}

const updateRole = () => {
  form.put(route('admin.roles.update', selectedRole.value.id), {
    onSuccess: () => {
      closeModal()
      form.reset()
    }
  })
}

const editRole = (role) => {
  selectedRole.value = role
  form.name = role.name
  form.description = role.description || ''
  
  // Ensure permissions are properly set as an array of IDs
  const permissionIds = role.permissions ? role.permissions.map(p => p.id) : []
  form.permissions = [...permissionIds]
  
  showEditModal.value = true
  
  // Debug logging
  console.log('Editing role:', role.name)
  console.log('Role permissions:', role.permissions)
  console.log('Form permissions after setting:', form.permissions)
}

const viewRole = (role) => {
  selectedRole.value = role
  showViewModal.value = true
}

const deleteRole = (role) => {
  if (confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
    useForm().delete(route('admin.roles.destroy', role.id))
  }
}

const togglePermission = (permissionId) => {
  const index = form.permissions.indexOf(permissionId)
  if (index > -1) {
    form.permissions.splice(index, 1)
  } else {
    form.permissions.push(permissionId)
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  showViewModal.value = false
  selectedRole.value = null
  form.reset()
}
</script> 