<?php

declare (strict_types=1);
require_once 'funktioner.php';
$posterPerSida = 15;

// Kontrollera indata
$error = [];
if (isset($_GET['sida'])) {
    $sida = filter_input(INPUT_GET, 'sida', FILTER_SANITIZE_STRING);
    if ($sida != (string) intval($sida) || $sida < 1) {
        $error[] = "Felaktigt sidnummer ('sida')";
    } else {
        $recFrom = ($sida - 1) * $posterPerSida;
    }
}

if (!isset($sida)) {
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
}

// Indata fel?
if (count($error) > 0) {
    array_unshift($error, "Fel på indata");
    $fel = new stdClass();
    $fel->error = $error;
    skickaJSON($fel, 400);
}

$out = new stdClass();
if (isset($sida)) {
    $out->sida = (int) $sida;
    $out->sidor = ++$sida;

    $out->uppgifter = [];
    for ($i = 0; $i < $posterPerSida; $i++) {
        $rec = new stdClass();
        $rec->id = $recFrom + $i;
        $rec->uppgiftId = $i % count($duties);
        $rec->uppgift = $duties[$i % count($duties)];
        $rec->datum = date("Y-m-d", strtotime("-$i days"));
        $rec->tid = date("h:i", strtotime(rand(3, 8) * 15 . " minutes"));
        $rec->beskrivning = "Fritext ";
        $out->uppgifter[] = $rec;
    }
} else {
    if ($till->format("Y-m-d") === $fran->format("Y-m-d")) {
        $out = new stdClass();
        $out->error = ["Inga rader matchar angivet datumintervall"];
        skickaJSON($out, 400);
    }
    $out->uppgifter = [];
    $date = $fran;
    while ($date < $till) {
        $i = rand(1, 15);
        $rec = new stdClass();
        $rec->id = ++$i;
        $rec->uppgiftId = $i % count($duties);
        $rec->uppgift = $duties[$i % count($duties)];
        $rec->datum = $date->format("Y-m-d");
        $rec->tid = date("h:i", strtotime(rand(3, 8) * 15 . " minutes"));
        $rec->beskrivning = "Fritext ";
        $out->uppgifter[] = $rec;
        $date = $date->add(new DateInterval("P{$i}D"));
    }
}
skickaJSON($out);
