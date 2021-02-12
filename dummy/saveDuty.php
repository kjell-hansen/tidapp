<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Saknar postdata", "Metoden POST ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

$error = [];
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    if ($id != (string) intval($id) || $id < 1) {
        $error[] = "Ogiltigt id";
    }
}

if (!isset($_POST['uppgift'])) {
    $error[] = "'uppgift' saknas";
} else {
    $uppgift = trim(filter_input(INPUT_POST, 'uppgift', FILTER_SANITIZE_STRING));
    if ($uppgift === "") {
        $error[] = "Uppgiften får inte vara tom";
    }
}

// Indata fel?
if (count($error) > 0) {
    array_unshift($error, "Fel på indata");
    $fel = new stdClass();
    $fel->error = $error;
    skickaJSON($fel, 400);
}

if (isset($id)) {
    if ($id > 100) {
        $error = new stdClass();
        $error->error = ["Fel vid spara", "Id=$id saknas"];
        skickaJSON($error, 400);
    } elseif (in_array($uppgift, $duties)) {
        $svar = new stdClass();
        $svar->resultat = false;
        $svar->meddelande = ["Spara misslyckades", "Inga poster uppdaterades"];
        skickaJSON($svar);
    } else {
        $svar = new stdClass();
        $svar->resultat = true;
        $svar->meddelande = ["Spara gick bra", "1 post(er) uppdaterades"];
        skickaJSON($svar);
    }
} else {
    if (in_array($uppgift, $duties)) {
        $error = new stdClass();
        $error->error = ["Fel vid spara", "Uppgiften finns redan"];
        skickaJSON($error, 400);
    } else {
        $nyID = rand(30, 50);
        $svar = new stdClass();
        $svar->meddelande = ["Spara lyckades"];
        $svar->id = $nyID;
        skickaJSON($svar);
    }
}