module.exports = function(grunt) {
  //require('load-grunt-tasks')(grunt);

	var pkg = grunt.file.readJSON( 'package.json' );

  grunt.initConfig({
    pkg: pkg,
    concat: {
      js: {
        src: ['node_modules/quill/dist/quill.js', 'assets/js/components/*.js'],
        dest: 'assets/js/userwall-wp.js',
      },
      css: {
        src: ['node_modules/quill/dist/quill.snow.css', 'assets/css/components/*.css'],
        dest: 'assets/css/userwall-wp.css',
      },
    },
    uglify: {
      js: {
        src: 'assets/js/userwall-wp.js',
        dest: 'assets/js/userwall-wp.min.js',
      },
    },
    watch: {
      scripts: {
        files: ['js/components/*.js'],
        tasks: ['scripts'],
        options: {
          spawn: false,
        },
      },
      styles: {
        files: ['assets/css/components/*.css'],
        tasks: ['styles'],
        options: {
          spawn: false,
        },
      },
    },
    webpack: {
      options: require('./webpack.config.js'),
      build: {},
    },

    makepot: {
      target: {
        options: {
          domainPath: '/languages', // Path to your translation files
          mainFile: '<%= pkg.name %>.php', // Main plugin file
          potFilename: '<%= pkg.name %>.pot', // Name of the POT file
          type: 'wp-plugin', // Type of project (wp-plugin or wp-theme)
        },
      },
    },
    replace: {
			version_php: {
				src: [
					'**/*.php',
					'!vendor/**',
				],
				overwrite: true,
				replacements: [ {
						from: /Version:(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: 'Version:$1' + pkg.version
				}, {
						from: /@version(\s*?)[a-zA-Z0-9\.\-\+]+$/m,
						to: '@version$1' + pkg.version
				}, {
						from: /@since(.*?)NEXT/mg,
						to: '@since$1' + pkg.version
				}, {
						from: /VERSION(\s*?)=(\s*?['"])[a-zA-Z0-9\.\-\+]+/mg,
						to: 'VERSION$1=$2' + pkg.version
				} ]
			},
			version_readme: {
				src: 'README.md',
				overwrite: true,
				replacements: [ {
						from: /^\*\*Stable tag:\*\*(\s*?)[a-zA-Z0-9.-]+(\s*?)$/mi,
						to: '**Stable tag:**$1<%= pkg.version %>$2'
				} ]
			},
			readme_txt: {
				src: 'README.md',
				dest: 'release/' + pkg.version + '/readme.txt',
				replacements: [ {
						from: /^# (.*?)( #+)?$/mg,
						to: '=== $1 ==='
					}, {
						from: /^## (.*?)( #+)?$/mg,
						to: '== $1 =='
					}, {
						from: /^### (.*?)( #+)?$/mg,
						to: '= $1 ='
					}, {
						from: /^\*\*(.*?):\*\*/mg,
						to: '$1:'
				} ]
			}
		},

		copy: {
			release: {
				src: [
					'**',
					'!assets/js/components/**',
					'!assets/js/src/**',
					'!assets/css/components/**',
					'!assets/css/sass/**',
					'!assets/repo/**',
					'!bin/**',
					'!release/**',
          '!vendor/**',
					'!tests/**',
					'!node_modules/**',
					'!**/*.md',
					'!.travis.yml',
					'!.bowerrc',
					'!.gitignore',
					'!bower.json',
					'!Dockunit.json',
					'!Gruntfile.js',
					'!package.json',
					'!composer.json',
					'!composer.json',
					'!phpcs.xml',
					'!package-lock.json',
          '!webpack.config.js',
          '!composer.lock',
					'!phpunit.xml',
				],
				dest: 'release/' + pkg.version + '/'
			},
            svn: {
                cwd: 'release/<%= pkg.version %>/',
                expand: true,
                src: '**',
                dest: 'release/svn/'
            }
		},

		compress: {
            dist: {
                options: {
                    mode: 'zip',
                    archive: './release/<%= pkg.name %>.<%= pkg.version %>.zip'
                },
                expand: true,
                cwd: 'release/<%= pkg.version %>',
                src: ['**/*'],
                dest: '<%= pkg.name %>'
            }
        },

        wp_deploy: {
            dist: {
                options: {
                    plugin_slug: '<%= pkg.name %>',
                    build_dir: 'release/svn/',
                    assets_dir: 'assets/repo/'
                }
            }
        },

        clean: {
            release: [
                'release/<%= pkg.version %>/',
                'release/svn/'
            ]
        },
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-webpack');
  grunt.loadNpmTasks('grunt-wp-i18n');
  grunt.loadNpmTasks('grunt-wp-deploy');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks( 'grunt-text-replace' );
  grunt.loadNpmTasks( 'grunt-contrib-copy' );

  grunt.registerTask('scripts', ['concat:js', 'uglify:js']);
  grunt.registerTask('styles', ['concat:css']);
  
  // Added a task for localization
  grunt.registerTask('translate', ['makepot']);

  grunt.registerTask('default', ['webpack', 'styles', 'watch']);
  grunt.registerTask('release', ['translate', 'clean:release', 'replace:readme_txt', 'copy', 'compress']);
};
