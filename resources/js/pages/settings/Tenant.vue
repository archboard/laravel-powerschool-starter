<template>
  <Authenticated>
    <div class="space-y-8">
      <AppForm
        v-if="smtpForm"
        :form="smtpForm"
      >
        <template #actions="{ loading }">
          <AppButton color="white">{{ __('Send test') }}</AppButton>
          <AppButton type="submit" :loading="loading" />
        </template>
      </AppForm>
    </div>
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import CardHeader from '@/components/CardHeader.vue'
import DynamicFormFields from '@/components/forms/fields/DynamicFormFields.vue'
import { useForm } from '@inertiajs/vue3'
import CardAction from '@/components/CardAction.vue'
import AppForm from '@/components/forms/AppForm.vue'
import AppButton from '@/components/AppButton.vue'

const props = defineProps({
  smtpForm: Object,
})
</script>
