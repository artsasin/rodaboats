const path = require('path')
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require("extract-text-webpack-plugin")

const NODE_ENV = process.env.NODE_ENV;

const plugins =  (NODE_ENV === 'production') ? [new ExtractTextPlugin("[name].css"), new UglifyJSPlugin()] : [new ExtractTextPlugin("[name].css")]

const module_settings = {
    rules: [
        {
            test: /\.js$/,
            exclude: /(node_modules|bower_components)/,
            use: {
                loader: 'babel-loader',
                options: {
                    presets: ['env']
                }
            }
        },
        {
            test: /\.vue$/,
            loader: 'vue-loader',
            options: {
                extractCSS: true,
                loaders: {
                    js: [
                        {loader: 'babel-loader', options: {presets: ['env']}}
                    ]
                }
            }
        },
        {
            test: /\.css$/,
            use: ExtractTextPlugin.extract({
                fallback: "style-loader",
                use: "css-loader"
            })
        }
    ]
};

const resolve = {
    alias: {
        vue: 'vue/dist/vue.js'
    },
    extensions: ['*', '.js', '.vue', '.json']
};

const customerConfig = {
    entry: {
        'customer-list': './app/Resources/vue/customer/customer-list.js'
    },
    output: {
        path: path.resolve(__dirname, './public_html/vue/customer'),
        publicPath: '/vue/customer/',
        filename: '[name].js'
    },
    module: module_settings,
    resolve: resolve,
    plugins: plugins
}

const orderConfig = {
    entry: {
        'order-create': './app/Resources/vue/order/create/app.js'
    },
    output: {
        path: path.resolve(__dirname, './public_html/vue/order'),
        publicPath: '/vue/order/',
        filename: '[name].js'
    },
    module: module_settings,
    resolve: resolve,
    plugins: plugins
}

module.exports = [customerConfig, orderConfig];