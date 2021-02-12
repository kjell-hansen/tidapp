<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Missing postdata", "POST required"];
    echo skickaJSON($error, 405);
    exit;
}

if (isset($_GET['id'])) {
    $id = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!is_int($id) || $id == 0) {
        $error = new stdClass();
        $error->error = ["Bad indata", "Ogiltigt id"];
        echo skickaJSON($error, 400);
        exit;
    }
}

if (!isset($_POST['uppgift'])) {
    $error = new stdClass();
    $error->error = ["Bad indata", "'uppgift' saknas"];
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

$uppgift = trim(filter_input(INPUT_POST, 'uppgift', FILTER_SANITIZE_STRING));
if ($uppgift === "") {
    $error = new stdClass();
    $error->error[] = "Fel vid spara";
    $error->error[] = "Uppgiften får inte vara tom";
    echo skickaJSON($error, 400);
    exit();
}

if (isset($id)) {
    $sql = "UPDATE duties SET uppgift='$uppgift' WHERE id=$id";
    if ($db->query($sql)) {
        if ($db->affected_rows === 0) {
            $error = new stdClass();
            $error->error[] = "Fel vid spara";
            $error->error[] = "Id=$id saknas (eller ändrades inte uppgift)";
            echo skickaJSON($error, 400);
            exit();
        } else {
            $antal=$db->affected_rows;
            $svar=new stdClass();
            $svar->resultat=true;
            $svar->meddelande=["$antal poster uppdaterades"];
            echo skickaJSON($svar);
            exit;
        }
    }
}

$sql = "SELECT * from duties where uppgift='$uppgift'";
$resultat = $db->query($sql);
if ($resultat->num_rows > 0) {
    $error = new stdClass();
    $error->error[] = "Fel vid spara";
    $error->error[] = "Uppgiften finns redan";
    echo skickaJSON($error, 400);
    exit();
}

$sql = "INSERT INTO duties (uppgift) VALUES ('$uppgift')";
if ($db->query($sql)) {
    $nyID = $db->insert_id;
    $svar = new stdClass();
    $svar->meddelande = ["Spara lyckades"];
    $svar->id = $nyID;
    echo skickaJSON($svar);
    exit;
} else {
    $fel = $db->error;
    $error = new stdClass();
    $error->error[] = "Fel vid spara";
    $error->error[] = $fel;
    echo skickaJSON($error, 400);
    exit();
}
