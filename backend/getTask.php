<?php

declare (strict_types=1);

require_once 'funktioner.php';

if (!isset($_GET['id'])) {
    $out = new stdClass();
    $out->error = ["Bad indata", "Invalid GET, id is missing"];
    echo skickaJSON($out, 400);
    exit;
}

$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING);
if (($id != (string) intval($id)) || ($id < 1)) {
    $out = new stdClass();
    $out->error = ["Bad indata", "Invalid id"];
    echo skickaJSON($out, 400);
    exit;
}

if (!$db = kopplaDB()) {
    $fel = mysqli_error($db);
    $error = new stdClass();
    $error->error[] = "Något gick fel vid databaskoppling";
    $error->error[] = $fel;
    echo skickaJSON($error, 500);
    exit();
}

$sql = "SELECT tasks.id, dutyid, uppgift, tid, datum, beskrivning FROM tasks "
            . " INNER JOIN duties ON tasks.dutyid=duties.id  where tasks.id=$id";
$resultat = $db->query($sql);
if ($row = $resultat->fetch_assoc()) {
    $rec = new stdClass();
        $rec = new stdClass();
        $rec->id = $row["id"];
        $rec->dutyid = $row["dutyid"];
        $rec->uppgift = $row["uppgift"];
        $rec->datum = $row["datum"];
        $rec->tid = $row["tid"];
        $rec->beskrivning = $row["beskrivning"];
    echo skickaJSON($rec);
    exit;
} else {
    $out = new stdClass();
    $out->error = ["No such record", "Id=$id not found"];
    echo skickaJSON($out, 400);
    exit;
}
