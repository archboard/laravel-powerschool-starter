<template>
  <Authenticated>
    <form @submit.prevent="submit" data-cy="form">
      <Fieldset>
        <InputWrap :error="form.errors.first_name">
          <Label for="first_name">{{ __('First Name') }}</Label>
          <Input v-model="form.first_name" type="text" id="first_name" data-cy="first_name" required />
        </InputWrap>
        <InputWrap :error="form.errors.last_name">
          <Label for="last_name">{{ __('Last Name') }}</Label>
          <Input v-model="form.last_name" type="text" id="last_name" data-cy="last_name" required />
        </InputWrap>
        <InputWrap :error="form.errors.email">
          <Label for="email">{{ __('Email') }}</Label>
          <Input v-model="form.email" type="email" id="email" data-cy="email" required />
        </InputWrap>
        <InputWrap :error="form.errors.password">
          <Label for="password">{{ __('Password') }}</Label>
          <Input v-model="form.password" type="password" id="password" data-cy="password" />
        </InputWrap>
        <InputWrap :error="form.errors.password_confirmation">
          <Label for="password_confirmation">{{ __('Confirm Password') }}</Label>
          <Input v-model="form.password_confirmation" type="password" id="password_confirmation" data-cy="password_confirmation" />
        </InputWrap>
        <InputWrap>
          <Button type="submit" :loading="form.processing">
            {{ __('Save') }}
          </Button>
        </InputWrap>
      </Fieldset>
    </form>
  </Authenticated>
</template>

<script>
import { defineComponent, ref, inject } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'
import Authenticated from '@/layouts/Authenticated.vue'
import pick from 'just-pick'
import Fieldset from '@/components/forms/Fieldset.vue'
import InputWrap from '@/components/forms/InputWrap.vue'
import Label from '@/components/forms/Label'
import Input from '@/components/forms/Input'
import Button from '@/components/Button'

export default defineComponent({
  components: {
    Button,
    Input,
    Label,
    InputWrap,
    Fieldset,
    Authenticated
  },

  props: {
    user: Object,
  },

  setup ({ user }) {
    const $route = inject('$route')
    const form = useForm({
      ...pick(user, ['first_name', 'last_name', 'email']),
      password: '',
      password_confirmation: '',
    })
    const submit = () => {
      form.value.post($route('settings.personal'), {
        onFinish () {
          form.value.reset('password', 'password_confirmation')
        }
      })
    }

    return {
      form,
      submit,
    }
  },
})
</script>
