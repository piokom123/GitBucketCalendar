<?php
namespace GitBucketCalendar\Helpers;

class TemplateHelper {
    public static function getCellColor($steps, $count) {
        foreach ($steps as $requiredCount => $color) {
            if ($count >= $requiredCount) {
                return $color;
            }
        }

        return '#eee';
    }
}
