<template>
  <Authenticated>
    <form @submit.prevent="submit" data-cy="form">
      <Fieldset>
        <InputWrap :error="form.errors.name">
          <Label for="name">{{ __('Tenant Name') }}</Label>
          <Input v-model="form.name" type="text" id="name" data-cy="name" required />
        </InputWrap>
        <InputWrap :error="form.errors.ps_url">
          <Label for="ps_url">{{ __('PowerSchool URL') }}</Label>
          <Input v-model="form.ps_url" type="url" id="ps_url" data-cy="ps_url" />
        </InputWrap>
        <InputWrap :error="form.errors.ps_client_id">
          <Label for="ps_client_id">{{ __('PowerSchool Client ID') }}</Label>
          <Input v-model="form.ps_client_id" type="text" id="ps_client_id" data-cy="ps_client_id" required />
        </InputWrap>
        <InputWrap :error="form.errors.ps_secret">
          <Label for="ps_secret">{{ __('PowerSchool Client Secret') }}</Label>
          <Input v-model="form.ps_secret" type="text" id="ps_secret" data-cy="ps_secret" />
        </InputWrap>
        <InputWrap :error="form.errors.password_confirmation">
          <Label>
            <Checkbox name="allow_password_auth" v-model:checked="form.allow_password_auth" data-cy="allow_password_auth" />
            <CheckboxText>{{ __('Allow password authentication') }}</CheckboxText>
          </Label>
        </InputWrap>
        <InputWrap>
          <Button type="submit" :loading="form.processing">
            {{ __('Save') }}
          </Button>
        </InputWrap>
        <pre>{{ tenant }}</pre>
      </Fieldset>
    </form>
  </Authenticated>
</template>

<script>
import { defineComponent, ref, inject } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Authenticated from '../../layouts/Authenticated'
import Fieldset from '../../components/forms/Fieldset'
import InputWrap from '../../components/forms/InputWrap'
import Label from '../../components/forms/Label'
import Input from '../../components/forms/Input'
import Button from '../../components/Button'
import Checkbox from '../../components/forms/Checkbox'
import CheckboxText from '../../components/forms/CheckboxText'

export default defineComponent({
  components: {
    CheckboxText,
    Checkbox,
    Button,
    Input,
    Label,
    InputWrap,
    Fieldset,
    Authenticated
  },

  props: {
    tenant: Object,
  },

  setup ({ tenant }) {
    const $route = inject('$route')
    const form = useForm({
      ...tenant
    })
    const submit = () => {
      form.value.post($route('settings.tenant'))
    }

    return {
      form,
      submit,
    }
  },
})
</script>
