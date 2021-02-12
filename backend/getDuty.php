<?php

declare (strict_types=1);

require_once 'funktioner.php';

if (!isset($_GET['id'])) {
    $out = new stdClass();
    $out->error = ["Bad indata", "Invalid GET, id is missing"];
    echo skickaJSON($out, 400);
    exit;
}

$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
if (!($id > 0)) {
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

$sql = "select id, uppgift from duties where id=$id";
$resultat = $db->query($sql);
if ($row = $resultat->fetch_assoc()) {
    $rec = new stdClass();
    $rec->id = $row["id"];
    $rec->uppgift = $row["uppgift"];
    echo skickaJSON($rec);
    exit;
} else {
    $out = new stdClass();
    $out->error = ["No such record", "Id=$id not found"];
    echo skickaJSON($out, 400);
    exit;
}
