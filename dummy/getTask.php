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
    $i = rand(0, count($duties) - 1);
    $rec = new stdClass();
    $rec->id = $id;
    $rec->dutyid = $i;
    $rec->uppgift = $duties[$i];
    $rec->datum = date("Y-m-d", strtotime("-$i days"));
    $rec->tid = date("h:i", strtotime(rand(3, 8) * 15 . " minutes"));
    ;
    $rec->beskrivning = "Fritext";
    skickaJSON($rec);
} else {
    $out = new stdClass();
    $out->error = ["Post saknas", "id=$id finns inte"];
    skickaJSON($out, 400);
}
