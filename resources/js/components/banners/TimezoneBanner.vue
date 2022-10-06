<template>
  <div v-if="show" class="fixed inset-x-0 bottom-0">
    <div class="bg-yellow-600">
      <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap">
          <div class="w-0 flex-1 flex items-center">
            <span class="flex p-2 rounded-lg bg-yellow-800">
              <ClockIcon class="h-6 w-6 text-white" aria-hidden="true" />
            </span>
            <p class="ml-3 font-medium text-white truncate">
              <span class="md:hidden">{{ __("Are you in a different timezone?") }}</span>
              <span class="hidden md:inline">{{ __("It looks like you're in a different timezone.") }}</span>
            </p>
          </div>
          <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
            <button @click.prevent="$emit('launch')" class="flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-yellow-600 bg-white hover:bg-yellow-50">
              {{ __('Update timezone') }}
            </button>
          </div>
          <div class="order-2 flex-shrink-0 sm:order-3 sm:ml-3">
            <button @click.prevent="show = false" type="button" class="-mr-1 flex p-2 rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-white sm:-mr-2">
              <span class="sr-only">Dismiss</span>
              <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ClockIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

export default {
  components: {
    ClockIcon,
    XMarkIcon,
  },
  emits: ['launch'],

  setup () {
    const show = ref(true)

    return {
      show,
    }
  }
}
</script>
