<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';
require 'contrib/rsync.php';

set('application', 'Your Project Name');
set('repository', 'git@github.com:HilmiMubarok/cicd-laravel.git');
set('ssh_multiplexing', true);


set('rsync_src', function () {
    return __DIR__;
});

add('rsync', [
    'exclude' => [
        '.git',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared');
});

host('hilmimub@hilmimubarok.com')
    ->set('remote_user', 'hilmimub')
    ->set('branch', 'main')
    ->set('deploy_path', '~/test-cicd-laravel');

after('deploy:failed', 'deploy:unlock');

desc('Start of Deploy the application');

task('deploy', [
    'deploy:prepare',
    'rsync',
    'deploy:secrets',
    'deploy:vendors',
    'deploy:shared',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:migrate',
    'artisan:queue:restart',
    'deploy:publish'
]);

desc('End of Deploy the application');
