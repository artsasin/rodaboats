var path = require('path')
// var UglifyJSPlugin = require('uglifyjs-webpack-plugin');
var ExtractTextPlugin = require("extract-text-webpack-plugin")

module.exports = {
    entry: './app/Resources/vue/main.js',
    output: {
        path: path.resolve(__dirname, './public_html/js'),
        publicPath: '/js/',
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
                loader: 'css-loader'
            }
        ]
    },
    plugins: [
        // new UglifyJSPlugin()
        new ExtractTextPlugin("[name].css")
    ]
}