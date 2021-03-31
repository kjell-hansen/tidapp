<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Saknar postdata", "Metoden POST ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

$error = [];
if (isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false || $id < 0) {
        $error[] = "Ogiltigt id ($id)";
    }
}

$db = kopplaTestDB();
if (!isset($_POST['activity'])) {
    $error[] = "'activity' saknas";
} else {
    $uppgift = trim(filter_input(INPUT_POST, 'activity', FILTER_SANITIZE_STRING));
    if ($uppgift === "") {
        $error[] = "'activity' får inte vara tom";
    }
    if (isset($id) && $id !== false) {
        if ($id < 0) {
            $error[] = "Ogiltigt id";
        } else {
            $sql = 'SELECT * from activities where activity=:activity and id<>:id';
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id, 'activity' => $uppgift]);
            if ($stmt->fetch()!==false) {
                $error[] = "'activity' ($uppgift) finns redan för annat 'id'";
            } else {
                $sql = 'SELECT * from activities where id=:id';
                $stmt = $db->prepare($sql);
                $stmt->execute(['id' => $id]);
                if (count($stmt->fetchAll()) === 0) {
                    $error[] = "Angivet 'id' saknas ($id)";
                    $error[]=$db->errorInfo();
                }
            }
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


if (isset($id)) {
    $sql = "UPDATE activities SET activity=:activity WHERE id=:id";
    $stmt = $db->prepare($sql);
    $stmt->execute(['id' => $id, 'activity' => $uppgift]);
    $antal = $stmt->rowCount();
    if ($antal === 0) {
        $svar = new stdClass();
        $svar->result = false;
        $svar->message = ["Uppdatera misslyckades", "Inga poster uppdaterades"];
        skickaJSON($svar);
    } else {
        $svar = new stdClass();
        $svar->result = true;
        $svar->message = ["Uppdatera gick bra", "$antal post(er) uppdaterades"];
        skickaJSON($svar);
    }
} else {
    $sql = "INSERT INTO activities (activity) VALUES (:activity)";
    $stmt = $db->prepare($sql);
    $stmt->execute(['activity' => $uppgift]);
    $antal = $stmt->rowCount();
    if (in_array($uppgift, $activities)) {
        $error = new stdClass();
        $error->error = array_merge(["Fel vid spara"], $stmt->errorInfo());
        skickaJSON($error, 400);
    } else {
        $nyID = $db->lastInsertId();
        $svar = new stdClass();
        $svar->message = ["Spara lyckades"];
        $svar->id = $nyID;
        skickaJSON($svar);
    }
}