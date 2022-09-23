import { useForm } from '@inertiajs/inertia-vue3'

export default (fields) => {
  const form = useForm(
    fields.reduce((carry, field) => {

      return carry
    }, {})
  )

  return {
    form,
  }
}
