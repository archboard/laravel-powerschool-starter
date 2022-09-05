<template>
  <div>
    <MobileMenu :sidebar-open="sidebarOpen" @close="sidebarOpen = false" />

    <!-- Static sidebar for desktop -->
    <div class="hidden md:flex md:w-56 md:flex-col md:fixed md:inset-y-0">
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div class="flex-1 flex flex-col min-h-0 border-r border-gray-300 bg-white">
        <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
          <div class="flex items-center flex-shrink-0 px-4">
            <LifeplusHorizontal class="h-8 text-brand-primary" />
          </div>
          <nav class="mt-5 flex-1 px-2 bg-white space-y-1">
            <InertiaLink
              v-for="item in navigation"
              :key="item.name"
              :as="item.as || 'a'"
              :method="item.method || 'get'"
              :href="item.href"
              :class="[item.current ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900', 'group flex w-full items-center px-2 py-2 text-sm font-medium rounded-md']"
            >
              <svg xmlns="http://www.w3.org/2000/svg" :class="[item.current ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500', 'mr-3 flex-shrink-0 h-6 w-6']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" v-html="item.icon"></svg>
              {{ item.name }}
            </InertiaLink>
          </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-gray-300 p-4">
          <InertiaLink href="/settings" class="flex-shrink-0 w-full group block">
            <p class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ user.name }}</p>
            <p class="text-xs font-medium text-gray-500 group-hover:text-gray-700">{{ $t('View settings') }}</p>
          </InertiaLink>
        </div>
      </div>
    </div>
    <div class="md:pl-56 flex flex-col flex-1">
      <div class="sticky top-0 z-20 flex-shrink-0 flex h-16 bg-white shadow">
        <button type="button" class="px-4 border-r border-gray-300 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-primary md:hidden" @click="sidebarOpen = true">
          <span class="sr-only">Open sidebar</span>
          <MenuIcon class="h-6 w-6" aria-hidden="true" />
        </button>
        <div class="flex-1 px-4 flex justify-between">
          <div class="flex-1 flex">
            <form class="w-full flex md:ml-0" action="#" method="GET">
              <label for="search-field" class="sr-only">Search</label>
              <div class="relative w-full text-gray-400 focus-within:text-gray-600">
                <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                  <SearchIcon class="h-5 w-5" aria-hidden="true" />
                </div>
                <input id="search-field" class="block w-full h-full pl-8 pr-3 py-2 border-transparent text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-0 focus:border-transparent sm:text-sm" placeholder="Search" type="search" name="search" />
              </div>
            </form>
          </div>
        </div>
      </div>

      <main class="flex-1">
        <div class="py-6">
          <div class="px-4 sm:px-6 md:px-8 mb-6 flex items-start justify-between">
            <div>
              <h1 class="font-bold text-3xl md:text-4xl">{{ title }}</h1>
              <slot name="below-title" />
            </div>
            <div>
              <slot name="actions" />
            </div>
          </div>
          <div class="px-4 sm:px-6 md:px-8">
            <slot />
          </div>
        </div>
      </main>
    </div>

    <Modal />
    <Notifications />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Modal } from 'momentum-modal'
import MobileMenu from '../MobileMenu.vue'
import { SearchIcon } from '@heroicons/vue/24/solid'
import { MenuIcon } from '@heroicons/vue/24/outline'
import LifeplusHorizontal from '@/components/logos/lifeplus-horizontal.vue'
import { usePage } from '@inertiajs/inertia-vue3'
import usesPageTitle from '@/composition/usesPageTitle.js'
import usesUser from '@/composition/usesUser.js'
import Notifications from '@/components/Notifications.vue'

const page = usePage()
const navigation = page.props.value.navigation
const user = usesUser()
const title = usesPageTitle()
let sidebarOpen = ref(false)
</script>
