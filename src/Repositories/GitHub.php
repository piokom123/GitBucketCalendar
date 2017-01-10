<?php
namespace GitBucketCalendar\Repositories;

use GitBucketCalendar\Repositories\Fetchers\GitHubFetcher;

class GitHub extends AbstractRepository {
    private $fetcher;

    public function __construct(array $config, $fetcher) {
        if (!$fetcher instanceof GitHubFetcher) {
            throw new \RuntimeException('GitHub requires fetcher to be instace of GitBucketCalendar\Repositories\Fetchers\GitHubFetcher');
        }

        $this->fetcher = $fetcher;
    }

    public function getContributions($afterTimestamp) {
        $content = $this->fetcher->getCalendar();

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