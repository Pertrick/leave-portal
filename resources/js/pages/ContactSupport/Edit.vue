<template>
  <AppLayout title="Edit Support Request">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Edit Support Request</h2>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6 flex justify-end">
          <Button @click="$inertia.visit(route('contact-support.index'))" variant="outline" class="bg-black text-white hover:bg-gray-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Requests
          </Button>
        </div>

        <!-- Status Warning -->
        <div v-if="request.status !== 'open'" class="mb-6">
          <Card class="border-red-200 bg-red-50">
            <CardContent class="pt-6">
              <div class="flex items-center gap-3">
                <AlertCircle class="w-5 h-5 text-red-600" />
                <div>
                  <h3 class="text-sm font-medium text-red-800">Cannot Edit Request</h3>
                  <p class="text-sm text-red-700 mt-1">
                    This request cannot be edited because its status is "{{ request.status_label }}". 
                    Only open requests can be modified.
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Edit Form -->
        <Card v-if="request.status === 'open'" class="shadow-lg">
          <CardHeader class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b">
            <CardTitle class="text-2xl font-bold text-gray-900">Edit Support Request</CardTitle>
            <CardDescription class="text-gray-600">
              Update your support request details. You can only edit requests that are still open.
            </CardDescription>
          </CardHeader>
          <CardContent class="p-8">
            <form @submit.prevent="submit" class="space-y-8">
              <!-- Subject Field -->
              <div class="space-y-2">
                <Label for="subject" class="text-base font-semibold text-gray-700">Subject</Label>
                <Input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  placeholder="Enter a brief subject for your request"
                  class="h-12 text-base"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.subject }"
                />
                <p v-if="form.errors.subject" class="text-red-500 text-sm mt-1">
                  {{ form.errors.subject }}
                </p>
              </div>

              <!-- Category and Priority Row -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                  <Label for="category" class="text-base font-semibold text-gray-700">Category</Label>
                  <SelectInput v-model="form.category" :class="{ 'border-red-500': form.errors.category }" class="h-12 text-base">
                    <option value="">Select a category</option>
                    <option v-for="(label, value) in categories" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </SelectInput>
                  <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">
                    {{ form.errors.category }}
                  </p>
                </div>

                <div class="space-y-2">
                  <Label for="priority" class="text-base font-semibold text-gray-700">Priority</Label>
                  <SelectInput v-model="form.priority" :class="{ 'border-red-500': form.errors.priority }" class="h-12 text-base">
                    <option value="">Select priority level</option>
                    <option v-for="(label, value) in priorities" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </SelectInput>
                  <p v-if="form.errors.priority" class="text-red-500 text-sm mt-1">
                    {{ form.errors.priority }}
                  </p>
                </div>
              </div>

              <!-- Message Field -->
              <div class="space-y-2">
                <Label for="message" class="text-base font-semibold text-gray-700">Message</Label>
                <Textarea
                  id="message"
                  v-model="form.message"
                  rows="8"
                  placeholder="Describe your issue or request in detail..."
                  class="text-base resize-none"
                  :class="{ 'border-red-500 focus:border-red-500': form.errors.message }"
                />
                <p v-if="form.errors.message" class="text-red-500 text-sm mt-1">
                  {{ form.errors.message }}
                </p>
              </div>

              <!-- Action Buttons -->
              <div class="flex justify-end space-x-4 pt-6 border-t">
                <Button
                  type="button"
                  variant="outline"
                  size="lg"
                  @click="$inertia.visit(route('contact-support.index'))"
                  class="px-8"
                >
                  Cancel
                </Button>
                <Button 
                  type="submit" 
                  size="lg"
                  :disabled="form.processing"
                  class="px-8 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700"
                >
                  <Loader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update Request</span>
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import SelectInput from '@/components/SelectInput.vue'
import { AlertCircle, Loader2 } from 'lucide-vue-next'

const props = defineProps({
  request: Object,
  categories: Object,
  priorities: Object,
})

const form = useForm({
  subject: props.request.subject,
  message: props.request.message,
  category: props.request.category,
  priority: props.request.priority,
})



const submit = () => {
  form.put(route('contact-support.update', props.request.id))
}
</script> 