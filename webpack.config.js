/* global process __dirname */
const DEV = 'production' !== process.env.NODE_ENV;

/**
 * Plugins
 */
const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const OptimizeCssAssetsPlugin = require( '@soda/friendly-errors-webpack-plugin' );
const cssnano = require( 'cssnano' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const ESLintPlugin = require( 'eslint-webpack-plugin' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const StyleLintPlugin = require( 'stylelint-webpack-plugin' );
const FriendlyErrorsPlugin = require( '@soda/friendly-errors-webpack-plugin' );

// JS Directory path.
const JSDir = path.resolve( __dirname, 'src/js' );
const IMG_DIR = path.resolve( __dirname, 'src/img' );
const FONTS_DIR = path.resolve( __dirname, 'src/fonts' );
const DIST_DIR = path.resolve( __dirname, 'dist' );

const entry = {
	admin: JSDir + '/admin.js',
	public: JSDir + '/public.js'
};

const output = {
	path: DIST_DIR,
	filename: 'js/[name].js'
};

/**
 * Note: argv.mode will return 'development' or 'production'.
 */
const plugins = ( argv ) => [
	new CleanWebpackPlugin( ),

	new MiniCssExtractPlugin( {
		filename: 'css/[name].css'
	} ),
	new ESLintPlugin(  ),
	new StyleLintPlugin( {
		'extends': [
			'stylelint-config-standard-scss',
			'@wordpress/stylelint-config/scss'
		]
	} ),

	new FriendlyErrorsPlugin( {
		clearConsole: false
	} )
];

const rules = [

	{
		test: /\.js$/,
		use: {
			loader: 'babel-loader',
			options: {
				presets: [
					[
						'@babel/preset-env',
						{
							'targets': {
								'browsers': [
									'last 2 Chrome versions',
									'last 2 Firefox versions',
									'last 2 Safari versions',
									'last 2 iOS versions',
									'last 1 Android version',
									'last 1 ChromeAndroid version',
									'ie 11'
								]
							}
						}
					]
				]
			}
		},
		include: [
			JSDir,
			path.resolve( __dirname, 'node_modules/foundation-sites' )
		]
	},
	{
		test: /\.scss$/,
		exclude: /node_modules/,
		use: [
			MiniCssExtractPlugin.loader,
			'css-loader',
			'sass-loader'
		]
	},
	{
		test: /\.(png|jpg|svg|jpeg|gif|ico)$/,
		exclude: [ FONTS_DIR, /node_modules/ ],
		use: {
			loader: 'file-loader',
			options: {
				name: '[path][name].[ext]',
				publicPath: '../'
			}
		}
	},
	{
		test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
		exclude: [ IMG_DIR, /node_modules/ ],
		use: {
			loader: 'file-loader',
			options: {
				name: '[path][name].[ext]',
				publicPath: '../'
			}
		}
	}
];

const optimization = [
	new OptimizeCssAssetsPlugin( {
		cssProcessor: cssnano
	} ),

	new TerserPlugin( {
		parallel: true,
		extractComments: false,
		terserOptions: {
			sourceMap: false,
			mangle: {
				reserved: [ '__' ]
			}
		}
	} )
];

module.exports = ( env, argv ) => ( {
	entry: entry,
	output: output,
	plugins: plugins( argv ),
	devtool: 'source-map',

	module: {
		'rules': rules
	},

	optimization: {
		minimizer: optimization
	},

	externals: {
		jquery: 'jQuery'
	}
} );
