<template>
  <component
    :is="component"
    class="border border-transparent overflow-hidden font-medium rounded-md shadow hover:shadow-none text-white focus:outline-none transition ease-in-out duration-150 relative text-center justify-center disabled:cursor-not-allowed"
    :class="[
      buttonSize,
      buttonColor,
      {
        'flex w-full': isBlock,
        'inline-flex': !isBlock,
      }
    ]"
    :style="style"
    :disabled="loading"
  >
    <transition
      enter-active-class="transform transition duration-100 ease-in-out"
      enter-from-class="-translate-x-6 opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="transform transition duration-100 ease-in-out"
      leave-from-class="translate-x-0 opacity-100"
      leave-to-class="-translate-x-6 opacity-0"
    >
      <div
        v-show="loading"
        class="inline-flex items-center"
      >
        <svg class="animate-spin" :class="loaderSize" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </transition>

    <transition
      enter-active-class="transform transition duration-100 ease-in-out"
      enter-from-class="translate-x-6 opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="transform transition duration-100 ease-in-out"
      leave-from-class="translate-x-0 opacity-100"
      leave-to-class="translate-x-6 opacity-0"
    >
      <span
        v-show="!loading"
        class="inline-flex items-center"
      >
        <slot />
      </span>
    </transition>
  </component>
</template>

<script>
export default {
  props: {
    component: {
      type: String,
      default: 'button',
    },
    size: {
      type: String,
      default: 'base'
    },
    color: {
      type: String,
      default: 'primary'
    },
    loading: {
      type: Boolean,
      default: false
    },
    isBlock: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      sizes: {
        xs: `px-2.5 py-1.5 text-xs leading-4`,
        sm: `px-3 py-2 text-sm leading-4`,
        base: `px-4 py-2 leading-5`,
        lg: `px-4 py-2 sm:py-3 sm:text-lg leading-6`,
        xl: `px-4 sm:px-6 py-3 text-lg leading-6`,
      },
      loaderSizes: {
        lg: 'h-6 w-6',
        xl: 'h-6 w-6',
      },
      style: {
        width: '',
        height: '',
      },
      colors: {
        primary: 'bg-primary-600 hover:bg-primary-500 focus:ring focus:ring-primary-400 active:bg-primary-600',
        red: 'bg-red-600 hover:bg-red-500 focus:border-red-700 focus:shadow-outline-red active:bg-red-700',
        yellow: 'bg-yellow-600 hover:bg-yellow-500 focus:border-yellow-700 focus:shadow-outline-yellow active:bg-yellow-700',
        green: 'bg-green-600 hover:bg-green-500 focus:border-green-700 focus:shadow-outline-green active:bg-green-700',
        gray: 'bg-gray-600 hover:bg-gray-500 focus:border-gray-700 focus:shadow-outline-gray active:bg-gray-700',
        white: 'text-gray-700 bg-white hover:bg-gray-50 border-gray-300 focus:shadow-outline-gray active:bg-gray-100',
      }
    }
  },

  computed: {
    buttonSize () {
      return this.sizes[this.size] || this.sizes.base
    },

    buttonColor () {
      return this.colors[this.color] || this.colors.white
    },

    loaderSize () {
      return this.loaderSizes[this.size] || 'h-5 w-5'
    },
  },

  watch: {
    loading () {
      if (!this.isBlock) {
        this.style.width = `${this.$el.offsetWidth}px`
      }

      this.style.height = `${this.$el.offsetHeight}px`
    }
  }
}
</script>
