<template>
  <Authenticated>
    <pre>{{ student }}</pre>
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'

defineProps({
  student: Object,
})
</script>
