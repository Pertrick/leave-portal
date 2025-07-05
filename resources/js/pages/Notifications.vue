<template>
  <AppLayout title="Notifications">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div class="text-center flex-1">
          <h1 class="text-2xl font-bold text-foreground">Notifications</h1>
          <p class="text-muted-foreground">Manage your notifications and stay updated</p>
        </div>
        
        <div class="flex items-center gap-2">
          <Button
            v-if="notifications.length > 0 && unreadCount > 0"
            @click="markAllAsRead"
            variant="outline"
            size="sm"
          >
            <CheckIcon class="w-4 h-4 mr-2" />
            Mark all as read
          </Button>
          
          <Button
            v-if="notifications.length > 0"
            @click="deleteAllNotifications"
            variant="outline"
            size="sm"
            class="text-destructive hover:text-destructive"
          >
            <TrashIcon class="w-4 h-4 mr-2" />
            Clear all
          </Button>
        </div>
      </div>

      <!-- Filters -->
      <div class="flex items-center gap-4 p-4 bg-card rounded-lg border">
        <div class="flex items-center gap-2">
          <Button
            @click="filter = 'all'"
            :variant="filter === 'all' ? 'default' : 'outline'"
            size="sm"
          >
            All ({{ totalCount }})
          </Button>
          <Button
            @click="filter = 'unread'"
            :variant="filter === 'unread' ? 'default' : 'outline'"
            size="sm"
          >
            Unread ({{ unreadCount }})
          </Button>
        </div>
        
        <div class="flex items-center gap-2 ml-auto">
          <Button
            @click="refreshNotifications"
            variant="ghost"
            size="sm"
            :disabled="loading"
          >
            <ArrowPathIcon class="w-4 h-4" :class="{ 'animate-spin': loading }" />
          </Button>
        </div>
      </div>

      <!-- Notifications List -->
      <div class="bg-card rounded-lg border overflow-hidden">
        <div v-if="loading && notifications.length === 0" class="p-12">
          <div class="flex items-center justify-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
          </div>
        </div>
        
        <div v-else-if="filteredNotifications.length === 0" class="p-12 text-center">
          <div class="mx-auto w-16 h-16 bg-muted rounded-full flex items-center justify-center mb-4">
            <BellIcon class="w-8 h-8 text-muted-foreground" />
          </div>
          <h3 class="text-lg font-medium text-foreground mb-2">
            {{ filter === 'unread' ? 'No unread notifications' : 'No notifications' }}
          </h3>
          <p class="text-muted-foreground">
            {{ filter === 'unread' ? 'You\'re all caught up!' : 'Notifications will appear here when you receive them.' }}
          </p>
        </div>
        
        <div v-else class="divide-y divide-border">
          <TransitionGroup name="notification" tag="div">
            <div
              v-for="notification in filteredNotifications"
              :key="notification.id"
              class="group p-6 hover:bg-accent/50 transition-all duration-200"
              :class="{ 'bg-accent/30': !notification.read_at }"
            >
              <div class="flex items-start space-x-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-lg">{{ getNotificationIcon(notification) }}</span>
                  </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <div class="flex items-center gap-2">
                        <h4 class="text-sm font-medium text-foreground">
                          {{ notification.data.title }}
                        </h4>
                        <div v-if="!notification.read_at" class="w-2 h-2 bg-primary rounded-full"></div>
                      </div>
                      <p class="text-sm text-muted-foreground mt-1">
                        {{ notification.data.message }}
                      </p>
                      <p class="text-xs text-muted-foreground mt-2">
                        {{ formatTime(notification.created_at) }}
                      </p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                      <Button
                        v-if="!notification.read_at"
                        @click="markAsRead(notification)"
                        variant="ghost"
                        size="sm"
                        class="h-8 w-8 p-0"
                      >
                        <CheckIcon class="w-4 h-4" />
                      </Button>
                      
                      <Button
                        @click="deleteNotification(notification)"
                        variant="ghost"
                        size="sm"
                        class="h-8 w-8 p-0 text-destructive hover:text-destructive hover:bg-destructive/10"
                      >
                        <TrashIcon class="w-4 h-4" />
                      </Button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="filteredNotifications.length > 0 && hasMore" class="flex items-center justify-center">
        <Button
          @click="loadMore"
          :disabled="loading"
          variant="outline"
          size="sm"
        >
          <span v-if="loading" class="flex items-center gap-2">
            <ArrowPathIcon class="w-4 h-4 animate-spin" />
            Loading...
          </span>
          <span v-else>Load More</span>
        </Button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Dialog :open="showDeleteModal" @update:open="showDeleteModal = false">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Delete Notification</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete this notification? This action cannot be undone.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteModal = false">
            Cancel
          </Button>
          <Button variant="destructive" @click="confirmDelete">
            Delete
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete All Confirmation Modal -->
    <Dialog :open="showDeleteAllModal" @update:open="showDeleteAllModal = false">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Clear All Notifications</DialogTitle>
          <DialogDescription>
            Are you sure you want to delete all notifications? This action cannot be undone.
          </DialogDescription>
        </DialogHeader>
        <DialogFooter>
          <Button variant="outline" @click="showDeleteAllModal = false">
            Cancel
          </Button>
          <Button variant="destructive" @click="confirmDeleteAll">
            Clear All
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Button } from '@/components/ui/button'
import { 
  Dialog, 
  DialogContent, 
  DialogDescription, 
  DialogFooter, 
  DialogHeader, 
  DialogTitle 
} from '@/components/ui/dialog'
import { 
  BellIcon, 
  CheckIcon, 
  TrashIcon, 
  ArrowPathIcon,
  DocumentTextIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline'

const notifications = ref([])
const unreadCount = ref(0)
const totalCount = ref(0)
const loading = ref(false)
const hasMore = ref(true)
const page = ref(1)
const filter = ref('all')
const showDeleteModal = ref(false)
const showDeleteAllModal = ref(false)
const notificationToDelete = ref(null)

const filteredNotifications = computed(() => {
  if (filter.value === 'unread') {
    return notifications.value.filter(n => !n.read_at)
  }
  return notifications.value
})

const getNotificationIcon = (notification) => {
  const type = notification.data.type || 'default'
  const icons = {
    'leave_submitted': '📝',
    'leave_approved': '✅',
    'leave_rejected': '❌',
    'reminder': '⏰',
    'default': '📢'
  }
  return icons[type] || icons.default
}

const loadNotifications = async (reset = false) => {
  if (loading.value) return
  
  loading.value = true
  
  try {
    if (reset) {
      page.value = 1
      notifications.value = []
    }
    
    const response = await fetch(`/api/notifications?page=${page.value}`, {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      const text = await response.text()
      console.error('Non-JSON response:', text)
      throw new Error('Server returned non-JSON response')
    }
    
    const data = await response.json()
    
    if (reset) {
      notifications.value = data.notifications
    } else {
      notifications.value.push(...data.notifications)
    }
    
    unreadCount.value = data.unread_count
    totalCount.value = data.total_count || notifications.value.length
    hasMore.value = data.notifications.length === 20
  } catch (error) {
    console.error('Failed to load notifications:', error)
    // Show user-friendly error message
    notifications.value = []
    unreadCount.value = 0
    totalCount.value = 0
  } finally {
    loading.value = false
  }
}

const refreshNotifications = async () => {
  await loadNotifications(true)
}

const markAsRead = async (notification) => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) {
      console.error('CSRF token not found')
      return
    }

    const response = await fetch(`/api/notifications/${notification.id}/mark-read`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    if (result.success) {
      notification.read_at = new Date().toISOString()
      unreadCount.value = Math.max(0, unreadCount.value - 1)
    }
  } catch (error) {
    console.error('Failed to mark notification as read:', error)
  }
}

