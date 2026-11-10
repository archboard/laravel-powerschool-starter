<template>
  <div v-if="!ignoreView" class="p-1">
    <AppMenuItem :href="`/sections/${section.id}`">{{ __('View') }}</AppMenuItem>
  </div>
  <div v-if="can('section.update')" class="p-1">
    <AppMenuItem as="button" method="post" :href="`/sync/${section.model_alias}/${section.id}`">
      <span class="flex items-center gap-2">
        <ArrowPathIcon class="h-5 w-5" />
        <span>{{ __('Sync from SIS') }}</span>
      </span>
    </AppMenuItem>
  </div>
</template>

<script setup>
import AppMenuItem from '@/components/AppMenuItem.vue'
import { ArrowPathIcon } from '@heroicons/vue/24/outline/index.js'

defineProps({
  section: Object,
  ignoreView: {
    type: Boolean,
    default: false,
  }
})
</script>
