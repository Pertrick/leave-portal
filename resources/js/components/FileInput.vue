<template>
  <input
    type="file"
    :value="modelValue"
    class="block w-full text-sm text-gray-500
      file:mr-4 file:py-2 file:px-4
      file:rounded-md file:border-0
      file:text-sm file:font-semibold
      file:bg-indigo-50 file:text-indigo-700
      hover:file:bg-indigo-100
      file:cursor-pointer
      cursor-pointer"
    v-bind="$attrs"
    @input="$emit('update:modelValue', $event.target.files[0])"
  />
</template>

<script setup>
defineProps({
  modelValue: {
    type: [File, null],
    default: null,
  },
})

defineEmits(['update:modelValue'])
</script> 