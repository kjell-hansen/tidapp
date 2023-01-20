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
        return createOutput($out);
    } else {
        $err = ["Fel inträffade", "Id: $id saknas"];
        $out->error = $err;
        return createOutput($out, 400);
    }
}

function deleteTask(stdClass $db, int $id): stdClass {
    $all = $db->tasks;

    $out = new stdClass();

    $new = array_values(array_filter($all, function ($itm) use ($id) {
                return $itm->id !== $id;
            }));

    if ($all !== $new) {
        $db->tasks = $new;
        setDatabase($db);
        $out->result = true;
        $out->message = ["Radera post $id lyckades", "1 poster raderade"];
    } else {
        $out->result = false;
        $out->message = ["Radera post $id misslyckades", "0 poster raderade"];
    }

    return createOutput($out);
}

function addTask(stdClass $db, array $postData): stdClass {

    $out = new stdClass();
    try {
        $today = new DateTimeImmutable();
        $new = new stdClass();
        if (!isset($postData["activityId"])) {
            throw new Exception("Aktivitet (activityId) saknas");
        }
        $new->activityId =(int) $postData["activityId"];
        if (!isset($postData["time"])) {
            throw new Exception("Tid (time) saknas");
        }
        $time = DateTimeImmutable::createFromFormat("H:i", $postData["time"]);
        if ($time !== false) {
            $new->time = $postData["time"];
        } else {
            throw new Exception("Ogiltig tid");
        }
        if (!isset($postData["date"])) {
            throw new Exception("Datum (date) saknas");
        }
        $date = new DateTimeImmutable($postData["date"]);
        if ($date !== false) {
            if ($date->format('Y-m-d') < $today->format('Y-m-d')) {
                $new->date = $today->diff($date)->format("%R%a");
            } else {
                throw new Exception("Datum får inte vara i framtiden");
            }
        } else {
            throw new Exception("Ogiltigt datum");
        }
        $new->description = $postData["description"] ?? null;
        $max = 0;
        foreach ($db->tasks as $item) {
            if ($item->id > $max) {
                $max = $item->id;
            }
        }
        $max++;
        $new->id = $max;
        $db->tasks[] = $new;
        setDatabase($db);
        $out->id = $max;
        $out->message = ["Spara ny post lyckades", "1 poster lades till"];
        return createOutput($out);
    } catch (Exception $ex) {
        $out->error[] = "Fel inträffade";
        $out->error[] = $ex->getMessage();
        return createOutput($out, 400);
    }
}

function updateTask(stdClass $db, array $postData, int $id): stdClass {
    $out = new stdClass();
    try {
        $today = new DateTimeImmutable();
        $new = new stdClass();
        if (!isset($postData["activityId"])) {
            throw new Exception("Aktivitet (activityId) saknas");
        }
        $new->activityId =(int) $postData["activityId"];
        if (!isset($postData["time"])) {
            throw new Exception("Tid (time) saknas");
        }
        $time = DateTimeImmutable::createFromFormat("H:i", $postData["time"]);
        if ($time !== false) {
            $new->time = $postData["time"];
        } else {
            throw new Exception("Ogiltig tid");
        }
        if (!isset($postData["date"])) {
            throw new Exception("Datum (date) saknas");
        }
        $date = new DateTimeImmutable($postData["date"]);
        if ($date !== false) {
            if ($date->format('Y-m-d') < $today->format('Y-m-d')) {
                $new->date = $today->diff($date)->format("%R%a");
            } else {
                throw new Exception("Datum får inte vara i framtiden");
            }
        } else {
            throw new Exception("Ogiltigt datum");
        }
        $new->description = $postData["description"] ?? null;
        $new->id = $id;
        $old = array_values(array_filter($db->tasks, function ($itm) use ($id) {
                    return $itm->id !== $id;
                }));
        if (count($old) === count($db->tasks) - 1) {
            $db->tasks = $old;
            $db->tasks[] = $new;
            setDatabase($db);
            $out->result = true;
            $out->message = ["Uppdatera post $id lyckades", "1 poster uppdaterades"];
        } else {
            $out->result = false;
            $out->message = ["Uppdatera post $id misslyckades", "0 poster uppdaterades"];
        }
        return createOutput($out);
    } catch (Exception $ex) {
        $err = new stdClass();
        $err->error = ["Fel vid uppdatera post", $ex->getMessage()];
        return createOutput($err, 400);
    }
}

function getCompilation(stdClass $db, DateTimeInterface $from, DateTimeInterface $to): stdClass {
    $all = adjustDates($db->tasks);

    $out = new stdClass();
    if ($from > $to) {
        $out->error = ["Fel vid hämtning", "Fråndatum ({$from->format("Y-m-d")} kan inte vara större än tilldatum ({$to->format("Y-m-d")})"];
        return createOutput($out, 400);
    } else {
        $records = array_filter($all, function ($itm) use ($from, $to) {
            return $itm->date >= $from->format("Y-m-d") && $itm->date <= $to->format("Y-m-d");
        });
        $sum = [];
        $tasks = [];
        foreach ($records as $item) {
            $key = $item->activityId;
            $tid = explode(":", $item->time);
            $antalMinuter = $tid[0] * 60 + $tid[1];
            if (isset($sum[$key])) {
                $sum[$key] += $antalMinuter;
                $tasks[$key]->time = floor($sum[$key] / 60) . ":" . trim(sprintf("%02d", $sum[$key] % 60));
            } else {
                $tasks[$key] = new stdClass();
                $tasks[$key]->activityId = $key;
                $sum[$key] = $antalMinuter;
                $tasks[$key]->time = floor($sum[$key] / 60) . ":" . trim(sprintf("%02d", $sum[$key] % 60));
            }
        }
        $tasks = array_values(addActivityToTasks($tasks, $db->activities));
        $out->tasks = $tasks;
        return createOutput($out);
    }
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
