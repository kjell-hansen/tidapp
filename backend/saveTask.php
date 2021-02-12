<?php

declare (strict_types=1);
require_once 'funktioner.php';

// Kontrollera anropsmetod
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Missing postdata", "POST required"];
    echo skickaJSON($error, 405);
    exit;
}

// Koppla databas
if (!$db = kopplaDB()) {
    $fel = mysqli_error($db);
    $error = new stdClass();
    $error->error[] = "Något gick fel vid databaskoppling";
    $error->error[] = $fel;
    echo skickaJSON($error, 500);
    exit();
}

// Kontrollera indata
$error = [];
if (isset($_POST['date']) && $_POST['date'] !== "") {
    $in = filter_input(INPUT_POST, "date", FILTER_SANITIZE_STRING);
    if (($datum = date_create_immutable($in)) === false) {
        $error[] = "Felaktigt datum";
    }
} else {
    $error[] = "'date' saknas";
}

if (isset($_POST['time']) && $_POST['time'] !== "") {
    $in = filter_input(INPUT_POST, "time", FILTER_SANITIZE_STRING);
    $tidTest = explode(":", $in);
    if ($tidTest[0] > "08" || $tidTest[1] > 59) {
        $error[] = "Felaktigt angiven tid";
    }
    if (($tid = date_create_immutable_from_format("H:i", $in)) === false) {
        $error[] = "Felaktig tid";
    }
    if ($tid && $tid->format("H:i") > "08:00") {
        $error[] = "Endast tillåtet att rapportera mindre än 8 timmars arbete på en gång";
    }
} else {
    $error[] = "'time' saknas";
}

$dutyid = (int) filter_input(INPUT_POST, "dutyId", FILTER_SANITIZE_NUMBER_INT);
if ($dutyid < 1) {
    $error[] = "Felaktigt dutyId";
} else {
    $sql = "SELECT id FROM duties WHERE id=$dutyid";
    if (!($resultat = $db->query($sql)) || $resultat->num_rows !== 1) {
        $error[] = "Angivet DutyId ($dutyid) saknas";
    }
}
$beskrivning = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

// Finns GET-data (dvs uppdatering av post!)
if (isset($_GET['id'])) {
    $id = (float) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    if ($id != (int) $id || $id < 1) {
        $error[] = "Felaktigt id ($id) angivet";
    } else {
        $sql = "SELECT id from tasks where id=$id";
        if (!($resultat = $db->query($sql)) || $resultat->num_rows !== 1) {
            $error[] = "Angivet id ($id) saknas";
        }
    }
}

// Indata fel?
if (count($error) > 0) {
    array_unshift($error, "Fel på indata");
    $fel = new stdClass();
    $fel->error = $error;
    echo skickaJSON($fel, 400);
    exit;
}

// Uppdatera? OK?
if (isset($id)) {
    $sql = "UPDATE tasks set dutyid=$dutyid, datum='" . $datum->format('Y-m-d') . "',"
            . " tid='" . $tid->format("H:i") . "', beskrivning='$beskrivning'"
            . " WHERE id=$id";
    if ($db->query($sql) && $db->affected_rows > 0) {
        $out = new stdClass();
        $out->message = ["Spara gick bra", $db->affected_rows . " rader uppdaterades"];
        echo skickaJSON($out);
        exit;
    } else {
        $fel = $db->error;
        $out = new stdClass();
        $out->error = ["Något gick fel vid spara", $fel];
        echo skickaJSON($out, 400);
        exit;
    }
}

// Infoga OK?
$sql = "INSERT INTO tasks (dutyid, datum, tid, beskrivning) VALUES"
        . "($dutyid, '" . $datum->format('Y-m-d') . "', '" . $tid->format("H:i") . "'"
        . ", '$beskrivning')";
if ($db->query($sql) && $db->affected_rows > 0) {
    $id = $db->insert_id;
    $out = new stdClass();
    $out->message = ["Spara gick bra"];
    $out->id = $id;
    echo skickaJSON($out);
    exit;
} else {
    $fel = $db->error;
    $out = new stdClass();
    $out->error = ["Något gick fel vid spara", $fel];
    echo skickaJSON($out, 400);
    exit;
}