<template>
  <Authenticated>
    <div class="space-y-8">
      <form @submit.prevent="saveTenantSettings">
        <CardWrapper>
          <CardPadding>
            <CardHeader>{{ __('Tenant Settings') }}</CardHeader>
          </CardPadding>
          <CardPadding>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-5">
              <div class="md:col-span-6">
                <InputField v-model="tenantForm.name" :error="tenantForm.errors.name" :label="__('Tenant name')" required />
              </div>
              <div class="md:col-span-3">
                <InputField v-model="tenantForm.domain" :error="tenantForm.errors.domain" :label="__('Domain')" :disabled="!editable" required />
              </div>
              <div class="md:col-span-3">
                <SelectField v-model="tenantForm.sis_provider" :error="tenantForm.errors.sis_provider" :options="sisOptions" :label="__('SIS data provider')" required />
              </div>
              <div class="md:col-span-6">
                <CheckboxField v-model="tenantForm.allow_password_auth" :error="tenantForm.errors.allow_password_auth" :label="__('Allow password authentication')" :help-text="__('Allow users to login with their email and password.')" />
              </div>
              <div class="md:col-span-6">
                <CheckboxField v-model="tenantForm.allow_oidc_login" :error="tenantForm.errors.allow_oidc_login" :label="__('Allow OpenID Connect login')" :help-text="__('Allow users to login with OpenID Connect with the SIS.')" />
              </div>
            </div>
          </CardPadding>
          <CardAction>
            <AppButton type="submit" :loading="tenantForm.processing" />
          </CardAction>
        </CardWrapper>
      </form>

      <form v-if="smtpSettings" @submit.prevent="saveSmtpSettings">
        <CardWrapper>
          <CardPadding>
            <CardHeader>{{ __('SMTP Settings') }}</CardHeader>
          </CardPadding>
          <CardPadding>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-5">
              <div class="md:col-span-3">
                <InputField v-model="smtpForm.host" :error="smtpForm.errors.host" :label="__('Host')" placeholder="127.0.0.1" :help-text="__('IP address or domain name of the SMTP server.')" />
              </div>
              <div class="md:col-span-3">
                <NumberField v-model="smtpForm.port" :error="smtpForm.errors.port" :label="__('Port')" placeholder="587" />
              </div>
              <div class="md:col-span-3">
                <InputField v-model="smtpForm.username" :error="smtpForm.errors.username" :label="__('Username')" />
              </div>
              <div class="md:col-span-3">
                <InputField v-model="smtpForm.password" :error="smtpForm.errors.password" :label="__('Password')" />
              </div>
              <div class="md:col-span-2">
                <InputField v-model="smtpForm.from_name" :error="smtpForm.errors.from_name" :label="__('From name')" placeholder="App Name" />
              </div>
              <div class="md:col-span-2">
                <EmailField v-model="smtpForm.from_address" :error="smtpForm.errors.from_address" :label="__('From address')" />
              </div>
              <div class="md:col-span-2">
                <SelectField v-model="smtpForm.encryption" :error="smtpForm.errors.encryption" :options="[{ label: 'TLS', value: 'tls' }, { label: 'SSL', value: 'ssl' }]" :label="__('Encryption')" />
              </div>
            </div>
          </CardPadding>
          <CardAction>
            <AppButton @click.prevent="sendSmtpTest" type="button" color="white" :loading="uiState === 'sending'">{{ __('Send test') }}</AppButton>
            <AppButton type="submit" :loading="smtpForm.processing" />
          </CardAction>
        </CardWrapper>
      </form>

      <form @submit.prevent="saveSchools">
        <CardWrapper>
          <CardPadding>
            <CardHeader>{{ __('Active schools') }}</CardHeader>
            <HelpText>{{ __('Select the schools that are active in the app.') }}</HelpText>
          </CardPadding>
          <CardPadding>
            <SimpleAlert v-if="!editable" level="warning" class="mb-4">
              {{ __('Schools are managed in your Archboard account.') }}
            </SimpleAlert>
            <SimpleAlert v-if="schoolForm.errors.schools" level="error" class="mb-4">
              {{ schoolForm.errors.schools }}
            </SimpleAlert>

            <div class="flex items-center space-x-2 mb-2 text-sm">
              <AppLink is="button" @click.prevent="select(true)">{{ __('Select all') }}</AppLink>
              <AppLink is="button" @click.prevent="select(false)">{{ __('Select none') }}</AppLink>
            </div>

            <template v-for="school in schools" :key="school.id">
              <div>
                <AppCheckbox v-model="schoolForm.schools" :value="school.id" :disabled="!editable">
                  {{ school.name }}
                </AppCheckbox>
              </div>
            </template>
          </CardPadding>
          <CardAction>
            <AppButton type="submit" :loading="schoolForm.processing" :disabled="!editable">
              {{ __('Save') }}
            </AppButton>
          </CardAction>
        </CardWrapper>
      </form>
    </div>
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import CardHeader from '@/components/CardHeader.vue'
import { router, useForm } from '@inertiajs/vue3'
import CardAction from '@/components/CardAction.vue'
import AppButton from '@/components/AppButton.vue'
import HelpText from '@/components/forms/HelpText.vue'
import AppCheckbox from '@/components/forms/AppCheckbox.vue'
import AppLink from '@/components/AppLink.vue'
import SimpleAlert from '@/components/alerts/SimpleAlert.vue'
import InputField from '@/components/forms/fields/InputField.vue'
import SelectField from '@/components/forms/fields/SelectField.vue'
import CheckboxField from '@/components/forms/fields/CheckboxField.vue'
import NumberField from '@/components/forms/fields/NumberField.vue'
import EmailField from '@/components/forms/fields/EmailField.vue'
import { ref } from 'vue'

const props = defineProps({
  tenantSettings: Object,
  smtpSettings: Object,
  sisOptions: Array,
  schools: Array,
  editable: Boolean,
})
const uiState = ref()

const tenantForm = useForm({
  name: props.tenantSettings.name,
  domain: props.tenantSettings.domain,
  sis_provider: props.tenantSettings.sis_provider,
  allow_password_auth: props.tenantSettings.allow_password_auth,
  allow_oidc_login: props.tenantSettings.allow_oidc_login,
})
const saveTenantSettings = () => {
  tenantForm.put('/settings/tenant', { preserveScroll: true })
}

const smtpForm = props.smtpSettings ? useForm({
  host: props.smtpSettings.host,
  port: props.smtpSettings.port,
  username: props.smtpSettings.username,
  password: props.smtpSettings.password,
  from_name: props.smtpSettings.from_name,
  from_address: props.smtpSettings.from_address,
  encryption: props.smtpSettings.encryption,
}) : null
const saveSmtpSettings = () => {
  smtpForm.put('/settings/tenant/smtp', { preserveScroll: true })
}

const schoolForm = useForm({
  schools: props.schools.reduce((carry, school) => {
    if (school.active) {
      carry.push(school.id)
    }

    return carry
  }, []),
})
const select = all => {
  if (props.editable) {
    schoolForm.schools = all
      ? props.schools.map(school => school.id)
      : []
  }
}
const saveSchools = () => {
  if (props.editable) {
    schoolForm.put('/settings/tenant/schools', {
      preserveScroll: true,
    })
  }
}
const sendSmtpTest = () => {
  uiState.value = 'sending'

  router.post('/settings/tenant/smtp/test', null, {
    preserveScroll: true,
    onSuccess: () => {
      uiState.value = null
    }
  })
}
</script>
