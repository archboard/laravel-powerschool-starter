<template>
  <Authenticated>
    <template #actions>
      <DropDownButton>
        {{ __('Actions') }}
        <template #dropdown>
          <SectionActionMenu :section="section" ignore-view />
        </template>
      </DropDownButton>
    </template>
    <template #after-title>
      <div class="flex items-center gap-2">
        <span>{{ section.teacher_display }}</span>
      </div>
    </template>

    <StudentsTable :students="section.students" />
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import SectionActionMenu from '@/components/actions/SectionActionMenu.vue'
import DropDownButton from '@/components/forms/buttons/DropDownButton.vue'
import StudentsTable from '@/components/tables/StudentsTable.vue'

defineProps({
  section: Object,
})
</script>
