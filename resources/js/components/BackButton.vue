<template>
  <button
    @click="goBack"
    class="inline-flex items-center px-3 py-1.5 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium shadow-sm border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition cursor-pointer"
    aria-label="Go back"
  >
    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
      <path d="M15 19l-7-7 7-7" />
    </svg>
    Back
  </button>
</template>

<script setup lang="ts">
function goBack() {
  window.history.length > 1 ? window.history.back() : window.location.href = '/';
}
</script> 