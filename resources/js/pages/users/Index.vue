<template>
  <Authenticated>
    <Table>
      <Thead>
        <tr>
          <Th class="pr-0 w-4"><Checkbox v-model="selectedAll" /></Th>
          <Th>{{ __('Name') }}</Th>
          <Th>{{ __('Email') }}</Th>
          <Th>{{ __('Type') }}</Th>
          <Th></Th>
        </tr>
      </Thead>
      <Tbody>
        <tr v-for="user in users.data" :key="user.id">
          <Td class="pr-0 w-4">
            <Checkbox v-model="selection" :value="user.id" @change="toggleSelection(user.id)" />
          </Td>
          <Td>{{ user.name }}</Td>
          <Td>{{ user.email }}</Td>
          <Td>{{ user.user_type_display }}</Td>
          <ActionColumn>
            <ContextMenu>
              <div class="p-1">
                <AppMenuItem v-if="can('user.view')" is="InertiaLink" :href="`/users/${user.id}`">
                  {{ __('View') }}
                </AppMenuItem>
                <AppMenuItem v-if="can('edit_permissions')" is="InertiaLink" :href="`/users/${user.id}/permissions`">
                  {{ __('Edit permissions') }}
                </AppMenuItem>
              </div>
            </ContextMenu>
          </ActionColumn>
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
import AppMenuItem from '@/components/AppMenuItem.vue'
import Checkbox from '@/components/forms/Checkbox.vue'
import useModelSelection from '@/composition/useModelSelection.js'

const props = defineProps({
  users: Object,
})
const { selection, selectedAll, toggleSelection } = useModelSelection('user')

</script>
