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
    } elseif ($datum->format('Y-m-d') != $in) {
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
        } elseif ($tid->format('G:i') != $in) {
            $error[] = "Felaktigt angiven 'time'";
        }
        if ($tid && $tid->format("H:i") > "08:00") {
            $error[] = "Endast tillåtet att rapportera mindre än 8 timmars arbete på en gång";
        }
    }
} else {
    $error[] = "'time' saknas";
}

$db = kopplaDB();

$activityId = filter_input(INPUT_POST, "activityId", FILTER_VALIDATE_INT);
if (!$activityId || $activityId < 0) {
    $error[] = "Felaktig 'activityId'";
} else {
    $sql = "SELECT * from activities where id=:id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $activityId]);
    if (!$stmt->fetch()) {
        $error[] = "Angivet aktivitets id ($activityId) saknas";
    }
}
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

// Finns GET-data (dvs uppdatering av post!)
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id < 0) {
        $error[] = "Felaktigt id ($id) angivet";
    } else {
        $sql = "SELECT * from tasks where id=:id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetch()) {
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
    $sql = "UPDATE tasks SET activityid=:activityid, time=:time, date=:date, description=:description where id=:id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id, 'activityid' => $activityId, 'time' => tidStrangTillMinuter($tid->format('G:i')), 'date' => $datum->format('Y-m-d'), 'description' => $description]);
    $antalPoster = $stmt->rowCount();
    if ($antalPoster === 0) {
        $out->result = false;
        $out->message = ["Inga förändringar i posten", "0 rad(er) uppdaterades"];
    } else {
        $out->result = true;
        $out->message = ["Spara gick bra", "$antalPoster rad(er) uppdaterades"];
    }
} else {
    $sql = "INSERT INTO tasks (activityid, time, date, description) VALUES (:activityid, :time, :date, :description)";
    $stmt = $db->prepare($sql);
    $stmt->execute(['activityid' => $activityId, 'time' =>  tidStrangTillMinuter($tid->format('G:i')), 'date' => $datum->format('Y-m-d'), 'description' => $description]);
    $antalPoster = $stmt->rowCount();
    if ($antalPoster === 1) {
        $out = new stdClass();
        $out->message = ["Spara gick bra"];
        $out->id = $db->lastInsertId();
    } else {
        $out = new stdClass();
        $out->error = array_merge(["Spara misslyckades", "$antalPoster uppdaterades"], $stmt->errorInfo());
        skickaJSON($out, 400);
    }
}

skickaJSON($out);
