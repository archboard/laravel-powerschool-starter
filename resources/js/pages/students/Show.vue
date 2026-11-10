<template>
  <Authenticated>
    <template #actions>
      <DropDownButton v-if="can('student.update')">
        {{ __('Actions') }}
        <template #dropdown>
          <StudentActionMenu :student="student" ignore-view />
        </template>
      </DropDownButton>
    </template>

    <CardWrapper>
      <CardPadding>
        <CardHeader>{{ __('Enrollment') }}</CardHeader>
      </CardPadding>
      <Table no-top-radius>
        <Thead>
          <tr>
            <Th>{{ __('Course') }}</Th>
            <Th>{{ __('Section') }}</Th>
            <Th>{{ __('Teacher') }}</Th>
            <Th></Th>
          </tr>
        </Thead>
        <Tbody>
          <tr v-if="student.sections.length === 0">
            <Td colspan="4" class="text-center">
              {{ __('No records') }}
            </Td>
          </tr>
          <tr v-for="section in student.sections" :key="section.id">
            <Td>
              <div class="flex items-center gap-2">
                <AppLink :href="`/courses/${section.course.id}`">{{ section.course.name }}</AppLink>
              </div>
            </Td>
            <Td>
              <div class="flex items-center gap-2">
                <AppLink :href="`/sections/${section.id}`">{{ section.section_number }}</AppLink>
              </div>
            </Td>
            <Td>
              <div class="flex items-center gap-2">
                <span>{{ section.teacher_display }}</span>
              </div>
            </Td>
            <ActionColumn>
              <div class="flex items-center gap-3 justify-end">
                <ContextMenu>
                  <SectionActionMenu :section="section" />
                </ContextMenu>
              </div>
            </ActionColumn>
          </tr>
        </Tbody>
      </Table>
    </CardWrapper>
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import DropDownButton from '@/components/forms/buttons/DropDownButton.vue'
import StudentActionMenu from '@/components/actions/StudentActionMenu.vue'
import CardWrapper from '@/components/CardWrapper.vue'
import CardPadding from '@/components/CardPadding.vue'
import CardHeader from '@/components/CardHeader.vue'
import { Table, Thead, Th, Tbody, Td, ActionColumn } from '@/components/tables/index.js'
import ContextMenu from '@/components/ContextMenu.vue'
import SectionActionMenu from '@/components/actions/SectionActionMenu.vue'
import AppLink from '@/components/AppLink.vue'

defineProps({
  student: Object,
})
</script>
