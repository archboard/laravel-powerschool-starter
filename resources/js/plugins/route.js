export default {
  install (app) {
    const route = (name = '', params) => {
      if (name.startsWith('http')) {
        return name
      }

      try {
        return window.route(name, params)
      } catch (err) {
        console.warn(err)
        return '#'
      }
    }

    app.config.globalProperties.$route = route
    app.provide('$route', route)
  }
}
