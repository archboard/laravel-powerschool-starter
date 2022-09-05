<template>
  <div class="w-full bg-white dark:bg-gray-600 mt-4 shadow-lg rounded-xl pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
    <div class="p-4">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <!-- Notification icon -->
          <svg class="h-6 w-6" :class="color" v-html="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"></svg>
        </div>
        <div class="ml-3 w-0 flex-1 pt-0.5">
          <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ title }}
          </p>
          <p v-html="notification.text" class="mt-1 text-sm text-gray-500 dark:text-gray-300"></p>
        </div>
        <div class="ml-4 flex-shrink-0 flex">
          <!-- Close icon -->
          <button @click.prevent="removeNotification(notification.id)" class="p-0.5 bg-white dark:bg-gray-600 rounded-full inline-flex text-gray-400 dark:text-gray-300 hover:text-gray-500 hover:bg-gray-50 focus:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
            <span class="sr-only">Close</span>
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import store from '@/stores/notifications'

export default defineComponent({
  props: {
    notification: {
      type: Object,
      required: true
    }
  },

  setup ({ notification }) {
    const colors = {
      success: `text-green-400`,
      error: `text-red-400`,
      warning: `text-yellow-400`,
      neutral: `text-gray-400`,
    }
    const titles = {
      success: `Success!`,
      error: `Error!`,
      warning: `Warning!`,
      neutral: 'Hey!',
    }
    const icons = {
      success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />`,
      error: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
      warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`,
      neutral: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>`
    }

    return {
      color: notification.color || colors[notification.level] || colors.neutral,
      title: notification.title || titles[notification.level] || titles.neutral,
      icon: icons[notification.level] || icons.neutral,
      state: store.state,
      removeNotification: store.removeNotification,
    }
  }
})
</script>
