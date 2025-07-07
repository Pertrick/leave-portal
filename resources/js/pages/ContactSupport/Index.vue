<template>
  <AppLayout title="Contact Support">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Contact Support</h2>
        <Button @click="$inertia.visit(route('contact-support.create'))" class="flex items-center gap-2">
          <PlusIcon class="w-4 h-4" />
          New Request
        </Button>
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
                  <ChatBubbleLeftRightIcon class="w-6 h-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Total Requests</p>
                  <p class="text-2xl font-bold text-gray-900">{{ requests.total }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                  <ClockIcon class="w-6 h-6 text-yellow-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Open</p>
                  <p class="text-2xl font-bold text-gray-900">{{ openCount }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                  <ArrowPathIcon class="w-6 h-6 text-blue-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">In Progress</p>
                  <p class="text-2xl font-bold text-gray-900">{{ inProgressCount }}</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent class="p-6">
              <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                  <CheckCircleIcon class="w-6 h-6 text-green-600" />
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-600">Resolved</p>
                  <p class="text-2xl font-bold text-gray-900">{{ resolvedCount }}</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Requests Table -->
        <Card>
          <CardHeader>
            <CardTitle>Support Requests</CardTitle>
            <CardDescription>View and manage your support requests</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Subject</TableHead>
                    <TableHead>Category</TableHead>
                    <TableHead>Priority</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Created</TableHead>
                    <TableHead>Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow v-for="request in requests.data" :key="request.id">
                    <TableCell>
                      <div class="flex items-center">
                        <ChatBubbleLeftRightIcon class="w-5 h-5 text-blue-500 mr-2" />
                        <span class="font-medium">{{ request.subject }}</span>
                      </div>
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline">
                        {{ request.category_label }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <Badge :class="request.priority_color">
                        {{ request.priority_label }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <Badge :class="request.status_color">
                        {{ request.status_label }}
                      </Badge>
                    </TableCell>
                    <TableCell>
                      <span class="text-gray-600">{{ formatDate(request.created_at) }}</span>
                    </TableCell>
                    <TableCell>
                      <div class="flex items-center gap-2">
                        <Button
                          variant="outline"
                          size="sm"
                          @click="$inertia.visit(route('contact-support.show', request.id))"
                        >
                          <EyeIcon class="w-4 h-4" />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex items-center justify-between">
              <div class="text-sm text-gray-700">
                Showing {{ requests.from }} to {{ requests.to }} of {{ requests.total }} results
              </div>
              <div class="flex gap-2">
                <Button
                  v-if="requests.prev_page_url"
                  variant="outline"
                  size="sm"
                  @click="$inertia.visit(requests.prev_page_url)"
                >
                  Previous
                </Button>
                <Button
                  v-if="requests.next_page_url"
                  variant="outline"
                  size="sm"
                  @click="$inertia.visit(requests.next_page_url)"
                >
                  Next
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { Badge } from '@/components/ui/badge'
import {
  PlusIcon,
  EyeIcon,
  ChatBubbleLeftRightIcon,
  ClockIcon,
  ArrowPathIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  requests: Object,
  categories: Object,
  priorities: Object,
  statuses: Object
})

// Computed properties for stats
const openCount = computed(() => {
  return props.requests.data.filter(r => r.status === 'open').length
})

const inProgressCount = computed(() => {
  return props.requests.data.filter(r => r.status === 'in_progress').length
})

const resolvedCount = computed(() => {
  return props.requests.data.filter(r => r.status === 'resolved').length
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script> 