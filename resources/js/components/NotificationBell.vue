<template>
  <div class="relative">
    <button
      @click="navigateToNotifications"
      class="relative p-2 text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 rounded-md transition-colors duration-200"
    >
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 6 6v3.75l1.5 1.5H3l1.5-1.5V9.75a6 6 0 0 1 6-6z" />
      </svg>
      
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 bg-destructive text-destructive-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

const unreadCount = ref(0)

const navigateToNotifications = () => {
  router.visit('/notifications')
}

const loadUnreadCount = async () => {
  try {
    const response = await fetch('/api/notifications')
    const data = await response.json()
    unreadCount.value = data.unread_count
  } catch (error) {
    console.error('Failed to load notification count:', error)
  }
}

onMounted(() => {
  loadUnreadCount()
})
</script> 