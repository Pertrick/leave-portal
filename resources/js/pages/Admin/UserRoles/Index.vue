<template>
  <AppLayout title="User Roles Management">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">User Roles Management</h2>
        <div class="flex items-center gap-2">
          <Input
            v-model="search"
            placeholder="Search users..."
            class="w-64"
          />
          <Button @click="showBulkAssignModal = true" class="flex items-center gap-2">
            <UserPlusIcon class="w-4 h-4" />
            Bulk Assign
          </Button>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <UsersIcon class="w-6 h-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Total Users</p>
                  <p class="text-2xl font-bold text-gray-900">{{ users.total }}</p>
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
                  <p class="text-sm font-medium text-gray-600">Users with Roles</p>
                  <p class="text-2xl font-bold text-gray-900">{{ usersWithRoles }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                  <KeyIcon class="w-6 h-6 text-purple-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Users with Permissions</p>
                  <p class="text-2xl font-bold text-gray-900">{{ usersWithPermissions }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                  <ExclamationTriangleIcon class="w-6 h-6 text-orange-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Users without Roles</p>
                  <p class="text-2xl font-bold text-gray-900">{{ usersWithoutRoles }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Users Table -->
        <Card>
          <CardHeader>
            <CardTitle>Users</CardTitle>
            <CardDescription>Manage user roles and permissions</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>User</TableHead>
                    <TableHead>Department</TableHead>
                    <TableHead>Roles</TableHead>
                    <TableHead>Direct Permissions</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="user in filteredUsers" :key="user.id">
                    <TableCell>
                      <div class="flex items-center">
                        <Avatar class="w-8 h-8 mr-3">
                          <AvatarImage :src="user.avatar" :alt="user.full_name" />
                          <AvatarFallback>{{ user.firstname.charAt(0) }}{{ user.lastname.charAt(0) }}</AvatarFallback>
                        </Avatar>
                        <div>
                          <p class="font-medium">{{ user.full_name }}</p>
                          <p class="text-sm text-gray-500">{{ user.email }}</p>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell>
                      <span class="text-gray-600">{{ user.department?.name || 'No department' }}</span>
                    </TableCell>
                    <TableCell>
                      <div class="flex flex-wrap gap-1">
                        <Badge 
                          v-for="role in user.roles" 
                          :key="role.id"
                          variant="secondary"
                          class="text-xs"
                        >
                          {{ role.name }}
                        </Badge>
                        <span v-if="user.roles.length === 0" class="text-gray-400 text-sm">
                          No roles
                        </span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div class="flex flex-wrap gap-1">
                        <Badge 
                          v-for="permission in user.permissions" 
                          :key="permission.id"
                          variant="outline"
                          class="text-xs"
                        >
                          {{ permission.name }}
                        </Badge>
                        <span v-if="user.permissions.length === 0" class="text-gray-400 text-sm">
                          No direct permissions
                        </span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <div class="flex items-center gap-2">
                        <Button
                          variant="outline"
                          size="sm"
                          @click="viewUser(user)"
                        >
                          <EyeIcon class="w-4 h-4" />
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          @click="editUserRoles(user)"
                        >
                          <UserIcon class="w-4 h-4" />
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          @click="editUserPermissions(user)"
                        >
                          <KeyIcon class="w-4 h-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
              <p class="text-sm text-gray-700">
                Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
              </p>
              <div class="flex items-center gap-2">
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="!users.prev_page_url"
                  @click="navigateToPage(users.current_page - 1)"
                >
                  Previous
                </Button>
                <span class="text-sm text-gray-600">
                  Page {{ users.current_page }} of {{ users.last_page }}
                </span>
                <Button
                  variant="outline"
                  size="sm"
                  :disabled="!users.next_page_url"
                  @click="navigateToPage(users.current_page + 1)"
                >
                  Next
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <!-- View User Modal -->
    <Dialog :open="showViewModal" @update:open="showViewModal = false">
      <DialogContent class="sm:max-w-[700px]">
        <DialogHeader>
          <DialogTitle>User Details</DialogTitle>
        </DialogHeader>

        <div v-if="selectedUser" class="space-y-6">
          <!-- User Info -->
          <div class="flex items-center space-x-4">
            <Avatar class="w-16 h-16">
              <AvatarImage :src="selectedUser.avatar" :alt="selectedUser.full_name" />
              <AvatarFallback class="text-lg">{{ selectedUser.firstname.charAt(0) }}{{ selectedUser.lastname.charAt(0) }}</AvatarFallback>
            </Avatar>
            <div>
              <h3 class="text-xl font-semibold">{{ selectedUser.full_name }}</h3>
              <p class="text-gray-600">{{ selectedUser.email }}</p>
              <p class="text-sm text-gray-500">{{ selectedUser.department?.name || 'No department' }}</p>
            </div>
          </div>

          <!-- Roles and Permissions -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="font-medium text-gray-900 mb-3">Assigned Roles</h4>
              <div class="space-y-2">
                <Badge
                  v-for="role in selectedUser.roles"
                  :key="role.id"
                  variant="secondary"
                  class="mr-2 mb-2"
                >
                  {{ role.name }}
                </Badge>
                <span v-if="selectedUser.roles.length === 0" class="text-gray-400 text-sm">
                  No roles assigned
                </span>
              </div>
            </div>

            <div>
              <h4 class="font-medium text-gray-900 mb-3">Direct Permissions</h4>
              <div class="space-y-2">
                <Badge
                  v-for="permission in selectedUser.permissions"
                  :key="permission.id"
                  variant="outline"
                  class="mr-2 mb-2"
                >
                  {{ permission.name }}
                </Badge>
                <span v-if="selectedUser.permissions.length === 0" class="text-gray-400 text-sm">
                  No direct permissions
                </span>
              </div>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showViewModal = false">
            Close
          </Button>
          <Button @click="editUserRoles(selectedUser)">
            Manage Roles
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Edit User Roles Modal -->
    <Dialog :open="showRolesModal" @update:open="showRolesModal = false">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Manage User Roles</DialogTitle>
          <DialogDescription>
            Assign or remove roles for {{ selectedUser?.full_name }}
          </DialogDescription>
        </DialogHeader>

        <div v-if="selectedUser" class="space-y-4">
          <div>
            <Label>Select Roles</Label>
            <div class="mt-2 space-y-2 max-h-60 overflow-y-auto border rounded-md p-3">
              <div
                v-for="role in roles"
                :key="role.id"
                class="flex items-center space-x-2"
              >
                <Checkbox
                  :id="`role-${role.id}`"
                  :checked="selectedUserRoles.includes(role.id)"
                  @update:checked="toggleRole(role.id)"
                />
                <Label :for="`role-${role.id}`" class="text-sm">
                  {{ role.name }}
                </Label>
              </div>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="showRolesModal = false">
            Cancel
          </Button>
          <Button @click="updateUserRoles" :disabled="rolesForm.processing">
            Update Roles
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Edit User Permissions Modal -->
    <Dialog :open="showPermissionsModal" @update:open="showPermissionsModal = false">
      <DialogContent class="sm:max-w-[600px]">
        <DialogHeader>
          <DialogTitle>Manage User Permissions</DialogTitle>
          <DialogDescription>
            Assign or remove direct permissions for {{ selectedUser?.full_name }}
          </DialogDescription>
        </DialogHeader>

        <div v-if="selectedUser" class="space-y-4">
          <div>
            <Label>Select Permissions</Label>
            <div class="mt-2 space-y-2 max-h-60 overflow-y-auto border rounded-md p-3">
              <div
                v-for="permission in permissions"
                :key="permission.id"
                class="flex items-center space-x-2"
              >
                <Checkbox
                  :id="`permission-${permission.id}`"
                  :checked="selectedUserPermissions.includes(permission.id)"
                  @update:checked="togglePermission(permission.id)"
                />
                <Label :for="`permission-${permission.id}`" class="text-sm">
                  {{ permission.name }}
                </Label>
              </div>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button type="button" variant="outline" @click="showPermissionsModal = false">
            Cancel
          </Button>
          <Button @click="updateUserPermissions" :disabled="permissionsForm.processing">
            Update Permissions
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
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'
import {
  PlusIcon,
  EyeIcon,
  UserIcon,
  KeyIcon,
  UsersIcon,
  ShieldCheckIcon,
  ExclamationTriangleIcon,
  UserPlusIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  users: Object,
  roles: Array,
  permissions: Array
})

// Reactive state
const search = ref('')
const showViewModal = ref(false)
const showRolesModal = ref(false)
const showPermissionsModal = ref(false)
const selectedUser = ref(null)
const selectedUserRoles = ref([])
const selectedUserPermissions = ref([])

// Forms
const rolesForm = useForm({
  roles: []
})

const permissionsForm = useForm({
  permissions: []
})

// Computed
const filteredUsers = computed(() => {
  if (!search.value) return props.users.data
  
  return props.users.data.filter(user => 
    user.full_name.toLowerCase().includes(search.value.toLowerCase()) ||
    user.email.toLowerCase().includes(search.value.toLowerCase()) ||
    user.staff_id?.toLowerCase().includes(search.value.toLowerCase())
  )
})

const usersWithRoles = computed(() => {
  return props.users.data.filter(user => user.roles.length > 0).length
})

const usersWithPermissions = computed(() => {
  return props.users.data.filter(user => user.permissions.length > 0).length
})

const usersWithoutRoles = computed(() => {
  return props.users.data.filter(user => user.roles.length === 0).length
})

// Methods
const viewUser = (user) => {
  selectedUser.value = user
  showViewModal.value = true
}

const editUserRoles = (user) => {
  selectedUser.value = user
  selectedUserRoles.value = user.roles.map(r => r.id)
  showRolesModal.value = true
}

const editUserPermissions = (user) => {
  selectedUser.value = user
  selectedUserPermissions.value = user.permissions.map(p => p.id)
  showPermissionsModal.value = true
}

const toggleRole = (roleId) => {
  const index = selectedUserRoles.value.indexOf(roleId)
  if (index > -1) {
    selectedUserRoles.value.splice(index, 1)
  } else {
    selectedUserRoles.value.push(roleId)
  }
}

const togglePermission = (permissionId) => {
  const index = selectedUserPermissions.value.indexOf(permissionId)
  if (index > -1) {
    selectedUserPermissions.value.splice(index, 1)
  } else {
    selectedUserPermissions.value.push(permissionId)
  }
}

const updateUserRoles = () => {
  rolesForm.roles = selectedUserRoles.value
  rolesForm.put(route('admin.user-roles.update-roles', selectedUser.value.id), {
    onSuccess: () => {
      showRolesModal.value = false
      selectedUser.value = null
      selectedUserRoles.value = []
    }
  })
}

const updateUserPermissions = () => {
  permissionsForm.permissions = selectedUserPermissions.value
  permissionsForm.put(route('admin.user-roles.update-permissions', selectedUser.value.id), {
    onSuccess: () => {
      showPermissionsModal.value = false
      selectedUser.value = null
      selectedUserPermissions.value = []
    }
  })
}

const navigateToPage = (page) => {
  window.location.href = `${route('admin.user-roles.index')}?page=${page}`
}
</script> 