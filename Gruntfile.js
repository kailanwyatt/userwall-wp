module.exports = function(grunt) {
    grunt.initConfig({
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
    });
  
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-webpack');
  
    grunt.registerTask('scripts', ['concat:js', 'uglify:js']);
    grunt.registerTask('styles', ['concat:css']);
  
    grunt.registerTask('default', ['webpack', 'styles', 'watch']);
  };
  