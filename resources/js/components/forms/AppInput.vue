<template>
  <input
    v-model="localValue"
    :type="type"
    class="bg-white dark:bg-gray-900 shadow-sm block w-full rounded-lg text-sm focus:outline-none focus:ring-2"
    :class="{
      'focus:ring-brand-blue focus:border-brand-blue border-gray-300 dark:border-gray-600': !hasError,
      'pr-10 border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500': hasError,
    }"
    ref="input"
  >
</template>

<script>
import { onMounted, ref } from 'vue'
import ModelValue from '@/mixins/ModelValue'

export default {
  mixins: [ModelValue],
  props: {
    type: {
      type: String,
      default: 'text',
    },
    autofocus: {
      type: Boolean,
      default: false
    },
    hasError: {
      type: Boolean,
      default: false,
    }
  },
  emits: ['update:modelValue'],

  setup (props) {
    const input = ref(null)

    if (props.autofocus) {
      onMounted(() => {
        input.value.focus()
      })
    }

    return {
      input,
    }
  }
}
</script>
