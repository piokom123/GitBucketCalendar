<?php
namespace GitBucketCalendar\Repositories;

abstract class AbstractRepository implements RepositoryInterface {
    protected function inArrayPartial($haystack, $needleArray) {
        foreach ($needleArray as $loopItem) {
            if (stripos($haystack, $loopItem) !== false) {
                return true;
            }
        }

        return false;
    }
}