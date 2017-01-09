<?php
namespace GitBucketCalendar\Repositories;

class GitHub extends AbstractRepository {
    private $accountUsername;

    public function configure($config) {
        $this->accountUsername = $config['github_account_username'];
    }

    public function getContributions($afterTimestamp) {
        $content = $this->performRequest('https://github.com/users/' . $this->accountUsername . '/contributions');

        $parsed = simplexml_load_string($content);

        $elements = $parsed->xpath("//rect");

        $found = [];

        foreach ($elements as $loopItem) {
            $timestamp = strtotime($loopItem['data-date']->__toString());

            if ($timestamp < $afterTimestamp) {
                continue;
            }

            $found[$loopItem['data-date']->__toString()] = (int) $loopItem['data-count']->__toString();
        }

        return $found;
    }
}