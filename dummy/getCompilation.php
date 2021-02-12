<?php

declare (strict_types=1);
require_once 'funktioner.php';

// Kontrollera indata
$error = [];
if (isset($_GET['till']) && $_GET['till'] !== "") {
    $in = filter_input(INPUT_GET, "till", FILTER_SANITIZE_STRING);
    if (($till = date_create_immutable($in)) === false) {
        $error[] = "Felaktigt datum för 'till'";
    }
} else {
    $error[] = "'till' saknas";
    $till = false;
}
if (isset($_GET['fran']) && $_GET['fran'] !== "") {
    $in = filter_input(INPUT_GET, "fran", FILTER_SANITIZE_STRING);
    if (($fran = date_create_immutable($in)) === false) {
        $error[] = "Felaktigt datum för 'fran'";
    }
} else {
    $error[] = "'fran' saknas";
    $fran = false;
}
if ($till && $fran && $till < $fran) {
    $error[] = "'till' ska vara större än 'fran'";
}

// Indata fel?
if (count($error) > 0) {
    array_unshift($error, "Fel på indata");
    $fel = new stdClass();
    $fel->error = $error;
    skickaJSON($fel, 400);
}

if ($$till === $fran) {
    $out = new stdClass();
    $out->error = ["Inga rader matchar angivet datumintervall"];
    skickaJSON($out, 400);
}
$out->uppgift = [];
for ($i = 0; $i < count($duties); $i++) {
    $rec = new stdClass();
    $rec->uppgiftId = $i;
    $rec->uppgift = $duties[$i];
    $rec->tid = date("h:i", strtotime(rand(0, 20) * 15 . " minutes"));
    $out->uppgift[] = $rec;
}