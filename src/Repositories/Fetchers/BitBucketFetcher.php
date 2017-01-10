<?php
namespace GitBucketCalendar\Repositories\Fetchers;

use Bitbucket\API\Api;
use Bitbucket\API\Http\Listener\OAuthListener;

class BitBucketFetcher {
    private $api;
    private $config;

    private $commitsAPI = null;
    private $issuesAPI = null;

    public function __construct($config) {
        $this->api = new Api();

        $this->config = [
            'oauth_consumer_key' => $config['bitbucket_key'],
            'oauth_consumer_secret' => $config['bitbucket_secret']
        ];

        $this->api->getClient()->addListener(
            new OAuthListener($this->config)
        );

        $this->api->getClient()->setApiVersion('2.0');
    }

    public function getRepositories() {
        $this->api->getClient()->setApiVersion('1.0');

        $repositoriesResponse = $this->api->api('User\\Repositories')->get()->getContent();

        $this->api->getClient()->setApiVersion('2.0');

        return $repositoriesResponse;
    }

    public function getUser() {
        return $this->api->api('User')->get()->getContent();
    }

    public function getCommits($owner, $slug) {
        if ($this->commitsAPI === null) {
            $this->commitsAPI = $this->api->api('Repositories\\Commits');
        }

        return $this->commitsAPI->all($owner, $slug, [
            'branch' => 'master',
            'pagelen' => 100
        ])->getContent();
    }

    public function getIssues($owner, $slug, $afterTimestamp) {
        if ($this->issuesAPI === null) {
            $this->issuesAPI = $this->api->api('Repositories\\Issues');
        }

        return $this->issuesAPI->all($owner, $slug, [
            'pagelen' => 100,
            'q' => 'created_on >= ' . date('Y-m-d', $afterTimestamp)
        ])->getContent();
    }
}