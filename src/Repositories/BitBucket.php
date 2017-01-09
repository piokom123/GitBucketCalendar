<?php
namespace GitBucketCalendar\Repositories;

use Bitbucket\API\Api;
use Bitbucket\API\Http\Listener\OAuthListener;

class BitBucket extends AbstractRepository {
    private $api;
    private $config = [];
    private $commitUsernames;
    private $accountUsername;

    public function configure($config) {
        $this->api = new Api();

        $this->config = [
            'oauth_consumer_key' => $config['bitbucket_key'],
            'oauth_consumer_secret' => $config['bitbucket_secret']
        ];

        $this->commitUsernames = $config['bitbucket_commit_usernames'];

        $this->api->getClient()->addListener(
            new OAuthListener($this->config)
        );

        $this->api->getClient()->setApiVersion('2.0');

        $this->getAccountUsername();
    }

    public function getContributions($afterTimestamp) {
        if ($afterTimestamp === null) {
            throw new \RuntimeException('Data from GitHub have to be fetched first!');
        }

        $this->api->getClient()->setApiVersion('1.0');

        $repositoriesResponse = $this->api->api('User\\Repositories')->get();

        $this->api->getClient()->setApiVersion('2.0');

        $repositories = json_decode($repositoriesResponse->getContent());

        $commitsAPI = $this->api->api('Repositories\\Commits');
        $issuesAPI = $this->api->api('Repositories\\Issues');

        $found = [];

        foreach ($repositories as $loopItem) {
            $found = $this->getCommits($commitsAPI, $found, $loopItem, $afterTimestamp);

            $found = $this->getIssues($issuesAPI, $found, $loopItem, $afterTimestamp);
        }

        ksort($found);

        return $found;
    }

    private function getAccountUsername() {
        $userResponse = $this->api->api('User')->get();

        $userJSON = json_decode($userResponse->getContent());

        $this->accountUsername = $userJSON->username;
    }

    private function getCommits($commitsAPI, $found, $repository, $afterTimestamp) {
        $items = $commitsAPI->all($repository->owner, $repository->slug, [
            'branch' => 'master',
            'pagelen' => 100
        ]);

        $itemsJSON = json_decode($items->getContent());

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

    private function getIssues($issuesAPI, $found, $repository, $afterTimestamp) {
        $items = $issuesAPI->all($repository->owner, $repository->slug, [
            'pagelen' => 100,
            'q' => 'created_on >= ' . date('Y-m-d', $afterTimestamp)
        ]);

        $itemsJSON = json_decode($items->getContent());

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