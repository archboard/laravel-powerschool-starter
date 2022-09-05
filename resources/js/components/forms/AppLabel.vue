<template>
  <label :class="['block text-sm font-medium', hasError ? 'text-red-700' : 'text-gray-900']">
    <slot />
  </label>
</template>

<script>
import { defineComponent, ref } from 'vue'

export default defineComponent({
  props: {
    hasError: {
      type: Boolean,
      default: false,
    }
  }
})
</script>
