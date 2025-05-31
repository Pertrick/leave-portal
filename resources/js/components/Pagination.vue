<template>
  <div v-if="links.length > 3" class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <Link
        v-if="links[0].url"
        :href="links[0].url"
        class="relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50"
      >
        Previous
      </Link>
      <Link
        v-if="links[links.length - 1].url"
        :href="links[links.length - 1].url"
        class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-white border border-gray-300 hover:bg-gray-50"
      >
        Next
      </Link>
    </div>
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div v-if="meta && meta.total > 0">
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ meta.from }}</span>
          to
          <span class="font-medium">{{ meta.to }}</span>
          of
          <span class="font-medium">{{ meta.total }}</span>
          results
        </p>
      </div>
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <Link
            v-for="(link, key) in links"
            :key="key"
            :href="link.url"
            class="relative inline-flex items-center px-4 py-2 text-sm font-medium"
            :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              key === 0 ? 'rounded-l-md' : '',
              key === links.length - 1 ? 'rounded-r-md' : '',
              'border'
            ]"
            :aria-current="link.active ? 'page' : undefined"
          >
            <span v-if="key === 0" class="sr-only">Previous</span>
            <span v-else-if="key === links.length - 1" class="sr-only">Next</span>
            <span v-html="link.label"></span>
          </Link>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  links: {
    type: Array,
    default: () => [],
  },
  meta: {
    type: Object,
    default: () => ({
      from: 0,
      to: 0,
      total: 0
    })
  }
})
</script> 