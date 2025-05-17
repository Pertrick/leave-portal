<template>
  <Modal :show="show" @close="close">
    <div class="p-6 max-w-md mx-auto">
      <div class="text-center">
        <div 
          class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"
          :class="{
            'bg-green-100': type === 'success',
            'bg-red-100': type === 'danger',
            'bg-yellow-100': type === 'warning',
            'bg-blue-100': type === 'info'
          }"
        >
          <component 
            :is="icon" 
            class="h-6 w-6"
            :class="{
              'text-green-600': type === 'success',
              'text-red-600': type === 'danger',
              'text-yellow-600': type === 'warning',
              'text-blue-600': type === 'info'
            }"
          />
        </div>
        <h2 class="text-lg font-medium text-gray-900">
          {{ title }}
        </h2>
        <p class="mt-2 text-sm text-gray-600">
          {{ message }}
        </p>
      </div>

      <!-- Optional Content Slot -->
      <div v-if="$slots.content" class="mt-4">
        <slot name="content" />
      </div>

      <div class="mt-6 flex justify-center space-x-3">
        <SecondaryButton @click="close">
          {{ cancelText }}
        </SecondaryButton>
        <button
          @click="confirm"
          :disabled="disabled"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2"
          :class="{
            'bg-green-600 hover:bg-green-700 focus:ring-green-500': type === 'success',
            'bg-red-600 hover:bg-red-700 focus:ring-red-500': type === 'danger',
            'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500': type === 'warning',
            'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': type === 'info'
          }"
        >
          <component 
            v-if="icon" 
            :is="icon" 
            class="h-4 w-4 mr-2"
          />
          {{ confirmText }}
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { computed } from 'vue'
import Modal from '@/components/Modal.vue'
import SecondaryButton from '@/components/SecondaryButton.vue'
import { 
  CheckCircleIcon, 
  XCircleIcon, 
  ExclamationTriangleIcon,
  InformationCircleIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  show: {
    type: Boolean,
    required: true
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'danger', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    required: true
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  disabled: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'confirm'])

const icon = computed(() => {
  switch (props.type) {
    case 'success':
      return CheckCircleIcon
    case 'danger':
      return XCircleIcon
    case 'warning':
      return ExclamationTriangleIcon
    case 'info':
      return InformationCircleIcon
    default:
      return InformationCircleIcon
  }
})

const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm')
}
</script> 