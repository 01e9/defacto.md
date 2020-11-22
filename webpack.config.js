const path = require('path');
const webpack = require('webpack');

const autoprefixer = require("autoprefixer");
const sass = require("sass");

//region variables
const isProduction = process.env.NODE_ENV === "production";
const sourcePath = path.join(__dirname, "assets");
//endregion

//region Plugins
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
//endregion

//region Rules
const createRules = {
    css: ({modules}) => [
        {
            loader: MiniCssExtractPlugin.loader,
            options: {
                hmr: !isProduction,
            },
        },
        {
            loader: "css-loader",
            options: {
                modules
            }
        },
        {
            loader: "postcss-loader",
            options: {
                plugins: [
                    autoprefixer()
                ],
            }
        }
    ],
    scss: ({modules}) => createRules.css({modules}).concat([
        {
            loader: "sass-loader",
            options: {
                implementation: sass
            }
        },
    ])
};
//endregion

module.exports = {
    mode: isProduction ? "production" : "development",
    entry: {
        "build/app/scripts": path.join(sourcePath, 'app/js/index.ts'),
        "build/app/styles": path.join(sourcePath, 'app/css/index.scss'),

        "build/app/pages/mandate/scripts": path.join(sourcePath, 'app/pages/mandate/index.ts'),
        "build/app/pages/mandate/styles": path.join(sourcePath, 'app/pages/mandate/index.scss'),

        "build/admin/scripts": path.join(sourcePath, 'admin/js/index.ts'),
        "build/admin/styles": path.join(sourcePath, 'admin/css/index.scss'),
    },
    output: {
        publicPath: "/build/",
        path: path.resolve(__dirname, 'public/build/'),
        filename: `[name]${isProduction ? ".[contenthash]" : ""}.js`,
        chunkFilename: `[name]${isProduction ? ".[contenthash]" : ""}.js`,
    },
    optimization: {
        minimize: isProduction,
        minimizer: [
            new TerserJSPlugin({sourceMap: !isProduction}),
            new OptimizeCSSAssetsPlugin({})
        ],
        splitChunks: {
            cacheGroups: {
                node_modules: {
                    name: 'vendor',
                    chunks: 'all',
                    test: /node_modules/,
                    priority: 40
                }
            }
        }
    },
    devtool: isProduction ? false : 'inline-source-map',
    watchOptions: {
        aggregateTimeout: 1000,
        poll: 3000
    },
    resolve: {
        extensions: ['.js', '.ts', '.tsx', '.scss'],
        alias: {
            "~": sourcePath,
        }
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: `[name]${isProduction ? ".[contenthash]" : ""}.css`,
            chunkFilename: `[id]${isProduction ? ".[contenthash]" : ""}.css`,
            ignoreOrder: false // Ignore warnings for the invalid order of the CSS files
        }),
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV),
        }),
        new CleanWebpackPlugin({
            dry: !isProduction, // prevent fonts delete
            cleanOnceBeforeBuildPatterns: ['**/*', '!.gitkeep'],
        }),
        new ManifestPlugin(),
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery'
        })
    ],
    module: {
        rules: [
            //region TS
            {
                test: /\.tsx?$/,
                loader: 'ts-loader',
                exclude: /node_modules/
            },
            //endregion
            //region SCSS
            {
                oneOf: [
                    {
                        test: /\.scss$/,
                        use: createRules.scss({modules: false}),
                    },
                ]
            },
            //endregion
            //region CSS
            {
                oneOf: [
                    {
                        test: /\.css$/,
                        use: createRules.css({modules: false}),
                    },
                ]
            },
            //endregion
            //region Fonts
            {
                test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: `${isProduction ? ".[contenthash]" : "[name]"}.[ext]`,
                            outputPath: 'fonts/',
                            publicPath: '/build/fonts/'
                        }
                    }
                ]
            },
            //endregion
        ],
    }
};
