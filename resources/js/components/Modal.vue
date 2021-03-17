<template>
  <teleport to="body">
    <div class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center">
        <transition
          enter-active-class="duration-300 ease-out"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="duration-200 ease-in"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-if="show" class="fixed inset-0 transition-opacity" style="backdrop-filter: blur(5px);" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-800 opacity-75"></div>
          </div>
        </transition>

        <!-- This element is to trick the browser into centering the modal contents. -->
<!--        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>-->

        <transition
          enter-active-class="ease-out duration-300"
          enter-from-class="opacity-0 -translate-y-5"
          enter-to-class="opacity-100 translate-y-0"
          leave-active-class="ease-in duration-200"
          leave-from-class="opacity-100 translate-y-0"
          leave-to-class="opacity-0 -translate-y-5"
          @after-leave="$emit('close')"
        >
          <div v-if="show" ref="modal" :class="modalSize" class="inline-block align-middle bg-white dark:bg-gray-700 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="hidden sm:block absolute top-0 right-0 pt-4 pr-4">
              <button @click.prevent="close" type="button" class="bg-white dark:bg-gray-700 rounded-full text-gray-400 hover:text-gray-500 focus:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition ease-in-out">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="sm:flex sm:items-start px-4 pt-5 pb-4 sm:p-6">
              <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                <h3 v-if="headline" class="text-lg mb-2 leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-headline">
                  {{ headline }}
                </h3>
                <div>
                  <slot/>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 border-t border-transparent dark:border-gray-500 px-4 py-3 sm:px-6 flex flex-col space-y-2 sm:space-y-0 sm:flex-row-reverse">
              <slot name="actions">
                <app-button @click.prevent="performAction" type="button" :color="actionColor" class="sm:ml-2">
                  {{ actionText }}
                </app-button>
                <app-button @click.prevent="close" type="button" color="white">
                  Cancel
                </app-button>
              </slot>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </teleport>
</template>

<script>
import { defineComponent } from 'vue'
import { disableBodyScroll, clearAllBodyScrollLocks } from 'body-scroll-lock'

export default defineComponent({
  emits: ['close', 'action'],

  props: {
    headline: String,
    actionText: String,
    actionLoading: {
      type: Boolean,
      default: false
    },
    actionColor: {
      type: String,
      default: 'primary',
    },
    autoClose: {
      type: Boolean,
      default: true
    },
    size: {
      type: String,
      default: 'lg'
    }
  },

  data () {
    return {
      show: false,
    }
  },

  computed: {
    modalSize () {
      const modalSizes = {
        xs: 'sm:max-w-xs',
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
        '3xl': 'sm:max-w-3xl',
        '4xl': 'sm:max-w-4xl',
      }

      return modalSizes[this.size]
    },

    localActionText () {
      return this.actionText || 'Confirm'
    }
  },

  mounted () {
    this.show = true

    document.addEventListener('keydown', this.listener)

    this.$nextTick(() => {
      disableBodyScroll(this.$refs.modal)
    })
  },

  unmounted () {
    clearAllBodyScrollLocks()
    document.removeEventListener('keydown', this.listener)
  },

  methods: {
    performAction () {
      this.$emit('action', this.close)

      if (this.autoClose) {
        this.close()
      }
    },

    close () {
      this.show = false
    },

    clickedAway () {
      console.log('clicked away')
    },

    isLevel (level) {
      return this.level === level
    },

    listener (e) {
      if (e.key === 'Escape') {
        e.stopPropagation()
        this.close()
      }
    },
  }
})
</script>
