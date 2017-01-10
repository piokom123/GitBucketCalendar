<?php
namespace GitBucketCalendar\Repositories;

use GitBucketCalendar\Repositories\BitBucket;
use GitBucketCalendar\Repositories\Fetchers\BitBucketFetcher;
use GitBucketCalendar\Repositories\GitHub;
use GitBucketCalendar\Repositories\Fetchers\GitHubFetcher;

class RepositoriesFactory {
    public function build(array $config) {
        return [
            new GitHub($config, new GitHubFetcher($config)),
            new BitBucket($config, new BitBucketFetcher($config))
        ];
    }
}