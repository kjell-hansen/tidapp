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
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id===false || $id < 0) {
        $error[] = "Ogiltigt id ($id)";
    }
}

if (!isset($_POST['activity'])) {
    $error[] = "'activity' saknas";
} else {
    $uppgift = trim(filter_input(INPUT_POST, 'activity', FILTER_SANITIZE_STRING));
    if ($uppgift === "") {
        $error[] = "'activity' får inte vara tom";
    }
    if (isset($id) && $id!==false) {
        if ($id < 0) {
            $error[] = "Ogiltigt id";
        } elseif ($id < count($activities) && $activities[$id] != $uppgift && in_array($uppgift, $activities)) {
            $error[] = "'activity' finns redan för annat 'id'";
        } elseif ($id > count($activities)) {
            $error[] = "Angivet 'id' saknas ($id)";
        }
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
    if ($id < count($activities) && $activities[$id] == $uppgift) {
        $svar = new stdClass();
        $svar->result = false;
        $svar->message = ["Spara misslyckades", "Inga poster uppdaterades"];
        skickaJSON($svar);
    } else {
        $svar = new stdClass();
        $svar->result = true;
        $svar->message = ["Spara gick bra", "1 post(er) uppdaterades"];
        skickaJSON($svar);
    }
} else {
    if (in_array($uppgift, $activities)) {
        $error = new stdClass();
        $error->error = ["Fel vid spara", "Uppgiften finns redan"];
        skickaJSON($error, 400);
    } else {
        $nyID = rand(30, 50);
        $svar = new stdClass();
        $svar->message = ["Spara lyckades"];
        $svar->id = $nyID;
        skickaJSON($svar);
    }
}