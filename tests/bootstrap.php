<?php

$cmd = function ($cmd, $args = '') {
    passthru(sprintf('php "%s/../bin/console" %s --env=test %s', __DIR__, $cmd, $args));
};

$cmd('cache:clear', '--no-warmup');
$cmd('doctrine:database:create', '--quiet');
$cmd('doctrine:query:sql', '--quiet \'CREATE EXTENSION IF NOT EXISTS "uuid-ossp";\'');

require __DIR__.'/../vendor/autoload.php';