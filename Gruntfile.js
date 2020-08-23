module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            mail: {
                files: {
                    'resources/js/humhub.mail.messenger.bundle.min.js': ['resources/js/humhub.mail.messenger.bundle.js'],
                    'resources/js/humhub.mail.notification.min.js': ['resources/js/humhub.mail.notification.js'],
                }
            }
        },
        cssmin: {
            target: {
                files: {
                    'resources/css/humhub.mail.min.css': ['resources/css/humhub.mail.css']
                }
            }
        },
        concat: {
            messenger: {
                src:[
                    'resources/js/humhub.mail.ConversationView.js',
                    'resources/js/humhub.mail.ConversationViewEntry.js',
                    'resources/js/humhub.mail.inbox.js',
                    'resources/js/humhub.mail.conversation.js',
                ],
                dest: 'resources/js/humhub.mail.messenger.bundle.js'
            },
        }
    });

    //grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');


    grunt.registerTask('build', ['concat', 'uglify', 'cssmin']);
};
