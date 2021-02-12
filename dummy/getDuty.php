<?php

declare (strict_types=1);

require_once 'funktioner.php';

if (!isset($_GET['id'])) {
    $out = new stdClass();
    $out->error = ["Felaktig indata", "'id' saknas"];
    skickaJSON($out, 400);
}

$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);
if ($id != (string) intval($id) || $id < 1) {
    $out = new stdClass();
    $out->error = ["Felaktig indata", "Ogiltigt id"];
    skickaJSON($out, 400);
}

if ($id < 100) {
    $rec = new stdClass();
    $rec->id = $id;
    $rec->uppgift = $duties[$id % count($duties)];
    skickaJSON($rec);
} else {
    $out = new stdClass();
    $out->error = ["Post saknas", "id=$id finns inte"];
    skickaJSON($out, 400);
}
