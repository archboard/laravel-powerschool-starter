<template>
  <Authenticated>
    <CardWrapper>
      <CardPadding>
        <CardHeader>{{ __('Welcome, :name', { name: user.first_name }) }}</CardHeader>
      </CardPadding>
    </CardWrapper>
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import CardHeader from '@/components/CardHeader.vue'

defineProps({
  user: Object,
})
</script>
