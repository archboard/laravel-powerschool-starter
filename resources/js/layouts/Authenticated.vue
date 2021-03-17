<template>
  <div data-cy="page">
    <slot />

    <Notifications />
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue'
import Notifications from '../components/Notifications'

export default defineComponent({
  components: {
    Notifications
  },

  setup () {

  }
})
</script>
