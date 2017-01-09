<?php
namespace GitBucketCalendar;

use GitBucketCalendar\Repositories\BitBucket;
use GitBucketCalendar\Repositories\GitHub;

class GitBucketCalendar {
    private $config;
    private $memcached;
    private $memcachedKey = 'gitbucket_contributions';

    public function __construct($config) {
        $this->config = $config;

        $this->verifyConfig();

        $this->memcached = new \Memcached();

        $this->memcached->addServer($this->config['memcached_host'], $this->config['memcached_port']);
    }

    public function refreshContributionsCache() {
        $contributions = $this->getContributions();

        $this->memcached->set($this->memcachedKey, $contributions);
    }

    public function getContributions() {
        $repositories = [
            new GitHub(),
            new BitBucket()
        ];

        $contributions = [];

        $startingTimestamp = null;

        foreach ($repositories as $loopItem) {
            $loopItem->configure($this->config);

            $repositoryContributions = $loopItem->getContributions($startingTimestamp);

            foreach ($repositoryContributions as $subLoopKey => $subLoopItem) {
                if ($startingTimestamp === null) {
                    $startingTimestamp = strtotime($subLoopKey);
                }

                if (isset($contributions[$subLoopKey])) {
                    $contributions[$subLoopKey] += $subLoopItem;
                } else {
                    $contributions[$subLoopKey] = $subLoopItem;
                }
            }
        }

        return $contributions;
    }

    public function printContributionsCalendar() {
        $contributions = $this->memcached->get($this->memcachedKey);

        if ($contributions === false) {
            echo "No contributions data";

            return;
        }

        $maxValue = 0;

        foreach ($contributions as $loopItem) {
            if ($loopItem > $maxValue) {
                $maxValue = $loopItem;
            }
        }

        $steps = [
            round($maxValue * 0.75) => '#1e6823',
            round($maxValue * 0.5) => '#44a340',
            round($maxValue * 0.25) => '#8cc665',
            1 => '#d6e685'
        ];

        require __DIR__ . '/Templates/calendar.php';
    }

    private function verifyConfig() {
        if (!isset($this->config['bitbucket_key'])
                || !isset($this->config['bitbucket_secret'])
                || !isset($this->config['bitbucket_commit_usernames'])
                || !isset($this->config['github_account_username'])
                || !isset($this->config['memcached_host'])
                || !isset($this->config['memcached_port'])) {
            throw new \RuntimeException('Incorrect configuration');
        }
    }
}