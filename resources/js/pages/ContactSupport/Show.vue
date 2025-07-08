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
            <div class="space-y-4">
              <div>
                <CardTitle>{{ request.subject }}</CardTitle>
                <CardDescription>
                  Request #{{ request.id }} • {{ formatDate(request.created_at) }}
                </CardDescription>
              </div>
              <div class="space-y-3">
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Category</h4>
                  <Badge :class="request.category_color">
                    {{ request.category }}
                  </Badge>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Priority</h4>
                  <Badge :class="request.priority_color">
                    {{ request.priority }}
                  </Badge>
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Status</h4>
                  <Badge :class="request.status_color">
                    {{ request.status   }}
                  </Badge>
                </div>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div>
                <Label class="text-sm font-medium text-gray-700">From</Label>
                <p class="mt-1">{{ request.user?.firstname }} {{ request.user?.lastname }} ({{ request.user?.email }})</p>
              </div>
              
              <div>
                <Label class="text-sm font-medium text-gray-700">Priority</Label>
                <p class="mt-1">{{ request.priority }}</p>
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

            <!-- Attachments -->
            <Card v-if="request.attachments && request.attachments.length > 0">
              <CardHeader>
                <CardTitle>Attachments</CardTitle>
                <CardDescription>
                  Files attached to this support request
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div class="space-y-3">
                  <div v-for="attachment in request.attachments" :key="attachment.filename"
                       class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                    <div class="flex items-center gap-3">
                      <FileIcon class="w-5 h-5 text-blue-500" />
                      <div>
                        <span class="text-sm font-medium">{{ attachment.original_name }}</span>
                        <span class="text-xs text-gray-500 ml-2">({{ formatFileSize(attachment.file_size) }})</span>
                      </div>
                    </div>
                    <a :href="route('contact-support.download-attachment', [request.id, attachment.filename])" 
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                      Download
                    </a>
                  </div>
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
import { FileIcon } from 'lucide-vue-next'

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

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script> 