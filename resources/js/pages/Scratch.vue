<template>
  <div class="py-12 max-w-lg mx-auto w-full">
    <h2 class="font-bold mb-8">Menu</h2>
    <Menu>
      <MenuButton>Drop down</MenuButton>
      <MenuItems>
        <MenuItem v-slot="{ active }">
          <a :class='{ "bg-primary-500": active }' href="#">
            Account settings
          </a>
        </MenuItem>
        <MenuItem v-slot="{ active }">
          <a :class='{ "bg-primary-500": active }' href="#">
            Documentation
          </a>
        </MenuItem>
        <MenuItem disabled>
          <span class="opacity-75">Invite a friend (coming soon!)</span>
        </MenuItem>
      </MenuItems>
    </Menu>
  </div>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'

export default defineComponent({
  components: {
    Menu,
    MenuButton,
    MenuItems,
    MenuItem,
  },

  setup () {

  }
})
</script>
