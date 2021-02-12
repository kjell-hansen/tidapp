<?php

declare (strict_types=1);
require_once 'funktioner.php';

// Kontrollera anropsmetod
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Saknar postdata", "Metoden POST ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

// Kontrollera indata
$error = [];
if (isset($_POST['datum']) && $_POST['datum'] !== "") {
    $in = filter_input(INPUT_POST, "datum", FILTER_SANITIZE_STRING);
    if (($datum = date_create_immutable($in)) === false) {
        $error[] = "Felaktigt datum";
    }
} else {
    $error[] = "'datum' saknas";
}

if (isset($_POST['tid']) && $_POST['tid'] !== "") {
    $in = filter_input(INPUT_POST, "tid", FILTER_SANITIZE_STRING);
    $tidTest = explode(":", $in);
    if (substr_count($in, ":") === 0 || $tidTest[1] > 59) {
        $error[] = "Felaktigt angiven tid";
    } else {
        if (($tid = date_create_immutable($in)) === false) {
            $error[] = "Felaktig tid";
        }
        if ($tidTest[0] > "23" || ($tid && $tid->format("H:i") > "08:00")) {
            $error[] = "Endast tillåtet att rapportera mindre än 8 timmars arbete på en gång";
        }
    }
} else {
    $error[] = "'tid' saknas";
}

$dutyid = filter_input(INPUT_POST, "uppgiftId", FILTER_SANITIZE_STRING);
if ($dutyid != (string) intval($dutyid) || $dutyid < 1) {
    $error[] = "Felaktig uppgiftId";
} else {
    if ($dutyid > 100) {
        $error[] = "Angiven uppgiftId ($dutyid) saknas";
    }
}
$beskrivning = filter_input(INPUT_POST, 'beskrivning', FILTER_SANITIZE_STRING);

// Finns GET-data (dvs uppdatering av post!)
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    if ($id != (string) intval($id) || $id < 1) {
        $error[] = "Felaktigt id ($id) angivet";
    } else {
        if ($id > 100) {
            $error[] = "Angivet id ($id) saknas";
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

// Uppdatera? OK?
if (isset($id)) {
    $out = new stdClass();
    $out->meddelande = ["Spara gick bra", "1 rad(er) uppdaterades"];
    skickaJSON($out);
} else {
    $id = rand(30, 50);
    $out = new stdClass();
    $out->meddelande = ["Spara gick bra"];
    $out->id = $id;
    skickaJSON($out);
}
    