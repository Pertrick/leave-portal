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

        <!-- Admin Response Section -->
        <Card v-if="canRespond" class="mb-6">
          <CardHeader>
            <CardTitle>Respond to Request</CardTitle>
            <CardDescription>
              Provide a response and update the status
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submitResponse" class="space-y-4">
              <div>
                <Label for="status">Status</Label>
                <Select v-model="responseForm.status" required>
                  <option v-for="(label, value) in statuses" :key="value" :value="value">
                    {{ label }}
                  </option>
                </Select>
              </div>

              <div>
                <Label for="admin_response">Response</Label>
                <Textarea
                  id="admin_response"
                  v-model="responseForm.admin_response"
                  placeholder="Provide a detailed response to the user..."
                  rows="4"
                  required
                />
              </div>

              <div class="flex items-center gap-4">
                <Button type="submit" :disabled="responseForm.processing">
                  <LoaderCircle v-if="responseForm.processing" class="w-4 h-4 animate-spin mr-2" />
                  Send Response
                </Button>
              </div>
            </form>
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

        <!-- Quick Status Update -->
        <Card v-if="canRespond && !request.admin_response" class="mt-6">
          <CardHeader>
            <CardTitle>Quick Status Update</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="flex items-center gap-4">
              <Select v-model="quickStatus" @change="updateStatus">
                <option value="">Update Status</option>
                <option v-for="(label, value) in statuses" :key="value" :value="value">
                  {{ label }}
                </option>
              </Select>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { usePermission } from '@/composables/usePermission'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select } from '@/components/ui/select'
import { LoaderCircle } from 'lucide-vue-next'

const props = defineProps({
  request: Object,
  categories: Object,
  priorities: Object,
  statuses: Object
})

const { can } = usePermission()
const canRespond = can('manage_support_requests')

const quickStatus = ref('')

const responseForm = useForm({
  admin_response: '',
  status: 'in_progress'
})

const submitResponse = () => {
  responseForm.post(route('contact-support.respond', props.request.id), {
    onSuccess: () => {
      responseForm.reset()
    }
  })
}

const updateStatus = () => {
  if (quickStatus.value) {
    useForm().patch(route('contact-support.update-status', props.request.id), {
      status: quickStatus.value
    })
    quickStatus.value = ''
  }
}

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