<template>
  <Authenticated>
    <Filters
      v-model="filters"
      v-model:search="search"
      :available-filters="availableFilters"
      @update="updateResults()"
      :loading="updatingResults"
      :search-placeholder="__('Search by name or email…')"
    />

    <SelectionManager :selection="selection" :select-none="selectNone" />

    <Table no-top-radius>
      <Thead>
        <tr>
          <Th class="pe-0 w-4"><Checkbox @change="selectedAll = !selectedAll" :checked="selectedAll" /></Th>
          <Th>{{ __('Number') }}</Th>
          <Th>{{ __('Grade') }}</Th>
          <Th>{{ __('Name') }}</Th>
          <Th v-if="can('student.update')"></Th>
        </tr>
      </Thead>
      <Tbody>
        <tr v-for="student in students.data" :key="student.id">
          <Td class="pe-0 w-4">
            <Checkbox v-model="selection" :value="student.id" @change="toggleSelection(student.id)" />
          </Td>
          <Td>{{ student.student_number }}</Td>
          <Td>{{ student.grade_level }}</Td>
          <Td>
            <div class="flex items-center space-x-2">
              <AppLink :href="`/students/${student.id}`">{{ student.name }}</AppLink>
            </div>
          </Td>
          <ActionColumn v-if="can('student.update')">
            <ContextMenu>
              <StudentActionMenu :student="student" />
            </ContextMenu>
          </ActionColumn>
        </tr>
      </Tbody>
    </Table>

    <Pagination :links="students.links" :meta="students.meta" />
  </Authenticated>
</template>

<script setup>
import Authenticated from '@/layouts/Authenticated.vue'
import { ActionColumn, Table, Tbody, Th, Thead } from '@/components/tables/index.js'
import Td from '@/components/tables/Td.vue'
import Pagination from '@/components/tables/Pagination.vue'
import ContextMenu from '@/components/ContextMenu.vue'
import Checkbox from '@/components/forms/Checkbox.vue'
import useModelSelection from '@/composition/useModelSelection.js'
import Filters from '@/components/tables/Filters.vue'
import useFilters from '@/composition/useFilters.js'
import StudentActionMenu from '@/components/actions/StudentActionMenu.vue'
import SelectionManager from '@/components/tables/SelectionManager.vue'
import AppLink from '@/components/AppLink.vue'

defineProps({
  students: Object,
  availableFilters: Array,
  currentFilters: [Array, Object],
})
const { selection, selectedAll, selectNone, toggleSelection } = useModelSelection('student')
const { filters, search, updateResults, updatingResults } = useFilters()
</script>
