import axios from 'axios'
import get from 'lodash/get'
import store from '@/stores/notifications'

const flashMessage = response => {
  const flash = get(response, 'data.props.flash')

  if (flash) {
    Object.keys(flash).forEach(level => {
      const text = flash[level]

      if (text) {
        console.log(`${level}: ${flash[level]}`)
        store.addNotification({ level, text }, 4000)
      }
    })
  }
}

axios.interceptors.response.use(response => {
  flashMessage(response)

  return response
}, async err => {
  const status = get(err, 'response.status')

  if (status === 419) {
    const config = err.response.config
    const { data } = await axios.get('/csrf-token')
    config.headers['X-XSRF-TOKEN'] = data.token

    return axios(config)
  }

  if (err.response) {
    store.addNotification({
      level: 'error',
      text: get(err, 'response.data.message', err.message),
    })
  }

  return Promise.reject(err)
})

export default {
  install (app) {
    app.config.globalProperties.$http = axios
    app.provide('$http', axios)
  }
}
