<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "Metoden GET ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

if (!isset($_GET['id'])) {
    $out = new stdClass();
    $out->error = ["Felaktig indata", "'id' saknas"];
    skickaJSON($out, 400);
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($id < 1) {
    $out = new stdClass();
    $out->error = ["Felaktig indata", "Ogiltigt id"];
    skickaJSON($out, 400);
}

if ($id < 100) {
    $rec = new stdClass();
    $rec->id = $id;
    $rec->activity = $activities[$id % count($activities)];
    skickaJSON($rec);
} else {
    $out = new stdClass();
    $out->error = ["Post saknas", "id=$id finns inte"];
    skickaJSON($out, 400);
}
