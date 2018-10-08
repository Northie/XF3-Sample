const merge = require('webpack-merge');

const common = require('./webpack.common.js');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

module.exports = merge(common, {
	mode: 'development',
	devtool: 'source-map',
	entry: {
		app: './client/app/index.js',
	},
	output: {
		publicPath: '/html/assets/react/',
		path: __dirname + '/html/assets/react/'
	},
	module: {
		rules: [
			{
				test: [/\.scss$/],
				exclude: /node_modules/,
				use: ['style-loader', MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader', 'sass-loader']
			}
		]
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: 'main.css'
		})
		//new BundleAnalyzerPlugin()
	]
});
