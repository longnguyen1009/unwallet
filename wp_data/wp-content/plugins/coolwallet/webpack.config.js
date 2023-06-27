const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const webpack = require('webpack');

module.exports = {
    ...defaultConfig,
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
        ],
    },
    optimization: {
		...defaultConfig.optimization,
	},
    plugins: [
        ...defaultConfig.plugins,
        new webpack.ProvidePlugin({
            Buffer: ['buffer', 'Buffer'],
        }),
    ],
    resolve: {
		...defaultConfig.resolve,
        fallback: {
            crypto: require.resolve('crypto-browserify'),
            stream: require.resolve('stream'),
        },
	},
};