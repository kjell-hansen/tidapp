<?php

declare (strict_types=1);
require_once __DIR__ .'/../functions/functions.php';
function getActivities(stdClass $db):string {
    $out=new stdClass();
    $out->activities= $db->activities;
    return skickaJson($out);
}

function getActivity(stdClass $db, int $id):string {
    foreach ($db->activities as $item) {
        if($item->id===$id) {
            return skickaJson($item);
        }
    }
    $err=["Fel inträffade", "Id: $id saknas"];
    $out=new stdClass();
    $out->error=$err;
    return skickaJson($out, 400);
}