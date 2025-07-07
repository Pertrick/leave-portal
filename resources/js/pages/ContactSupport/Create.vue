<template>
  <AppLayout title="New Support Request">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">New Support Request</h2>
        <Button @click="$inertia.visit(route('contact-support.index'))" variant="outline">
          Back to Requests
        </Button>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <Card>
          <CardHeader>
            <CardTitle>Submit Support Request</CardTitle>
            <CardDescription>
              Describe your issue or question. We'll get back to you as soon as possible.
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div>
                <Label for="subject">Subject</Label>
                <Input
                  id="subject"
                  v-model="form.subject"
                  placeholder="Brief description of your issue"
                  required
                />
                <p v-if="form.errors.subject" class="text-red-500 text-sm mt-1">
                  {{ form.errors.subject }}
                </p>
              </div>

              <div>
                <Label for="category">Category</Label>
                <Select v-model="form.category" required>
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
                <Select v-model="form.priority" required>
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
                  placeholder="Please provide detailed information about your issue or question..."
                  rows="6"
                  required
                />
                <p v-if="form.errors.message" class="text-red-500 text-sm mt-1">
                  {{ form.errors.message }}
                </p>
                <p class="text-gray-500 text-sm mt-1">
                  {{ form.message.length }}/2000 characters
                </p>
              </div>

              <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                  <LoaderCircle v-if="form.processing" class="w-4 h-4 animate-spin mr-2" />
                  Submit Request
                </Button>
                <Button 
                  type="button" 
                  variant="outline" 
                  @click="$inertia.visit(route('contact-support.index'))"
                >
                  Cancel
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <!-- Help Information -->
        <Card class="mt-6">
          <CardHeader>
            <CardTitle>Need Help?</CardTitle>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <div class="flex items-start gap-3">
                <QuestionMarkCircleIcon class="w-5 h-5 text-blue-500 mt-0.5" />
                <div>
                  <h4 class="font-medium">How to write a good support request:</h4>
                  <ul class="text-sm text-gray-600 mt-1 space-y-1">
                    <li>• Be specific about the issue you're experiencing</li>
                    <li>• Include any error messages or screenshots if applicable</li>
                    <li>• Mention what you've already tried to resolve the issue</li>
                    <li>• Provide context about when the issue occurs</li>
                  </ul>
                </div>
              </div>
              
              <div class="flex items-start gap-3">
                <ClockIcon class="w-5 h-5 text-green-500 mt-0.5" />
                <div>
                  <h4 class="font-medium">Response Time:</h4>
                  <p class="text-sm text-gray-600 mt-1">
                    We typically respond within 24 hours during business days. 
                    Urgent requests are prioritized.
                  </p>
                </div>
              </div>
            </div>
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
import { Select } from '@/components/ui/select'
import { LoaderCircle, QuestionMarkCircleIcon, ClockIcon } from 'lucide-vue-next'

const props = defineProps({
  categories: Object,
  priorities: Object
})

const form = useForm({
  subject: '',
  message: '',
  category: '',
  priority: ''
})

const submit = () => {
  form.post(route('contact-support.store'), {
    onSuccess: () => {
      form.reset()
    }
  })
}
</script> 