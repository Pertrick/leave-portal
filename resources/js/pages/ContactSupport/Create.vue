<template>
  <AppLayout title="New Support Request">
    <template #header-actions>
      <Button @click="$inertia.visit(route('contact-support.index'))" variant="outline">
        Back to Requests
      </Button>
    </template>

    <div class="py-8">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
          <div class="flex items-center gap-3 mb-2">
            <div class="p-2 bg-blue-100 rounded-lg">
              <HelpCircle class="w-6 h-6 text-blue-600" />
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Submit Support Request</h1>
          </div>
          <p class="text-gray-600 text-lg">Describe your issue or question. We'll get back to you as soon as possible.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Form -->
          <div class="lg:col-span-2">
            <Card class="shadow-lg border-0">
              <CardContent class="p-8">
                <form @submit.prevent="submit" class="space-y-8">
                  <!-- Subject Field -->
                  <div class="space-y-2">
                    <Label for="subject" class="text-sm font-semibold text-gray-700">Subject *</Label>
                    <Input
                      id="subject"
                      v-model="form.subject"
                      placeholder="Brief description of your issue"
                      class="h-12 text-base"
                      required
                    />
                    <p v-if="form.errors.subject" class="text-red-500 text-sm">
                      {{ form.errors.subject }}
                    </p>
                  </div>

                  <!-- Category and Priority Row -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                      <Label for="category" class="text-sm font-semibold text-gray-700">Category *</Label>
                      <SelectInput v-model="form.category" required class="h-12 text-base">
                        <option value="">Select a category</option>
                        <option v-for="(label, value) in categories" :key="value" :value="value">
                          {{ label }}
                        </option>
                      </SelectInput>
                      <p v-if="form.errors.category" class="text-red-500 text-sm">
                        {{ form.errors.category }}
                      </p>
                    </div>

                    <div class="space-y-2">
                      <Label for="priority" class="text-sm font-semibold text-gray-700">Priority *</Label>
                      <SelectInput v-model="form.priority" required class="h-12 text-base">
                        <option value="">Select priority level</option>
                        <option v-for="(label, value) in priorities" :key="value" :value="value">
                          {{ label }}
                        </option>
                      </SelectInput>
                      <p v-if="form.errors.priority" class="text-red-500 text-sm">
                        {{ form.errors.priority }}
                      </p>
                    </div>
                  </div>

                  <!-- Message Field -->
                  <div class="space-y-2">
                    <Label for="message" class="text-sm font-semibold text-gray-700">Message *</Label>
                    <Textarea
                      id="message"
                      v-model="form.message"
                      placeholder="Please provide detailed information about your issue or question..."
                      rows="8"
                      class="text-base resize-none"
                      required
                    />
                    <div class="flex justify-between items-center">
                      <p v-if="form.errors.message" class="text-red-500 text-sm">
                        {{ form.errors.message }}
                      </p>
                      <p class="text-gray-500 text-sm">
                        {{ form.message.length }}/2000 characters
                      </p>
                    </div>
                  </div>

                  <!-- Submit Buttons -->
                  <div class="flex items-center gap-4 pt-4">
                    <Button 
                      type="submit" 
                      :disabled="form.processing"
                      class="h-12 px-8 text-base font-semibold bg-blue-600 hover:bg-blue-700"
                    >
                      <LoaderCircle v-if="form.processing" class="w-5 h-5 animate-spin mr-2" />
                      Submit Request
                    </Button>
                    <Button 
                      type="button" 
                      variant="outline" 
                      @click="$inertia.visit(route('contact-support.index'))"
                      class="h-12 px-8 text-base"
                    >
                      Cancel
                    </Button>
                  </div>
                </form>
              </CardContent>
            </Card>
          </div>

          <!-- Sidebar Help Information -->
          <div class="space-y-6">
            <!-- Tips Card -->
            <Card class="shadow-lg border-0 bg-gradient-to-br from-blue-50 to-indigo-50">
              <CardContent class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 bg-blue-100 rounded-lg">
                    <HelpCircle class="w-5 h-5 text-blue-600" />
                  </div>
                  <h3 class="font-semibold text-gray-900">Writing Tips</h3>
                </div>
                <ul class="space-y-3 text-sm text-gray-700">
                  <li class="flex items-start gap-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Be specific about the issue you're experiencing</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Include any error messages or screenshots</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Mention what you've already tried</span>
                  </li>
                  <li class="flex items-start gap-2">
                    <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                    <span>Provide context about when it occurs</span>
                  </li>
                </ul>
              </CardContent>
            </Card>

            <!-- Response Time Card -->
            <Card class="shadow-lg border-0 bg-gradient-to-br from-green-50 to-emerald-50">
              <CardContent class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 bg-green-100 rounded-lg">
                    <Clock class="w-5 h-5 text-green-600" />
                  </div>
                  <h3 class="font-semibold text-gray-900">Response Time</h3>
                </div>
                <div class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Standard Requests</span>
                    <span class="text-sm font-semibold text-gray-900">24 hours</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Urgent Requests</span>
                    <span class="text-sm font-semibold text-green-600">4-8 hours</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Business Days</span>
                    <span class="text-sm font-semibold text-gray-900">Mon-Fri</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            <!-- Priority Guide Card -->
            <Card class="shadow-lg border-0 bg-gradient-to-br from-amber-50 to-orange-50">
              <CardContent class="p-6">
                <div class="flex items-center gap-3 mb-4">
                  <div class="p-2 bg-amber-100 rounded-lg">
                    <HelpCircle class="w-5 h-5 text-amber-600" />
                  </div>
                  <h3 class="font-semibold text-gray-900">Priority Guide</h3>
                </div>
                <div class="space-y-3 text-sm">
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Low</span>
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">General questions</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Medium</span>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Minor issues</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">High</span>
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">Important issues</span>
                  </div>
                  <div class="flex items-center justify-between">
                    <span class="text-gray-600">Urgent</span>
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Critical problems</span>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
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
import { LoaderCircle, HelpCircle, Clock } from 'lucide-vue-next'

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