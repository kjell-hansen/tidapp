<?php

declare (strict_types=1);
require_once __DIR__ . '/../functions/functions.php';

function getTasks(stdClass $db, int $page): stdClass {
    $all = $db->tasks;

    $first = ($page - 1) * 5;
    $out = new stdClass();
    if ($first > count($all) || $page===0) {
        $out->error = ["Fel vid hämtning", "Kunde inte hämta sida $page"];
        return createOutput($out, 400);
    } else {
        $pages = floor(count($all) / 5) + 1;
        $out->pages = $pages;
        $tasksToSend = array_splice($all, $first, 5);
        foreach ($tasksToSend as $item) {
            $item->date = date("Y-m-d", strtotime("{$item->date}days"));
            $item->activity = "undefined";
            foreach ($db->activities as $act) {
                if ($act->id === $item->activityId) {
                    $item->activity = $act->activity;
                    break;
                }
            }
        }
        $out->tasks = $tasksToSend;
        return createOutput($out);
    }
}
