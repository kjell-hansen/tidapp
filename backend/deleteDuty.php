<?php

declare (strict_types=1);
require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Missing postdata", "POST required"];
    echo skickaJSON($error, 405);
    exit;
}

if (isset($_POST['id'])) {
    $id = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!is_int($id) || $id == 0) {
        $error = new stdClass();
        $error->error = ["Bad indata", "Ogiltigt id"];
        echo skickaJSON($error, 400);
        exit;
    }
} else {
    $error = new stdClass();
    $error->error = ["Bad indata", "id saknas"];
    echo skickaJSON($error, 400);
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

$sql = "DELETE FROM duties WHERE id=$id";
if ($db->query($sql)) {
    if ($db->affected_rows === 0) {
        $error = new stdClass();
        $error->error[] = "Fel vid radera";
        $error->error[] = "Id=$id saknas";
        echo skickaJSON($error, 400);
        exit();
    } else {
        $antal = $db->affected_rows;
        $svar = new stdClass();
        $svar->resultat = true;
        $svar->meddelande = ["$antal poster raderades"];
        echo skickaJSON($svar);
        exit;
    }
}

