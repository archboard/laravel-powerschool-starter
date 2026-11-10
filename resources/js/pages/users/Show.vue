<template>
  <Authenticated>
    <template #actions>
      <DropDownButton>
        {{ __('Actions') }}
        <template #dropdown>
          <UserActionMenu :user="subjectUser" />
        </template>
      </DropDownButton>
    </template>

    <CardWrapper>
      <CardPadding>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <Dt>{{ __('Email') }}</Dt>
            <Dd>{{ subjectUser.email }}</Dd>
          </div>
          <div>
            <Dt>{{ __('User type') }}</Dt>
            <Dd>{{ subjectUser.user_type_display }}</Dd>
          </div>
        </dl>
      </CardPadding>
    </CardWrapper>
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import DropDownButton from '@/components/forms/buttons/DropDownButton.vue'
import UserActionMenu from '@/components/actions/UserActionMenu.vue'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import Dt from '@/components/Dt.vue'
import Dd from '@/components/Dd.vue'

defineProps({
  subjectUser: Object,
})
</script>
