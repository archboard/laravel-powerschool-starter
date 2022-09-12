<template>
  <div class="min-h-screen flex flex-col sm:justify-center pt-48 sm:pt-0 items-center bg-gradient-to-br from-yellow-300 via-red-400 to-primary-600 dark:from-yellow-700 dark:via-red-900 dark:to-primary-800">
    <div class="max-w-md px-4 w-full relative">
      <div class="pb-5 absolute top-0 inset-x-0 flex justify-center transform -translate-y-full">
        <InertiaLink href="/">
<!--          <Logo class="w-20 h-20 fill-current text-white" />-->
        </InertiaLink>
      </div>

      <Alert v-if="status" level="success" class="mb-4">
        {{ status }}
      </Alert>

      <CardWrapper>
        <CardPadding>
          <slot />
        </CardPadding>
      </CardWrapper>
    </div>

    <Notifications />
  </div>
</template>

<script setup>
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import Alert from '@/components/alerts/SimpleAlert.vue'
import Notifications from '@/components/Notifications.vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { computed } from 'vue'

const status = computed(() => {
  return usePage().props.value.status
})
</script>
