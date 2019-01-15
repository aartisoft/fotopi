module.exports = function(grunt) {

  grunt.registerTask('watch', [ 'watch' ]);

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

	// concat
	concat: {
		main: {
			options: {
				separator: ';'
			},
			src: ['includes/js/edd-wl.js'],
			dest: 'includes/js/<%= pkg.name %>.min.js'
		}
	},

    // uglify
    uglify: {
        options: {
          mangle: false
        },
        js: {
          files: {
            'includes/js/<%= pkg.name %>.min.js': ['includes/js/<%= pkg.name %>.min.js']
          }
        }
    },
	
    // LESS CSS
    less: {
      style: {
        options: {
          compress: true
        },
        files: {
          "templates/edd-wl.min.css": "includes/less/edd-wl.less"
        }
	},
	style2: {
	  options: {
		compress: false
	  },
	  files: {
		"templates/edd-wl.css": "includes/less/edd-wl.less"
	  }
	}
    },

    // watch our project for changes
    watch: {
      // JS
      js: {
        files: ['includes/js/edd-wl.js'],
        tasks: ['concat:main', 'uglify:js'],
      },
      // CSS
      css: {
        // compile CSS when any .less file is compiled in this theme and also the parent theme
        files: ['includes/less/edd-wl.less'],
        tasks: ['less:style', 'less:style2'],
      },

    }
  });

  // Saves having to declare each dependency
  require( "matchdep" ).filterDev( "grunt-*" ).forEach( grunt.loadNpmTasks );

  grunt.registerTask('default', ['concat', 'uglify', 'less' ]);
};
