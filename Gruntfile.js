module.exports = function(grunt) {
    grunt.initConfig({
        concat: {
            js: {
                src: ['assets/js/components/*.js'],
                dest: 'assets/js/threads-wp.js'
            },
            css: {
                src: ['assets/css/components/*.css'],
                dest: 'assets/css/threads-wp.css'
            }
        },
        uglify: {
            js: {
                src: 'assets/js/threads-wp.js',
                dest: 'assets/js/threads-wp.min.js'
            }
        },
        watch: {
            scripts: {
                files: ['assets/js/components/*.js'],
                tasks: ['scripts'],
                options: {
                    spawn: false
                }
            },
            styles: {
                files: ['assets/css/components/*.css'],
                tasks: ['styles'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('scripts', ['concat:js', 'uglify:js']);
    grunt.registerTask('styles', ['concat:css']);
    
    grunt.registerTask('default', ['scripts', 'styles', 'watch']);
};
