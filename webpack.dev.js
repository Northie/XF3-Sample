const merge = require('webpack-merge');
const webpack = require('webpack');
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const common = require('./webpack.common.js');

// ---------------------------------------------------

const devHtml = new HtmlWebpackPlugin({
	title: 'Checkout',
	template: __dirname + '/client/app/dev-server/index.html',
	chunks: ['vendor', 'app'],
	filename: 'index.html',
	inject: 'body',
});

// ---------------------------------------------------

const browserSync = new BrowserSyncPlugin({
	host: 'localhost',
	port: 3000,
	proxy: 'http://localhost:8080/',
	open: false,
	reload: false,
	//injectCss: true,
	//files: ['app/**/*.php', 'app/**/*.phtml', 'html/assets/react/*.css']
});

module.exports = merge(common, {
	mode: 'development',
	entry: {
		app: './client/app/index.js',
	},
	devtool: 'inline-source-map',
	//watch: true,
	devServer: {
		hot: true,
		inline: true,
		//publicPath: '/',
		contentBase: path.join(__dirname, 'client/app'), // allows multiple webpack dev server routes
		publicPath: '/',
		historyApiFallback: true,
		//port: 8080,
	},
	module: {
		rules: [
			/*
	         * Load inline styles
	         * */
			{
				test: [/\.scss$/],
				exclude: /node_modules/,
				use: ['style-loader', 'css-loader', 'sass-loader'],
			},
		],
	},
	//module: {
	//	rules: [
	//		{
	//			test: [/\.scss$/],
	//			exclude: /node_modules/,
	//			use: ['style-loader', MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader']
	//		}
	//	]
	//},
	plugins: [
		devHtml,
		new webpack.HotModuleReplacementPlugin(),
		new webpack.NamedModulesPlugin(),
		//browserSync, // localhost works BUT http://192.168.1.14:3001 does NOT
		//new MiniCssExtractPlugin({
		//	filename: 'main.css'
		//})
	],
});
