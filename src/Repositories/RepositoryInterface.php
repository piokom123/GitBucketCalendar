<?php
namespace GitBucketCalendar\Repositories;

interface RepositoryInterface {
    public function configure($config);

    public function getContributions($afterTimestamp);
}