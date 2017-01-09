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

        krsort($contributions);

        $longestStreak = 0;
        $longestStreakStart = '';
        $longestStreakEnd = '';
        $latestStreak = 0;
        $latestStreakEnded = false;
        $latestStreakStart = '';
        $latestStreakEnd = '';
        $currentStreak = 0;
        $currentStreakEnd = '';
        $count = 0;
        $previousKey = null;
        $maxValue = 0;

        foreach ($contributions as $loopKey => $loopItem) {
            if ($previousKey === null) {
                $previousKey = $loopKey;
            }

            if (!$latestStreakEnded) {
                if ($loopItem === 0) {
                    $latestStreakEnded = true;

                    $latestStreakStart = $previousKey;
                } else {
                    if ($latestStreak === 0) {
                        $latestStreakEnd = $loopKey;
                    }

                    $latestStreak++;
                }
            }

            if ($loopItem === 0) {
                if ($currentStreak > $longestStreak) {
                    $longestStreak = $currentStreak;

                    $longestStreakEnd = $currentStreakEnd;
                    $longestStreakStart = $previousKey;
                }

                $currentStreak = 0;
            } else {
                if ($currentStreak === 0) {
                    $currentStreakEnd = $loopKey;
                }

                $currentStreak++;
            }

            $count += $loopItem;
            $previousKey = $loopKey;

            if ($loopItem > $maxValue) {
                $maxValue = $loopItem;
            }
        }

        if ($currentStreak > $longestStreak) {
            $longestStreak = $currentStreak;
        }

        ksort($contributions);

        $result = [
            'contributions' => $contributions,
            'latestStreak' => $latestStreak,
            'latestStreakStart' => $latestStreakStart,
            'latestStreakEnd' => $latestStreakEnd,
            'longestStreak' => $longestStreak,
            'longestStreakStart' => $longestStreakStart,
            'longestStreakEnd' => $longestStreakEnd,
            'contributionsSum' => $count,
            'maxValue' => $maxValue,
            'steps' => [
                round($maxValue * 0.75) => '#1e6823',
                round($maxValue * 0.5) => '#44a340',
                round($maxValue * 0.25) => '#8cc665',
                1 => '#d6e685'
            ]
        ];

        return $result;
    }

    public function printContributionsCalendar() {
        $contributions = $this->memcached->get($this->memcachedKey);

        if ($contributions === false) {
            echo "No contributions data";

            return;
        }

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