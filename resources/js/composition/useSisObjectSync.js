import { ref, toValue } from 'vue'
import { router } from '@inertiajs/vue3'

export default function useSync (type, object) {
  const syncing = ref(false)
  const sync = () => {
    syncing.value = true

    router.post(`/sync/${type}/${toValue(object)?.id}`, null, {
      preserveScroll: true,
      onFinish: () => {
        syncing.value = false
      },
    })
  }

  return {
    syncing,
    sync,
  }
}
