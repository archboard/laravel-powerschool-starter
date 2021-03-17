const path = require('path')
const fs = require('fs')

module.exports = {
  output: {
    chunkFilename: 'js/[name].js?id=[chunkhash]'
  },
  plugins: [],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
  devServer: {
    public: `${process.env.APP_URL}:${process.env.APP_PORT}/`,
    https: {
      key: fs.readFileSync(process.env.APP_SSL_KEY),
      cert: fs.readFileSync(process.env.APP_SSL_CERT),
    },
    injectHot: (config) => {
      config.output.publicPath = `${process.env.APP_URL}:${process.env.APP_PORT}/`

      return true
    },
    headers: {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, PATCH, OPTIONS",
      "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization"
    },
  }
}
