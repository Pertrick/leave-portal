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
  
  const open = ref(false);
  const selectedValue = ref({ value: props.modelValue });
  const searchQuery = ref<string>('');
  
  function setOpen(val: boolean) {
    open.value = val;
    if (!val) {
      searchQuery.value = '';
    }
  }
  
  function setValue(val: string | number) {
    selectedValue.value = { value: val };
    emit('update:modelValue', val);
    setOpen(false);
  }
  
  function setSearchQuery(query: string) {
    searchQuery.value = query;
  }
  
  const context = {
    open,
    selectedValue,
    searchQuery,
    setOpen,
    setValue,
    setSearchQuery,
  };
  
  provide('select', context);
  
  const root = ref();
  
  function handleClickOutside(event: MouseEvent) {
    if (root.value && !root.value.contains(event.target as Node)) {
      setOpen(false);
    }
  }
  
  function handleEscape(event: KeyboardEvent) {
    if (event.key === 'Escape') {
      setOpen(false);
    }
  }
  
  watch(() => props.modelValue, (newValue) => {
    selectedValue.value = { value: newValue };
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