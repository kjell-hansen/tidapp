<?php

declare (strict_types=1);
require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "Metoden GET ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

// Kontrollera indata
$error = [];
if (isset($_GET['to']) && $_GET['to'] !== "") {
    $in = filter_input(INPUT_GET, "to", FILTER_SANITIZE_STRING);
    if (($to = date_create_from_format("Y-m-d", $in)) === false) {
        $error[] = "Felaktigt datum för 'to'";
    } elseif ($to->format('Y-m-d') != $in) {
        $error[] = "Felaktigt angivet datum för 'to'";
    }
} else {
    $error[] = "'to' saknas";
    $to = false;
}
if (isset($_GET['from']) && $_GET['from'] !== "") {
    $in = filter_input(INPUT_GET, "from", FILTER_SANITIZE_STRING);
    if (($from = date_create_from_format("Y-m-d", $in)) === false) {
        $error[] = "Felaktigt datum för 'from'";
    } elseif ($from->format('Y-m-d') != $in) {
        $error[] = "Felaktigt angivet datum för 'from'";
    }
} else {
    $error[] = "'from' saknas";
    $from = false;
}
if ($to && $from && $to < $from) {
    $error[] = "'to' ska vara större än 'from'";
}

// Indata fel?
if (count($error) > 0) {
    array_unshift($error, "Fel på indata");
    $fel = new stdClass();
    $fel->error = $error;
    skickaJSON($fel, 400);
}
$db = kopplaDB();

$sql = "SELECT activityId, a.activity, sum(time) as time FROM tasks t INNER JOIN activities a ON a.id=t.activityid WHERE t.date BETWEEN :from AND :to  GROUP BY activityId ";
$stmt = $db->prepare($sql);
$stmt->execute(['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]);

$out = new stdClass();
    $out->result=true;
$out->tasks = [];
while ($rec = $stmt->fetchObject()) {
    $rec->time= minuterTillTid((int)$rec->time);
    $out->tasks[] = $rec;
}

if (count($out->tasks) === 0) {
    $out = new stdClass();
    $out->result=false;
    $out->message = ["Inga rader matchar angivet datumintervall"];
    skickaJSON($out);
}

skickaJSON($out);
