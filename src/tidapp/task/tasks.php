<?php

declare (strict_types=1);
require_once __DIR__ . '/../functions/functions.php';

function getTasksByPage(stdClass $db, int $page): stdClass {
    $all = $db->tasks;
    adjustDates($all);

    $first = ($page - 1) * 5;
    $out = new stdClass();
    if ($first > count($all) || $page === 0) {
        $out->error = ["Fel vid hämtning", "Kunde inte hämta sida $page"];
        return createOutput($out, 400);
    } else {
        $pages = floor(count($all) / 5) + 1;
        $out->pages = $pages;
        $tasksToSend = array_splice($all, $first, 5);

        $out->tasks = addActivityToTasks($tasksToSend, $db->activities);

        return createOutput($out);
    }
}

function getTasksByDate(stdClass $db, DateTimeInterface $from, DateTimeInterface $to): stdClass {
    $all = adjustDates($db->tasks);

    $out = new stdClass();
    if ($from > $to) {
        $out->error = ["Fel vid hämtning", "Fråndatum ({$from->format("Y-m-d")} kan inte vara större än tilldatum ({$to->format("Y-m-d")})"];
        return createOutput($out, 400);
    } else {
        $tasksToSend = array_values(array_filter($all, function ($itm) use ($from, $to) {
                    return $itm->date >= $from->format("Y-m-d") && $itm->date <= $to->format("Y-m-d");
                }));
        $out->tasks = addActivityToTasks($tasksToSend, $db->activities);

        return createOutput($out);
    }
}

function getTask(stdClass $db, int $id): stdClass {
    $all = adjustDates($db->tasks);

    $out = new stdClass();

    $tasksToSend = array_values(array_filter($all, function ($itm) use ($id) {
        return $itm->id === $id;
    }));
    if (count($tasksToSend) === 1) {
        $out = addActivityToTasks($tasksToSend, $db->activities)[0];
    } else {
        $err = ["Fel inträffade", "Id: $id saknas"];
        $out->error = $err;
    }

    return createOutput($out);
}

function adjustDates(array $tasks): array {
    return array_map(function ($item) {
        $itm = clone($item);
        $itm->date = date("Y-m-d", strtotime("{$itm->date} days"));
        return $itm;
    }, $tasks);
}

function addActivityToTasks(array $tasks, array $activities): array {
    foreach ($tasks as $item) {
        $item->activity = array_values(array_filter($activities, function ($itm) use ($item) {
                            return $itm->id === $item->activityId;
                        }))[0]->activity ?? "undefined";
    }

    return $tasks;
}
