const merge = require('webpack-merge');
const path = require('path');
const webpack = require('webpack');

//console.log('ENV: ', process.env.NODE_ENV, ': ', process.env.USER, ': ', process.env.HOME);

module.exports = {
	output: {
		library: 'x1', // window.Zapper
		filename: 'x1-[name].js'
	},
	optimization: {
		//splitChunks: {
		//    chunks: 'all',
		//},
		splitChunks: {
			cacheGroups: {
				default: false,
				commons: {
					test: /[\\/]node_modules[\\/]/,
					name: 'vendor',
					chunks: 'all',
					minChunks: 2
				}
			}
		}
	},
	resolve: {
		extensions: ['*', '.js', '.jsx', '.css', '.scss'],
		alias: {
			root: path.resolve(__dirname, 'client/'),
			icons: path.resolve(__dirname, 'client/assets/images/icons/'),
		}
	},
	module: {
		rules: [
			{
				test: [/\.js$/, /\.jsx?$/],
				exclude: /node_modules/,
				//use: ['babel-loader', 'eslint-loader'], // eslint-loader will log to chrome console
				use: ['babel-loader']
			},
			{
				test: /\.(png|svg|jpg|gif)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]'
						}
					}
				]
			}
		]
	}
};
