import { plugin } from '@inertiajs/inertia-vue3/src'
import http from '@/plugins/http'
import route from '@/plugins/route'
import notify from '@/plugins/notify'
import i18n from '@/plugins/i18n'

export default [
  plugin,
  http,
  route,
  notify,
  i18n,
]
