<template>
  <div class="flex items-center justify-center min-h-screen bg-brand-base px-4">
    <slot />
  </div>
</template>

<script setup>
import usesPageTitle from '@/composition/usesPageTitle.js'

const title = usesPageTitle()
</script>
