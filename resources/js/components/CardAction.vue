<template>
  <div
    class="bg-gray-50 dark:bg-gray-700 rounded-br-md rounded-bl-md"
    :class="{
      '-mx-4 -mb-5 mt-6 sm:-mx-6': negativeMargin
    }"
  >
    <CardPadding class="space-x-2 flex">
      <slot/>
    </CardPadding>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import CardPadding from './CardPadding'

export default defineComponent({
  components: {
    CardPadding
  },

  props: {
    negativeMargin: {
      type: Boolean,
      default: false
    }
  }
})
</script>
