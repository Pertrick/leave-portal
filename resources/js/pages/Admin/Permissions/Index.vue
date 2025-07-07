<template>
  <AppLayout title="Permissions Management">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Permissions Management</h2>
        <Button @click="showCreateModal = true" class="flex items-center gap-2">
          <PlusIcon class="w-4 h-4" />
          Create Permission
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
                  <KeyIcon class="w-6 h-6 text-blue-600" />
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
                <div class="p-2 bg-green-100 rounded-lg">
                  <ShieldCheckIcon class="w-6 h-6 text-green-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Assigned to Roles</p>
                  <p class="text-2xl font-bold text-gray-900">{{ totalAssignedPermissions }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <ExclamationTriangleIcon class="w-6 h-6 text-purple-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Unassigned</p>
                  <p class="text-2xl font-bold text-gray-900">{{ unassignedPermissions }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Permissions Table -->
        <Card>
          <CardHeader>
            <CardTitle>Permissions</CardTitle>
            <CardDescription>Manage system permissions and their assignments</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Permission Name</TableHead>
                    <TableHead>Description</TableHead>
                    <TableHead>Assigned to Roles</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="permission in permissions" :key="permission.id">
                    <TableCell>
                      <div class="flex items-center">
                        <KeyIcon class="w-5 h-5 text-blue-500 mr-2" />
                        <span class="font-medium">{{ permission.name }}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <span class="text-gray-600">{{ permission.description || 'No description' }}</span>
                    </TableCell>
                    <TableCell>
                      <div class="flex flex-wrap gap-1">
                        <Badge 
                          v-for="role in permission.roles" 
                          :key="role.id"
                          variant="secondary"
                          class="text-xs"
                        >
                          {{ role.name }}
                        </Badge>
                        <span v-if="permission.roles.length === 0" class="text-gray-400 text-sm">
                          No roles
                        </span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div class="flex items-center gap-2">
                        <Button
                          variant="outline"
                          size="sm"
                          @click="editPermission(permission)"
                        >
                          <PencilIcon class="w-4 h-4" />
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          @click="viewPermission(permission)"
                        >
                          <EyeIcon class="w-4 h-4" />
                        </Button>
                        <Button
                          variant="destructive"
                          size="sm"
                          @click="deletePermission(permission)"
                          :disabled="permission.roles_count > 0"
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

    <!-- Create/Edit Permission Modal -->
    <Dialog :open="showCreateModal || showEditModal" @update:open="closeModal">
      <DialogContent class="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>{{ showEditModal ? 'Edit Permission' : 'Create New Permission' }}</DialogTitle>
          <DialogDescription>
            {{ showEditModal ? 'Update permission details' : 'Create a new system permission' }}
          </DialogDescription>
        </DialogHeader>

        <form @submit.prevent="showEditModal ? updatePermission() : createPermission()" class="space-y-4">
          <div class="grid grid-cols-1 gap-4">
            <div>
              <Label for="name">Permission Name</Label>
              <Input
                id="name"
                v-model="form.name"
                placeholder="e.g., manage_users, view_reports"
                required
              />
              <p class="text-xs text-gray-500 mt-1">
                Use snake_case format (e.g., manage_users, view_reports)
              </p>
            </div>

            <div>
              <Label for="description">Description</Label>
              <Textarea
                id="description"
                v-model="form.description"
                placeholder="Describe what this permission allows"
                rows="3"
              />
            </div>
          </div>

          <DialogFooter>
            <Button type="button" variant="outline" @click="closeModal">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing">
              {{ showEditModal ? 'Update Permission' : 'Create Permission' }}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>

    <!-- View Permission Modal -->
    <Dialog :open="showViewModal" @update:open="showViewModal = false">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Permission Details</DialogTitle>
        </DialogHeader>

        <div v-if="selectedPermission" class="space-y-4">
          <div>
            <Label class="text-sm font-medium text-gray-700">Permission Name</Label>
            <p class="mt-1 text-lg font-semibold">{{ selectedPermission.name }}</p>
          </div>

          <div>
            <Label class="text-sm font-medium text-gray-700">Description</Label>
            <p class="mt-1 text-gray-600">{{ selectedPermission.description || 'No description provided' }}</p>
          </div>

          <div>
            <Label class="text-sm font-medium text-gray-700">Assigned to Roles</Label>
            <div class="mt-2 flex flex-wrap gap-2">
              <Badge
                v-for="role in selectedPermission.roles"
                :key="role.id"
                variant="secondary"
              >
                {{ role.name }}
              </Badge>
              <span v-if="selectedPermission.roles.length === 0" class="text-gray-400">
                Not assigned to any roles
              </span>
            </div>
          </div>

          <div>
            <Label class="text-sm font-medium text-gray-700">Total Role Assignments</Label>
            <p class="mt-1 text-gray-600">{{ selectedPermission.roles_count }} roles</p>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showViewModal = false">
            Close
          </Button>
          <Button @click="editPermission(selectedPermission)">
            Edit Permission
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
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
import {
  PlusIcon,
  PencilIcon,
  EyeIcon,
  TrashIcon,
  KeyIcon,
  ShieldCheckIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  permissions: Array
})

// Reactive state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const selectedPermission = ref(null)

// Form handling
const form = useForm({
  name: '',
  description: ''
})

// Computed
const totalAssignedPermissions = computed(() => {
  return props.permissions.filter(p => p.roles_count > 0).length
})

const unassignedPermissions = computed(() => {
  return props.permissions.filter(p => p.roles_count === 0).length
})

// Methods
const createPermission = () => {
  form.post(route('admin.permissions.store'), {
    onSuccess: () => {
      closeModal()
      form.reset()
    }
  })
}

const updatePermission = () => {
  form.put(route('admin.permissions.update', selectedPermission.value.id), {
    onSuccess: () => {
      closeModal()
      form.reset()
    }
  })
}

const editPermission = (permission) => {
  selectedPermission.value = permission
  form.name = permission.name
  form.description = permission.description || ''
  showEditModal.value = true
}

const viewPermission = (permission) => {
  selectedPermission.value = permission
  showViewModal.value = true
}

const deletePermission = (permission) => {
  if (confirm(`Are you sure you want to delete the permission "${permission.name}"?`)) {
    useForm().delete(route('admin.permissions.destroy', permission.id))
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  showViewModal.value = false
  selectedPermission.value = null
  form.reset()
}
</script> 