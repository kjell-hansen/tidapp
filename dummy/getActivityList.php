<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "Metoden GET ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

$out = new stdClass();
$out->activities = [];
for ($i = 0; $i < count($activities); $i++) {
    $activity = new stdClass();
    $activity->id = $i;
    $activity->activity = $activities[$i];
    $out->activities[] = $activity;
}

skickaJSON($out);

