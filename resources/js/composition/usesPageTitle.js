import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'

export default () => {
  const page = usePage()
  const title = computed(() => page.props.value.title)

  onMounted(() => {
    document.title = title.value
      ? `${title.value} | LifePlus HRIS`
      : 'LifePlus HRIS'
  })

  return title
}
