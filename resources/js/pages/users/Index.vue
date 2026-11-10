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
          <Th>{{ __('Name') }}</Th>
          <Th>{{ __('Email') }}</Th>
          <Th>{{ __('Type') }}</Th>
          <Th></Th>
        </tr>
      </Thead>
      <Tbody>
        <tr v-for="user in users.data" :key="user.id">
          <Td class="pe-0 w-4">
            <Checkbox v-model="selection" :value="user.id" @change="toggleSelection(user.id)" />
          </Td>
          <Td>
            <div class="flex items-center space-x-2">
              <AppLink :href="`/users/${user.id}`" class="whitespace-nowrap">{{ user.name }}</AppLink>
            </div>
          </Td>
          <Td>{{ user.email }}</Td>
          <Td>{{ user.user_type_display }}</Td>
          <ActionColumn>
            <ContextMenu>
              <UserActionMenu :user="user" />
            </ContextMenu>
          </ActionColumn>
        </tr>

        <tr v-if="users.data.length === 0">
          <Td class="text-center" colspan="5">
            {{ __('No results found.') }}
          </Td>
        </tr>
      </Tbody>
    </Table>

    <Pagination :links="users.links" :meta="users.meta" />
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
import SelectionManager from '@/components/tables/SelectionManager.vue'
import UserActionMenu from '@/components/actions/UserActionMenu.vue'
import AppLink from '@/components/AppLink.vue'

defineProps({
  users: Object,
  availableFilters: Array,
  currentFilters: [Array, Object],
  model_alias: String,
})
const { selection, selectedAll, toggleSelection, selectNone } = useModelSelection('user')
const { filters, search, updateResults, updatingResults } = useFilters()
</script>
