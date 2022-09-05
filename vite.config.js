import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
const path = require('path')

export default ({ mode }) => {
  const env = loadEnv(mode, process.cwd(), 'APP_')
  const domain = (new URL(env.APP_URL)).hostname

  return defineConfig({
    resolve: {
      alias: {
        axios: path.resolve('node_modules/axios/dist/axios.js'),
      },
    },
    plugins: [
      laravel({
        input: [
          'resources/js/app.js',
        ],
        refresh: true,
      }),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        }
      })
    ],
    server: {
      https: {
        key: env.APP_SSL_KEY,
        cert: env.APP_SSL_CERT,
      },
      domain,
      hmr: {
        host: domain,
      }
    }
  })
}
