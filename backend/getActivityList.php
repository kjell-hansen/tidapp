<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "Metoden GET ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}
$out = new stdClass();

$db = kopplaDB();
$sql = "SELECT * from activities";
$stmt = $db->prepare($sql);
if (!$stmt->execute()) {
    $out->error = array_merge("Felaktigt databasanrop", $db->errorInfo());
    skickaJSON($out, 400);
}

$out->activities = [];
while ($rec = $stmt->fetchObject()) {
    $out->activities[] = $rec;
}

skickaJSON($out);

