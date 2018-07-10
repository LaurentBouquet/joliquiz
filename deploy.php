<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'joliquiz');

// Project repository
set('repository', 'git@github.com:LaurentBouquet/joliquiz.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);

set('allow_anonymous_stats', true);

host('54.38.243.9')
    ->stage('production')
    ->roles('app')
    ->set('deploy_path', '/var/www/{{application}}')
    ->port(2267);

// Tasks
task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