const markAllAsRead = async () => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) {
      console.error('CSRF token not found')
      return
    }

    const response = await fetch('/api/notifications/mark-all-read', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    if (result.success) {
      notifications.value.forEach(notification => {
        notification.read_at = new Date().toISOString()
      })
      unreadCount.value = 0
    }
  } catch (error) {
    console.error('Failed to mark all notifications as read:', error)
  }
}

const deleteNotification = (notification) => {
  notificationToDelete.value = notification
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!notificationToDelete.value) return
  
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) {
      console.error('CSRF token not found')
      return
    }

    const response = await fetch(`/api/notifications/${notificationToDelete.value.id}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    if (result.success) {
      const index = notifications.value.findIndex(n => n.id === notificationToDelete.value.id)
      if (index > -1) {
        notifications.value.splice(index, 1)
        if (!notificationToDelete.value.read_at) {
          unreadCount.value = Math.max(0, unreadCount.value - 1)
        }
        totalCount.value = Math.max(0, totalCount.value - 1)
      }
    }
  } catch (error) {
    console.error('Failed to delete notification:', error)
  } finally {
    showDeleteModal.value = false
    notificationToDelete.value = null
  }
}

const deleteAllNotifications = () => {
  showDeleteAllModal.value = true
}

const confirmDeleteAll = async () => {
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (!csrfToken) {
      console.error('CSRF token not found')
      return
    }

    const response = await fetch('/api/notifications/delete-all', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    if (result.success) {
      notifications.value = []
      unreadCount.value = 0
      totalCount.value = 0
    }
  } catch (error) {
    console.error('Failed to delete all notifications:', error)
  } finally {
    showDeleteAllModal.value = false
  }
}

const loadMore = async () => {
  if (hasMore.value && !loading.value) {
    page.value++
    await loadNotifications()
  }
}

const formatTime = (dateString) => {
  const date = new Date(dateString)
  const now = new Date()
  const diffInHours = (now - date) / (1000 * 60 * 60)
  
  if (diffInHours < 1) {
    return 'Just now'
  } else if (diffInHours < 24) {
    return `${Math.floor(diffInHours)}h ago`
  } else {
    return `${Math.floor(diffInHours / 24)}d ago`
  }
}

onMounted(() => {
  loadNotifications(true)
})
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(-20px);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style> 