<template>
    <div class="relative" ref="root">
      <slot />
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, provide, onMounted, onBeforeUnmount, watch } from 'vue';
  
  const props = defineProps<{
    modelValue?: string | number;
  }>();
  
  const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
  }>();
  
  const isOpen = ref(false);
  const selectedValue = ref(props.modelValue);
  const searchQuery = ref('');
  
  function toggle() {
    isOpen.value = !isOpen.value;
    if (!isOpen.value) {
      searchQuery.value = '';
    }
  }
  
  function selectValue(value: string | number) {
    selectedValue.value = value;
    emit('update:modelValue', value);
    isOpen.value = false;
    searchQuery.value = '';
  }
  
  function updateSearch(query: string) {
    searchQuery.value = query;
  }
  
  provide('select', {
    isOpen,
    selectedValue,
    searchQuery,
    toggle,
    selectValue,
    updateSearch
  });
  
  const root = ref();
  
  function handleClickOutside(event: MouseEvent) {
    if (root.value && !root.value.contains(event.target as Node)) {
      isOpen.value = false;
      searchQuery.value = '';
    }
  }
  
  function handleEscape(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      isOpen.value = false;
      searchQuery.value = '';
    }
  }
  
  watch(() => props.modelValue, (newValue) => {
    selectedValue.value = newValue;
  });
  
  onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside);
    document.addEventListener('keydown', handleEscape);
  });
  
  onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
  });
  </script>