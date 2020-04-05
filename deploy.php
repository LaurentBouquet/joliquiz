<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', getenv(DEPLOY_SITE));

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

host(getenv(DEPLOY_HOST))
    ->stage('production')
    ->roles('app')
    ->set('deploy_path', '/var/www/{{application}}')
    ->port(getenv(DEPLOY_PORT));

// Tasks
task('import', function () {
    $DATABASE_URL = run('cat /etc/nginx/sites-available/{{application}} | grep DATABASE_URL | sed -e \'s/^        fastcgi_param DATABASE_URL "//\' -e \'s/..$//\'');
    $DATABASE_URL = str_replace('pgsql://', 'postgres://', $DATABASE_URL);
    run('cd {{release_path}}; psql --file initial-prod-data-languages.pgsql.dump.sql '.$DATABASE_URL. ';');
    run('cd {{release_path}}; psql --file initial-prod-data-users.pgsql.dump.sql '.$DATABASE_URL. ';');
    // run('cd {{release_path}}; psql --file initial-prod-data-exemples.pgsql.dump.sql '.$DATABASE_URL. ';');
});
task('load:env-vars', function() {
    $data['APP_ENV'] = run('cat /etc/nginx/sites-available/{{application}} | grep APP_ENV | sed -e \'s/^        fastcgi_param APP_ENV //\' -e \'s/.$//\'');
    $data['APP_SECRET'] = run('cat /etc/nginx/sites-available/{{application}} | grep APP_SECRET | sed -e \'s/^        fastcgi_param APP_SECRET //\' -e \'s/.$//\'');
    $data['DATABASE_URL'] = run('cat /etc/nginx/sites-available/{{application}} | grep DATABASE_URL | sed -e \'s/^        fastcgi_param DATABASE_URL "//\' -e \'s/..$//\'');
    set('env', $data);
});
before('deploy', 'load:env-vars');
before('rollback', 'load:env-vars');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
