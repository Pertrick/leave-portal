<template>
  <AppLayout title="Support Request Details">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Support Request Details</h2>
        <Button @click="$inertia.visit(route('contact-support.index'))" variant="outline">
          Back to Requests
        </Button>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Request Details -->
        <Card class="mb-6">
          <CardHeader>
            <div class="flex items-center justify-between">
              <div>
                <CardTitle>{{ request.subject }}</CardTitle>
                <CardDescription>
                  Request #{{ request.id }} • {{ formatDate(request.created_at) }}
                </CardDescription>
              </div>
              <div class="flex items-center gap-2">
                <Badge :class="request.category_color">
                  {{ request.category_label }}
                </Badge>
                <Badge :class="request.priority_color">
                  {{ request.priority_label }}
                </Badge>
                <Badge :class="request.status_color">
                  {{ request.status_label }}
                </Badge>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div>
                <Label class="text-sm font-medium text-gray-700">From</Label>
                <p class="mt-1">{{ request.user.full_name }} ({{ request.user.email }})</p>
              </div>
              
              <div>
                <Label class="text-sm font-medium text-gray-700">Message</Label>
                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                  <p class="whitespace-pre-wrap">{{ request.message }}</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>



        <!-- Response History -->
        <Card v-if="request.admin_response">
          <CardHeader>
            <CardTitle>Response</CardTitle>
            <CardDescription>
              Response from {{ request.responder?.full_name || 'Admin' }} on {{ formatDate(request.responded_at) }}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div class="p-4 bg-blue-50 rounded-lg">
              <p class="whitespace-pre-wrap">{{ request.admin_response }}</p>
            </div>
          </CardContent>
        </Card>


      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'

const props = defineProps({
  request: Object,
  categories: Object,
  priorities: Object,
  statuses: Object
})



const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script> 