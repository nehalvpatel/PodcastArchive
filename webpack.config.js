 var CopyWebpackPlugin = require('copy-webpack-plugin');
 var webpack = require("webpack");
 var path = require('path');

 module.exports = {
     entry: {
        App: './src/frontend/App.js'
     },
     module: {
         rules: [
             {
                 test: /\.js$/,
                 loader: 'babel',
                 exclude: /node_modules/,
                 options: {
                    plugins: ["transform-runtime"],
                    presets: [
                        ["es2015", { "modules": false }],
                        "stage-0"
                    ]
                }
             },
             {
                 test: /\.vue$/,
                 loader: 'vue',
                 options: {
                     loaders: {
                        js: 'babel'
                    }
                 }
             },
             {
                 test: /\.css$/,
                 loaders: [
                     'style',
                     'css'
                 ]
             },
             {
                 test: /\.png$/,
                 loader: 'url'
             }
         ]
     },
     plugins: [
        new webpack.ProvidePlugin({
            fetch: "imports?this=>global!exports?global.fetch!whatwg-fetch"
        })
     ],
     output: {
         path: path.join(__dirname, 'public/js'),
         filename: '[name].js',
         sourceMapFilename: '[name].map'
     }
 };