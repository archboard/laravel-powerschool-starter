<template>
  <div>
    <slot />
    <Error v-if="error">{{ error }}</Error>
  </div>
</template>
<script>
import Error from '@/components/forms/Error'

export default {
  components: {
    Error,
  },

  props: {
    error: String,
  }
}
</script>
