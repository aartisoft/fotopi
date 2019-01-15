module.exports = () ->

	@initConfig
		watch:
			options:
				livereload: 12344
			css:
				files: [
					'css/sass/*.scss'
					'css/sass/**/*.scss'
				]
				tasks: [ 'sass', 'concat:initial', 'cssmin', 'concat:header' ]
			js:
				files: [
					'Gruntfile.*'
					'js/**/*.coffee',
					'js/app/*.js',
					'inc/**/*.coffee'
				]
				tasks: [ 'coffee', 'uglify' ]

		sass:
			dist:
				options:
					style: 'compressed'
				files:
					'css/style.css': 'css/sass/style.scss'

		concat:
			initial:
				files:
					'css/style.css': [ 'css/vendor/**/*.css', 'js/vendor/**/*.css', 'css/style.css']
			header:
				files:
					'style.css': [ 'css/_theme.css', 'css/style.min.css' ]

		cssmin:
			dist:
				files: 'css/style.min.css': [ 'css/style.css' ]

		coffee:
			dist:
				files:
					'js/download/download.js': 'js/download/download.coffee'
					'js/page-header-video/page-header-video-admin.js': 'js/page-header-video/page-header-video-admin.coffee'
					'js/page-header-video/page-header-video.js': 'js/page-header-video/page-header-video.coffee'
					'js/widgets/featured-popular.js': 'js/widgets/featured-popular.coffee'
					'js/widgets/testimonials.js': 'js/widgets/testimonials.coffee'
					'inc/integrations/facetwp/js/facetwp.js': 'inc/integrations/facetwp/js/facetwp.coffee'

		jsonlint:
			dist:
				src: [ 'inc/setup/import-content/**/*.json' ]
				options:
					formatter: 'prose'

		uglify:
			dist:
				options:
					sourceMap: true
				files:
					'js/marketify.min.js': [
						'js/vendor/**/*.js'
						'js/app/marketify.js'
						'js/widgets/featured-popular.js'
						'js/widgets/testimonials.js'
						'js/page-header-video/page-header-video.js'
						'!js/vendor/salvattore/*.js'
					]

		makepot:
			theme:
				options:
					type: 'wp-theme'

		checktextdomain:
			dist:
				options:
					force: true
					text_domain: 'marketify'
					create_report_file: false
					correct_domain: true
					keywords: [
						'__:1,2d'
						'_e:1,2d'
						'_x:1,2c,3d'
						'esc_html__:1,2d'
						'esc_html_e:1,2d'
						'esc_html_x:1,2c,3d'
						'esc_attr__:1,2d'
						'esc_attr_e:1,2d'
						'esc_attr_x:1,2c,3d'
						'_ex:1,2c,3d'
						'_n:1,2,4d'
						'_nx:1,2,4c,5d'
						'_n_noop:1,2,3d'
						'_nx_noop:1,2,3c,4d'
					]
				files: [{
					src: [ '**/*.php', '!node_modules/**' ]
					expand: true
				}]

		glotpress_download:
			theme:
				options:
					url: 'http://astoundify.com/glotpress'
					domainPath: 'languages'
					slug: 'marketify'
					textdomain: 'marketify'
					formats: [ 'mo', 'po' ]
					file_format: '%domainPath%/%wp_locale%.%format%'
					filter:
						translation_sets: false
						minimum_percentage: 50
						waiting_strings: false

	@loadNpmTasks 'grunt-contrib-watch'
	@loadNpmTasks 'grunt-contrib-coffee'
	@loadNpmTasks 'grunt-contrib-uglify'
	@loadNpmTasks 'grunt-contrib-sass'
	@loadNpmTasks 'grunt-contrib-cssmin'
	@loadNpmTasks 'grunt-contrib-concat'
	@loadNpmTasks 'grunt-contrib-concat'
	@loadNpmTasks 'grunt-wp-i18n'
	@loadNpmTasks 'grunt-exec'
	@loadNpmTasks 'grunt-potomo'
	@loadNpmTasks 'grunt-checktextdomain'
	@loadNpmTasks 'grunt-jsonlint'
	@loadNpmTasks 'grunt-glotpress'

	@registerTask 'default', [ 'watch' ]

	@registerTask 'i18n', [ 'makepot', 'glotpress_download' ]
	@registerTask 'checkTranslation', [ 'checktextdomain' ]

	@registerTask 'build', [ 'jsonlint', 'uglify', 'coffee', 'sass', 'concat:initial', 'cssmin', 'concat:header', 'i18n' ]
