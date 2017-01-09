<?php
namespace GitBucketCalendar;

use GitBucketCalendar\Repositories\BitBucket;
use GitBucketCalendar\Repositories\GitHub;

class GitBucketCalendar {
    private $config;

    public function __construct($config) {
        $this->config = $config;
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
        $contributions = $this->getContributions();

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
}