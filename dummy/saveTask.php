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
if (isset($_POST['date']) && $_POST['date'] !== "") {
    $in = filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING);
    if (($datum = date_create_from_format("Y-m-d", $in)) === false) {
        $error[] = "Felaktigt 'date'";
    }elseif ($datum->format('Y-m-d') != $in) {
            $error[] = "Felaktigt angivet 'date'";
        }
} else {
    $error[] = "'date' saknas";
}

if (isset($_POST['time']) && $_POST['time'] !== "") {
    $in = filter_input(INPUT_POST, "time", FILTER_SANITIZE_STRING);
    $tidTest = explode(":", $in);
    if (substr_count($in, ":") === 0 || $tidTest[1] > 59) {
        $error[] = "Felaktigt angiven 'time'";
    } else {
        if (($tid = date_create_from_format('H:i', $in)) === false) {
            $error[] = "Felaktig 'time'";
        }elseif ($tid->format('G:i') != $in) {
            $error[] = "Felaktigt angiven 'time'";
        }
        if ($tid && $tid->format("H:i") > "08:00") {
            $error[] = "Endast tillåtet att rapportera mindre än 8 timmars arbete på en gång";
        }
    }
} else {
    $error[] = "'time' saknas";
}

$activityId = filter_input(INPUT_POST, "activityId ", FILTER_VALIDATE_INT);
if ($activityId ===false || $activityId < 0) {
    $error[] = "Felaktig 'activityId'";
} else {
    if ($activityId > 100) {
        $error[] = "Angiven 'activityId' ($activityId ) saknas";
    }
}
$description = filter_input(INPUT_POST, 'description ', FILTER_SANITIZE_STRING);

// Finns GET-data (dvs uppdatering av post!)
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id < 1) {
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
    if ($id > 50) {
        $out->result = false;
        $out->message = ["Inga förändringar i posten", "0 rad(er) uppdaterades"];
    } else {
        $out->result = true;
        $out->message = ["Spara gick bra", "1 rad(er) uppdaterades"];
    }
} else {
    $id = rand(30, 50);
    $out = new stdClass();
    $out->message = ["Spara gick bra"];
    $out->id = $id;
}
skickaJSON($out);
