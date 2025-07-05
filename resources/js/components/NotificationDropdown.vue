<template>
  <div class="relative">
    <!-- Notification Bell -->
    <button
      @click="toggleDropdown"
      class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 6 6v3.75l1.5 1.5H3l1.5-1.5V9.75a6 6 0 0 1 6-6z" />
      </svg>
      
      <!-- Notification Badge -->
      <span
        v-if="unreadCount > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"
      >
        {{ unreadCount > 99 ? '99+' : unreadCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-50"
    >
      <div class="py-2">
        <!-- Header -->
        <div class="px-4 py-2 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            <button
              v-if="unreadCount > 0"
              @click="markAllAsRead"
              class="text-xs text-indigo-600 hover:text-indigo-900"
            >
              Mark all as read
            </button>
          </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
          <div v-if="loading" class="px-4 py-3 text-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-sm text-gray-600">Loading notifications...</p>
          </div>

          <div v-else-if="notifications.length === 0" class="px-4 py-3 text-center">
            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 1 6 6v3.75l1.5 1.5H3l1.5-1.5V9.75a6 6 0 0 1 6-6z" />
            </svg>
            <p class="mt-2 text-sm text-gray-600">No notifications</p>
          </div>

          <div v-else>
            <div
              v-for="notification in notifications"
              :key="notification.id"
              @click="handleNotificationClick(notification)"
              class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0"
              :class="{ 'bg-indigo-50': !notification.read_at }"
            >
              <div class="flex items-start space-x-3">
                <!-- Icon -->
                <div class="flex-shrink-0">
                  <span class="text-lg">{{ notification.data.icon || '📢' }}</span>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ notification.data.title }}
                    </p>
                    <span class="text-xs text-gray-500">
                      {{ formatTime(notification.created_at) }}
                    </span>
                  </div>
                  <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                    {{ notification.data.message }}
                  </p>
                  
                  <!-- Priority Badge -->
                  <div v-if="notification.data.priority && notification.data.priority !== 'normal'" class="mt-2">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      :class="{
                        'bg-red-100 text-red-800': notification.data.priority === 'high',
                        'bg-yellow-100 text-yellow-800': notification.data.priority === 'warning',
                        'bg-blue-100 text-blue-800': notification.data.priority === 'low'
                      }"
                    >
                      {{ notification.data.priority }}
                    </span>
                  </div>
                </div>

                <!-- Unread Indicator -->
                <div v-if="!notification.read_at" class="flex-shrink-0">
                  <div class="w-2 h-2 bg-indigo-600 rounded-full"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-2 border-t border-gray-200">
          <router-link
            to="/notifications"
            class="text-sm text-indigo-600 hover:text-indigo-900 font-medium"
          >
            View all notifications
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import { formatDistanceToNow } from 'date-fns'

const props = defineProps({
  initialNotifications: {
    type: Array,
    default: () => []
  },
  initialUnreadCount: {
    type: Number,
    default: 0
  }
})

const isOpen = ref(false)
const notifications = ref(props.initialNotifications)
const unreadCount = ref(props.initialUnreadCount)
const loading = ref(false)

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    isOpen.value = false
  }
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    loadNotifications()
  }
}

const loadNotifications = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/notifications')
    const data = await response.json()
    notifications.value = data.notifications
    unreadCount.value = data.unread_count
  } catch (error) {
    console.error('Failed to load notifications:', error)
  } finally {
    loading.value = false
  }
}

const markAllAsRead = async () => {
  try {
    await fetch('/api/notifications/mark-all-read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    
    notifications.value = notifications.value.map(notification => ({
      ...notification,
      read_at: new Date().toISOString()
    }))
    unreadCount.value = 0
  } catch (error) {
    console.error('Failed to mark notifications as read:', error)
  }
}

const handleNotificationClick = async (notification) => {
  // Mark as read
  if (!notification.read_at) {
    try {
      await fetch(`/api/notifications/${notification.id}/mark-read`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      
      notification.read_at = new Date().toISOString()
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    } catch (error) {
      console.error('Failed to mark notification as read:', error)
    }
  }

  // Navigate based on notification type
  if (notification.data.type === 'leave_submitted' || 
      notification.data.type === 'leave_approved' || 
      notification.data.type === 'leave_rejected') {
    router.visit(`/leave/applications/${notification.data.leave_id}`)
  } else if (notification.data.type === 'pending_approval') {
    router.visit(`/leave/approvals/${notification.data.leave_id}`)
  }

  isOpen.value = false
}

const formatTime = (dateString) => {
  return formatDistanceToNow(new Date(dateString), { addSuffix: true })
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 