<template>
  <Authenticated>
    <template #actions>
      <ActionWrapper>
        <SyncSchoolItemButton item="courses" />
      </ActionWrapper>
    </template>

    <Filters
      v-model="filters"
      v-model:search="search"
      :available-filters="availableFilters"
      @update="updateResults()"
      :loading="updatingResults"
      :search-placeholder="__('Search by course name…')"
    />

    <SelectionManager :selection="selection" :select-none="selectNone" />

    <Table no-top-radius>
      <Thead>
        <tr>
          <Th class="pe-0 w-4"><Checkbox @change="selectedAll = !selectedAll" :checked="selectedAll" /></Th>
          <Th>{{ __('Name') }}</Th>
          <Th>{{ __('Number') }}</Th>
          <Th>{{ __('Sections') }}</Th>
          <Th></Th>
        </tr>
      </Thead>
      <Tbody>
        <tr v-if="courses.data.length === 0">
          <Td class="text-center" colspan="5">
            <div class="py-6 space-y-3">
              <p>{{ __('No courses have been synced yet.') }}</p>
              <SyncSchoolItemButton item="courses" />
            </div>
          </Td>
        </tr>
        <tr v-for="course in courses.data" :key="course.id">
          <Td class="pe-0 w-4">
            <Checkbox v-model="selection" :value="course.id" @change="toggleSelection(course.id)" />
          </Td>
          <Td>
            <div class="flex items-center space-x-2">
              <AppLink :href="`/courses/${course.id}`">{{ course.name }}</AppLink>
            </div>
          </Td>
          <Td>{{ course.course_number }}</Td>
          <Td>{{ course.sections_count }}</Td>
          <ActionColumn>
            <ContextMenu>
              <CourseActionMenu :course="course" />
            </ContextMenu>
          </ActionColumn>
        </tr>
      </Tbody>
    </Table>

    <Pagination :links="courses.links" :meta="courses.meta" />
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import ActionWrapper from '@/components/ActionWrapper.vue'
import SyncSchoolItemButton from '@/components/SyncSchoolItemButton.vue'
import { ActionColumn, Table, Tbody, Th, Thead } from '@/components/tables/index.js'
import Td from '@/components/tables/Td.vue'
import Pagination from '@/components/tables/Pagination.vue'
import AppLink from '@/components/AppLink.vue'
import Filters from '@/components/tables/Filters.vue'
import SelectionManager from '@/components/tables/SelectionManager.vue'
import useModelSelection from '@/composition/useModelSelection.js'
import useFilters from '@/composition/useFilters.js'
import Checkbox from '@/components/forms/Checkbox.vue'
import ContextMenu from '@/components/ContextMenu.vue'
import CourseActionMenu from '@/components/actions/CourseActionMenu.vue'

const props = defineProps({
  courses: Object,
  availableFilters: {
    type: Array,
    default: () => [],
  },
  model_alias: String,
})
const { selection, selectedAll, selectNone, toggleSelection } = useModelSelection(props.model_alias)
const { filters, search, updateResults, updatingResults } = useFilters()
</script>
