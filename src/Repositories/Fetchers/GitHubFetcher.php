<?php
namespace GitBucketCalendar\Repositories\Fetchers;

use GitBucketCalendar\Helpers\HTTPHelper;

class GitHubFetcher {
    private $accountUsername;

    public function __construct($config) {
        $this->accountUsername = $config['github_account_username'];
    }

    public function getCalendar() {
        return HTTPHelper::performRequest('https://github.com/users/' . $this->accountUsername . '/contributions');
    }
}