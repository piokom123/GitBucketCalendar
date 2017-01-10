<?php

$composerAutoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

require_once __DIR__ . '/config.php';


$repo = new GitBucketCalendar\GitBucketCalendar($config, new GitBucketCalendar\Repositories\RepositoriesFactory());

$repo->refreshContributionsCache();