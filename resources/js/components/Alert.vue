<template>
  <div v-if="show" class="rounded-md p-4 shadow" :class="bgColor">
    <div class="flex">
      <div class="flex-shrink-0">
        <svg class="h-5 w-5" :class="iconColor" v-html="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"></svg>
      </div>
      <div class="ml-3">
        <p class="text-sm font-medium" :class="textColor">
          <slot/>
        </p>
      </div>
      <div class="ml-auto pl-3">
        <div class="-mx-1 -my-1">
          <button :class="closeColor" @click.prevent="show = false" class="inline-flex rounded-full p-1 focus:outline-none focus:ring-2">
            <span class="sr-only">Dismiss</span>
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
import { defineComponent, ref } from 'vue'

export default defineComponent({
  props: {
    level: {
      type: String,
      default: 'neutral'
    }
  },

  setup ({ level }) {
    let show = ref(true)
    const bgColors = {
      success: 'bg-green-50 dark:bg-green-800',
      error: 'bg-red-50 dark:bg-red-800',
      warning: 'bg-yellow-50 dark:bg-yellow-800',
      neutral: 'bg-gray-100 dark:bg-gray-700',
    }
    const iconColors = {
      success: 'text-green-400 dark:text-green-200',
      error: 'text-red-400 dark:text-red-200',
      warning: 'text-yellow-400 dark:text-yellow-200',
      neutral: 'text-gray-400 dark:text-gray-200',
    }
    const textColors = {
      success: 'text-green-800 dark:text-green-100',
      error: 'text-red-800 dark:text-red-100',
      warning: 'text-yellow-800 dark:text-yellow-100',
      neutral: 'text-gray-800 dark:text-gray-100',
    }
    const closeColors = {
      success: 'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-offset-green-50 focus:ring-green-600 dark:bg-green-800 dark:text-green-400',
      error: 'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-offset-red-50 focus:ring-red-600 dark:bg-red-800 dark:text-red-400',
      warning: 'bg-yellow-50 text-yellow-500 hover:bg-yellow-100 focus:ring-offset-yellow-50 focus:ring-yellow-600 dark:bg-yellow-800 dark:text-yellow-400',
      neutral: 'bg-gray-100 text-gray-500 hover:bg-gray-200 focus:ring-offset-gray-100 focus:ring-gray-600 dark:bg-gray-700 dark:text-gray-400',
    }
    const icons = {
      success: `<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />`,
      error: `<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>`,
      warning: `<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>`,
      neutral: `<path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>`,
    }

    return {
      show,
      iconColor: iconColors[level] || iconColors.neutral,
      bgColor: bgColors[level] || bgColors.neutral,
      textColor: textColors[level] || textColors.neutral,
      closeColor: closeColors[level] || closeColors.neutral,
      icon: icons[level] || icons.neutral,
    }
  }
})
</script>
