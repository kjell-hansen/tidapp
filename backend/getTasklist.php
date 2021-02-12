<?php

declare (strict_types=1);
require_once 'funktioner.php';
$posterPerSida =2;

// Kontrollera indata
$error = [];
if (isset($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING);
    if ($page != (string) intval($page)) {
        $error[] = "Felaktigt sidnummer ('page')";
    } else {
        $recFrom = ($page - 1) * $posterPerSida;
    }
}

if (!isset($page)) {
    if (isset($_GET['to']) && $_GET['to'] !== "") {
        $in = filter_input(INPUT_GET, "to", FILTER_SANITIZE_STRING);
        if (($to = date_create_immutable($in)) === false) {
            $error[] = "Felaktigt datum för 'to'";
        }
    } else {
        $error[] = "'to' saknas";
        $to = false;
    }
    if (isset($_GET['from']) && $_GET['from'] !== "") {
        $in = filter_input(INPUT_GET, "from", FILTER_SANITIZE_STRING);
        if (($from = date_create_immutable($in)) === false) {
            $error[] = "Felaktigt datum för 'from'";
        }
    } else {
        $error[] = "'from' saknas";
        $from = false;
    }
    if ($to && $from && $to < $from) {
        $error[] = "'to' ska vara större än 'from'";
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


// Koppla databas
if (!$db = kopplaDB()) {
    $fel = mysqli_error($db);
    $error = new stdClass();
    $error->error[] = "Något gick fel vid databaskoppling";
    $error->error[] = $fel;
    echo skickaJSON($error, 500);
    exit();
}


// Läs poster
if (isset($page)) {
    $sql = "SELECT tasks.id, dutyid, uppgift, tid, datum, beskrivning FROM tasks "
            . " INNER JOIN duties ON tasks.dutyid=duties.id "
            . " LIMIT $recFrom, $posterPerSida ";
    echo $sql;
} else {
    $sql = "SELECT tasks.id, dutyid, uppgift, tid, datum, beskrivning FROM tasks "
            . " INNER JOIN duties ON tasks.dutyid=duties.id "
            . " WHERE datum BETWEEN '" . $from->format("Y-m-d") . "' AND '" . $to->format("Y-m-d") . "'";
}

if (($resultat = $db->query($sql)) && ($resultat->num_rows > 0)) {
    $out = new stdClass();
    if (isset($page)) {
        $out->page=(int) $page;
    }
    $out->uppgift = [];
    while ($row = $resultat->fetch_assoc()) {
        $rec = new stdClass();
        $rec->id = $row["id"];
        $rec->dutyid = $row["dutyid"];
        $rec->uppgift = $row["uppgift"];
        $rec->datum = $row["datum"];
        $rec->tid = $row["tid"];
        $rec->beskrivning = $row["beskrivning"];
        $out->uppgift[] = $rec;
    }
    echo skickaJSON($out);
    exit;
} else {
    $out = new stdClass();
    $out->error = ["Inga rader matchar angivet datumintervall"];
    echo skickaJSON($out, 400);
    exit;
}