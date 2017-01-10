<?php
namespace GitBucketCalendar\Repositories;

interface RepositoryInterface {
    public function __construct(array $config, $fetcher);

    public function getContributions($afterTimestamp);
}