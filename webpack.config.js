const path = require('path')
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');
const ExtractTextPlugin = require("extract-text-webpack-plugin")

const NODE_ENV = process.env.NODE_ENV;

const plugins =  (NODE_ENV === 'production') ? [new ExtractTextPlugin("[name].css"), new UglifyJSPlugin()] : [new ExtractTextPlugin("[name].css")]

const customerConfig = {
    entry: {
        'customer-list': './app/Resources/vue/customer/customer-list.js'
    },
    output: {
        path: path.resolve(__dirname, './public_html/vue/customer'),
        publicPath: '/vue/customer/',
        filename: '[name].js'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    extractCSS: true
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
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.js'
        },
        extensions: ['*', '.js', '.vue', '.json']
    },
    plugins: plugins
}

module.exports = [customerConfig];