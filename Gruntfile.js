module.exports = function( grunt ) {

	'use strict';

	// Project configuration
	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		addtextdomain: {
			options: {
				textdomain: 'comment-mention',
			},
			update_all_domains: {
				options: {
					updateDomains: true
				},
				src: [ '*.php', '**/*.php', '!\.git/**/*', '!bin/**/*', '!node_modules/**/*', '!tests/**/*' ]
			}
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '\.git/*', 'bin/*', 'node_modules/*', 'tests/*' ],
					mainFile: 'comment-mention-pro.php',
					potFilename: 'lang.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true
					},
					type: 'wp-plugin',
					updateTimestamp: true
				}
			}
		},

		cssmin: {
		  target: {
		    files: [{
		      expand: true,
		      cwd: 'app/assets/css',
		      src: ['*.css', '!*.min.css'],
		      dest: 'app/assets/css',
		      ext: '.min.css'
		    }]
		  }
		},

		uglify: {
		   dev: {
		    files: [{
		      expand: true,
		      src: ['app/assets/js/*.js', '!app/assets/js/*.min.js'],
		      dest: 'assets/js',
		      cwd: '.',
		      rename: function (dst, src) {
		        // To keep the source js files and make new files as `*.min.js`:

		        return src.replace('.js', '.min.js');
		        // Or to override to src:
		        // return src;
		      }
		    }]
		  }
		},
		
		phpcbf: {
			options: {
				bin: 'C:\\Users\\Bunty\\AppData\\Roaming\\Composer\\vendor\\bin\\phpcs',
				standard: 'WordPress',
				noPatch: false,
			},
			application: {
				src: ['**/*.php', '!node_modules/**/*', '!tests/**/*'],
			}
		},

		watch: {
		  scripts: {
		    files: ['**/*.js', '**/*.css'],
		    tasks: ['addtextdomain', 'makepot', 'cssmin', 'uglify', 'phpcbf'],
		    options: {
		      spawn: false,
		    },
		  },
		},

	} );
	
	grunt.loadNpmTasks( 'grunt-phpcbf' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	//grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.registerTask( 'default', [ 'i18n' ] );
	grunt.registerTask( 'i18n', ['addtextdomain', 'makepot', 'cssmin', 'uglify', 'phpcbf'] );
	//grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );

	grunt.util.linefeed = '\n';

};
