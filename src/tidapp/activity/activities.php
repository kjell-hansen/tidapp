<?php

declare (strict_types=1);
require_once __DIR__ . '/../functions/functions.php';

function getActivities(stdClass $db): stdClass {
    $out = new stdClass();
    $out->activities = $db->activities;
    resetDatabase();
    return createOutput($out);
}

function getActivity(stdClass $db, int $id): stdClass{
    foreach ($db->activities as $item) {
        if ($item->id === $id) {
            return createOutput($item);
        }
    }
    $err = ["Fel inträffade", "Id: $id saknas"];
    $out = new stdClass();
    $out->error = $err;
    return createOutput($out, 400);
}

function addActivity(stdClass $db, array $postData): stdClass{
    try {
        $activity = new stdClass();
        if (isset($postData["activity"])) {
            $activity->activity = $postData["activity"];
        } else {
            throw new Exception("activity saknas");
        }
        $max = 0;
        foreach ($db->activities as $item) {
            if ($item->id > $max) {
                $max = $item->id;
            }
        }
        $max++;
        $activity->id = $max;
        array_push($db->activities, $activity);
        setDatabase($db);
        $out = new stdClass();
        $out->id = $activity->id;
        $out->message = ["Spara lyckades"];
        return createOutput($out);
    } catch (Exception $exc) {
        $err = new stdClass();
        $err->error = ["Fel vid spara ny post", $exc->getMessage()];
        return createOutput($err, 400);
    }
}

function updateActivity(stdClass $db, array $postData, int $id): stdClass{
    try {
        if (isset($postData["activity"])) {
            $activity = $postData["activity"];
        } else {
            throw new Exception("activity saknas");
        }
        $check = false;
        foreach ($db->activities as $item) {
            if ($item->id === $id) {
                $item->activity = $activity;
                $check = true;
            }
        }
        $out = new stdClass();
        $out->result = $check;
        if ($check) {
            setDatabase($db);
            $out->message = ["Uppdatera post $id lyckades", "1 poster uppdaterade"];
        } else {
            $out->message = ["Uppdatera post $id misslyckades", "0 poster uppdaterade"];
        }
        return createOutput($out);
    } catch (Exception $exc) {
        $err = new stdClass();
        $err->error = ["Fel vid uppdatera post", $exc->getMessage()];
        return createOutput($err, 400);
    }
}

function deleteActivity(stdClass $db, int $id) {
        $check = false;
        $old=$db->activities;
        $new=[];
        foreach ($old as $item) {
            if ($item->id === $id) {
                $check = true;
            } else {
                $new[]=$item;
            }
        }
        $out = new stdClass();
        $out->result = $check;
        if ($check) {
            $db->activities=$new;
            setDatabase($db);
            $out->message = ["Radera post $id lyckades", "1 poster raderade"];
        } else {
            $out->message = ["Radera post $id misslyckades", "0 poster raderade"];
        }
        return createOutput($out);    
}
