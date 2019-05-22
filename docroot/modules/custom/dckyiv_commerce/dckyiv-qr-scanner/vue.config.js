// vue.config.js
module.exports = {
    publicPath: '/modules/custom/dckyiv_commerce/dckyiv-qr-scanner/dist',
    assetsDir: 'assets',
    indexPath: 'index.html',
    devServer: {
        disableHostCheck: true,
        //https: true,
        public: 'dckyiv.docksal:8080',
        proxy: {
            '^/jsonapi': {
                target: 'http://dckyiv.docksal/',
            },
            '^/session': {
                target: 'http://dckyiv.docksal/',
            },


        }
    }
}
