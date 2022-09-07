<?php

namespace App\Services;

class FilterStatusTasks
{
    public static function filter(array $tasks, bool $boolean): array
    {
        $tasksStatus = [];
        foreach ($tasks as $element) {
            if ($element->isIsDone() === $boolean) {
                array_push($tasksStatus, $element);
            }
        }
        return $tasksStatus;
    }
}
