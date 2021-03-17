<template>
  <div class="min-h-screen flex flex-col sm:justify-center pt-48 sm:pt-0 items-center bg-gradient-to-br from-yellow-300 via-red-400 to-primary-600 dark:from-yellow-700 dark:via-red-900 dark:to-primary-800">
    <div class="max-w-md px-4 w-full relative">
      <div class="pb-5 absolute top-0 inset-x-0 flex justify-center transform -translate-y-full">
        <inertia-link href="/">
          <Logo class="w-20 h-20 fill-current text-white" />
        </inertia-link>
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

<script>
import Logo from '@/components/ApplicationLogo'
import CardWrapper from '@/components/CardWrapper'
import CardPadding from '@/components/CardPadding'
import Alert from '../components/Alert'
import Notifications from '../components/Notifications'

export default {
  components: {
    Notifications,
    Alert,
    CardPadding,
    CardWrapper,
    Logo,
  },

  computed: {
    status () {
      return this.$page?.props?.status
    }
  }
}
</script>
