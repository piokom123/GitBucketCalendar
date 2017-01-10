<?php
namespace GitBucketCalendar\Repositories;

use GitBucketCalendar\Repositories\Fetchers\BitBucketFetcher;

class BitBucket extends AbstractRepository {
    private $fetcher;
    private $commitUsernames;
    private $accountUsername;

    public function __construct(array $config, $fetcher) {
        if (!$fetcher instanceof BitBucketFetcher) {
            throw new \RuntimeException('BitBucket requires fetcher to be instace of GitBucketCalendar\Repositories\Fetchers\BitBucketFetcher');
        }

        $this->fetcher = $fetcher;

        $this->commitUsernames = $config['bitbucket_commit_usernames'];
    }

    public function getContributions($afterTimestamp) {
        $this->getAccountUsername();

        if ($afterTimestamp === null) {
            throw new \RuntimeException('Data from GitHub have to be fetched first!');
        }

        $repositoriesResponse = $this->fetcher->getRepositories();

        $repositories = json_decode($repositoriesResponse);

        $found = [];

        foreach ($repositories as $loopItem) {
            $found = $this->getCommits($found, $loopItem, $afterTimestamp);

            $found = $this->getIssues( $found, $loopItem, $afterTimestamp);
        }

        ksort($found);

        return $found;
    }

    private function getAccountUsername() {
        $userResponse = $this->fetcher->getUser();

        $userJSON = json_decode($userResponse);

        $this->accountUsername = $userJSON->username;
    }

    private function getCommits($found, $repository, $afterTimestamp) {
        $items = $this->fetcher->getCommits($repository->owner, $repository->slug);

        $itemsJSON = json_decode($items);

        if (!isset($itemsJSON->values)) {
            return $found;
        }

        foreach ($itemsJSON->values as $loopItem) {
            $timestamp = strtotime($loopItem->date);

            if ($timestamp < $afterTimestamp) {
                continue;
            }

            if ($this->inArrayPartial($loopItem->author->raw, $this->commitUsernames) !== false) {
                $date = date('Y-m-d', $timestamp);

                if (isset($found[$date])) {
                    $found[$date]++;
                } else {
                    $found[$date] = 1;
                }
            }
        }

        return $found;
    }

    private function getIssues($found, $repository, $afterTimestamp) {
        $items = $this->fetcher->getIssues($repository->owner, $repository->slug, $afterTimestamp);

        $itemsJSON = json_decode($items);

        if (!isset($itemsJSON->values)) {
            return $found;
        }

        foreach ($itemsJSON->values as $loopItem) {
            $timestamp = strtotime($loopItem->created_on);

            if ($timestamp < $afterTimestamp) {
                continue;
            }

            if (stripos($loopItem->reporter->username, $this->accountUsername) !== false) {
                $date = date('Y-m-d', $timestamp);

                if (isset($found[$date])) {
                    $found[$date]++;
                } else {
                    $found[$date] = 1;
                }
            }
        }

        return $found;
    }
}