<template>
  <input
    type="checkbox"
    :class="[
      'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0',
      $attrs.class
    ]"
    v-bind="$attrs"
    :checked="isChecked"
    @change="handleChange"
  />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [Array, Boolean],
    default: () => []
  },
  value: {
    type: [String, Number],
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'update:checked'])

const isChecked = computed(() => {
  if (Array.isArray(props.modelValue)) {
    return props.modelValue.includes(props.value)
  }
  return props.modelValue
})

const handleChange = (event) => {
  const checked = event.target.checked
  
  if (Array.isArray(props.modelValue)) {
    const newValue = [...props.modelValue]
    if (checked) {
      if (!newValue.includes(props.value)) {
        newValue.push(props.value)
      }
    } else {
      const index = newValue.indexOf(props.value)
      if (index > -1) {
        newValue.splice(index, 1)
      }
    }
    emit('update:modelValue', newValue)
  } else {
    emit('update:modelValue', checked)
  }
  
  emit('update:checked', checked)
}
</script>
