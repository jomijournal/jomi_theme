'use strict';
module.exports = function(grunt) {
	// Load all tasks
	require('load-grunt-tasks')(grunt);
	// Show elapsed time
	require('time-grunt')(grunt);

	var jsFileList = [
		'assets/vendor/bootstrap/js/transition.js',
		'assets/vendor/bootstrap/js/alert.js',
		'assets/vendor/bootstrap/js/button.js',
		'assets/vendor/bootstrap/js/carousel.js',
		'assets/vendor/bootstrap/js/collapse.js',
		'assets/vendor/bootstrap/js/dropdown.js',
		'assets/vendor/bootstrap/js/modal.js',
		'assets/vendor/bootstrap/js/tooltip.js',
		'assets/vendor/bootstrap/js/popover.js',
		'assets/vendor/bootstrap/js/scrollspy.js',
		'assets/vendor/bootstrap/js/tab.js',
		'assets/vendor/bootstrap/js/affix.js',
		'assets/js/plugins/*.js',
		'assets/js/_*.js',
		'assets/js/vendor/*.js',
		//'assets/vendor/jquery/dist/'
		'assets/vendor/coindonationwidget.com/coin.js'
	];

	grunt.initConfig({
		jshint: {
			options: {
				jshintrc: '.jshintrc'
			},
			all: [
				'Gruntfile.js',
				'assets/js/*.js',
				'!assets/js/scripts.js',
				'!assets/**/*.min.*'
			]
		},
		less: {
			dev: {
				files: {
					'assets/css/main.min.css': [
						'assets/less/app.less'
					]
				},
				options: {
					//compress: true,
					// LESS source map
					// To enable, set sourceMap to true and update sourceMapRootpath based on your install
					//sourceMap: true,
					//sourceMapFilename: 'assets/css/main.min.css.map',
					//sourceMapRootpath: '/app/themes/roots/'

					style: 'expanded',
					compass: true
				}
			},
			build: {
				files: {
					'assets/css/main.min.css': [
						'assets/less/app.less'
					]
				},
				options: {
					compress: true
				}
			}
		},
		concat: {
			options: {
				separator: ';',
			},
			dist: {
				src: [jsFileList],
				dest: 'assets/js/scripts.js',
			},
		},
		uglify: {
			dist: {
				files: {
					'assets/js/scripts.min.js': [jsFileList]
				}
			}
		},
		autoprefixer: {
			options: {
				browsers: ['last 2 versions', 'ie 8', 'ie 9', 'android 2.3', 'android 4', 'opera 12']
			},
			dev: {
				options: {
					map: {
						prev: 'assets/css/'
					}
				},
				src: 'assets/css/main.min.css'
			},
			build: {
				src: 'assets/css/main.min.css'
			}
		},
		modernizr: {
			build: {
				devFile: 'assets/vendor/modernizr/modernizr.js',
				outputFile: 'assets/js/vendor/modernizr.min.js',
				files: {
					'src': [
						['assets/js/scripts.min.js'],
						['assets/css/main.min.css']
					]
				},
				uglify: true,
				parseFiles: true
			}
		},
		version: {
			default: {
				options: {
					algorith: 'md5',
					format: true,
					rename: true,
					length: 6,
					manifest: 'assets/manifest.json',
					querystring: {
						style: 'roots_css',
						script: 'roots_js'
					}
				},
				files: {
					'lib/scripts.php': 'assets/{css,js}/{main,scripts}.min.{css,js}'
				}
			}
		},
		watch: {
			less: {
				files: [
					'assets/less/*.less',
					'assets/less/**/*.less'
				],
				tasks: ['less:dev'/*, 'autoprefixer:dev'*/],
				options: {
					livereload: 35729
				}
			},
			js: {
				files: [
					jsFileList,
					'<%= jshint.all %>'
				],
				tasks: ['jshint', 'concat'],
				options: {
					livereload: 35729
				}
			},
			php: {
				files: ['**/*.php'],
				options: {
					livereload: 35729
				}
			},
			livereload: {
				// Browser live reloading
				// https://github.com/gruntjs/grunt-contrib-watch#live-reloading
				options: {
					livereload: true
				},
				files: [
					'assets/css/main.min.css',
					'assets/js/scripts.min.js',
					'templates/*.php',
					'*.php'
				]
			}
		}
	});

	// Register tasks
	grunt.registerTask('default', [
		'dev'
	]);
	grunt.registerTask('dev', [
		'jshint',
		'less:dev',
		'autoprefixer:dev',
		'concat',
		'watch'
	]);
	grunt.registerTask('build', [
		'jshint',
		'less:build',
		'autoprefixer:build',
		'uglify',
		'modernizr',
		'version'
	]);
};