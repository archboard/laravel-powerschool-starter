<template>
  <div class="space-y-6">
    <CardWrapper>
      <CardPadding>
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <slot name="headline" />
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
              <slot />
            </div>
          </div>
        </div>
      </CardPadding>
    </CardWrapper>
    <FormActions v-if="!hideActions">
      <slot name="actions">
        <AppButton component="Link" :href="cancel" color="white">
          {{ __('Cancel') }}
        </AppButton>
        <AppButton type="submit" :loading="loading">
          {{ __('Save') }}
        </AppButton>
      </slot>
    </FormActions>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import FormActions from '@/components/forms/FormActions.vue'

export default defineComponent({
  components: {
    FormActions,
    CardPadding,
    CardWrapper,
  },
  props: {
    cancel: String,
    loading: Boolean,
    hideActions: {
      type: Boolean,
      default: () => false,
    }
  }
})
</script>
