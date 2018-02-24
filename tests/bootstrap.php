<?php

$cmd = function ($cmd, $args = '') {
    passthru(sprintf('php "%s/../bin/console" %s --env=test %s', __DIR__, $cmd, $args));
};

$cmd('cache:clear', '--no-warmup');
$cmd('doctrine:schema:drop', '--force');
$cmd('doctrine:schema:create');
$cmd('doctrine:query:sql', '\'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";\'');
$cmd('doctrine:fixtures:load', '--no-interaction');

require __DIR__.'/../vendor/autoload.php';