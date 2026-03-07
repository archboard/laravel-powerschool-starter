<template>
  <Installation>
    <form @submit.prevent="inertiaForm.post('/install')">
      <CardWrapper>
        <CardPadding>
          <CardHeader>
            {{ __('Installation') }}
          </CardHeader>
        </CardPadding>
        <CardPadding>
          <div class="grid grid-cols-1 md:grid-cols-6 gap-5">
            <div class="md:col-span-6">
              <InputField v-model="inertiaForm.name" :error="inertiaForm.errors.name" :label="__('Tenant name')" required />
            </div>
            <div class="md:col-span-6">
              <InputField v-model="inertiaForm.domain" :error="inertiaForm.errors.domain" :label="__('Domain')" :disabled="!isCloud" required />
            </div>
            <div v-if="isCloud" class="md:col-span-6">
              <InputField v-model="inertiaForm.custom_domain" :error="inertiaForm.errors.custom_domain" :label="__('Custom domain')" />
            </div>
            <div v-for="field in installationValues.sis_config_fields" :key="field.key" class="md:col-span-6">
              <component
                :is="fieldComponents[field.type]"
                v-model="inertiaForm.sis_config[field.key]"
                :error="inertiaForm.errors['sis_config.' + field.key]"
                :label="__(field.label)"
                :type="field.type"
                :required="field.required"
              />
            </div>
          </div>
        </CardPadding>
        <CardAction>
          <AppButton type="submit" :loading="inertiaForm.processing" full>
            {{ __('Install') }}
          </AppButton>
        </CardAction>
      </CardWrapper>
    </form>
  </Installation>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import CardHeader from '@/components/CardHeader.vue'
import AppButton from '@/components/AppButton.vue'
import CardAction from '@/components/CardAction.vue'
import CheckboxField from '@/components/forms/fields/CheckboxField.vue'
import InputField from '@/components/forms/fields/InputField.vue'
import Installation from '@/layouts/Installation.vue'

const fieldComponents = {
  text: InputField,
  url: InputField,
  checkbox: CheckboxField,
}

const props = defineProps({
  installationValues: Object,
  isCloud: Boolean,
})
const inertiaForm = useForm({
  name: props.installationValues.name,
  domain: props.installationValues.domain,
  custom_domain: props.installationValues.custom_domain,
  sis_config: { ...props.installationValues.sis_config },
})
</script>
