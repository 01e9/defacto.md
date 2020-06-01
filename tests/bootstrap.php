<?php

$cmd = function ($cmd, $args = '') {
    passthru(sprintf('php "%s/../bin/console" %s --env=test %s', __DIR__, $cmd, $args));
};

$cmd('cache:clear', '--no-warmup');
$cmd('doctrine:database:drop', '--force');
$cmd('doctrine:database:create', '');
$cmd('doctrine:migrations:migrate', '--no-interaction');

require __DIR__.'/../vendor/autoload.php';