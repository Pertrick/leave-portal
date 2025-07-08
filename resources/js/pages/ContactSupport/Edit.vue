<template>
  <AppLayout title="Edit Support Request">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">Edit Support Request</h2>
        <Button @click="$inertia.visit(route('contact-support.index'))" variant="outline">
          Back to Requests
        </Button>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>Edit Support Request</CardTitle>
            <CardDescription>Update your support request details</CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <Label for="subject">Subject</Label>
                <Input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  placeholder="Enter a brief subject for your request"
                  :class="{ 'border-red-500': form.errors.subject }"
                />
                <p v-if="form.errors.subject" class="text-red-500 text-sm mt-1">
                  {{ form.errors.subject }}
                </p>
              </div>

              <div>
                <Label for="category">Category</Label>
                <Select v-model="form.category" :class="{ 'border-red-500': form.errors.category }">
                  <option value="">Select a category</option>
                  <option v-for="(label, value) in categories" :key="value" :value="value">
                    {{ label }}
                  </option>
                </Select>
                <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">
                  {{ form.errors.category }}
                </p>
              </div>

              <div>
                <Label for="priority">Priority</Label>
                <Select v-model="form.priority" :class="{ 'border-red-500': form.errors.priority }">
                  <option value="">Select priority level</option>
                  <option v-for="(label, value) in priorities" :key="value" :value="value">
                    {{ label }}
                  </option>
                </Select>
                <p v-if="form.errors.priority" class="text-red-500 text-sm mt-1">
                  {{ form.errors.priority }}
                </p>
              </div>

              <div>
                <Label for="message">Message</Label>
                <Textarea
                  id="message"
                  v-model="form.message"
                  rows="6"
                  placeholder="Describe your issue or request in detail..."
                  :class="{ 'border-red-500': form.errors.message }"
                />
                <p v-if="form.errors.message" class="text-red-500 text-sm mt-1">
                  {{ form.errors.message }}
                </p>
              </div>

              <div class="flex justify-end space-x-3">
                <Button
                  type="button"
                  variant="outline"
                  @click="$inertia.visit(route('contact-support.index'))"
                >
                  Cancel
                </Button>
                <Button type="submit" :disabled="form.processing">
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
import { Select } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'

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