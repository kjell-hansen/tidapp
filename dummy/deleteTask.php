<?php

declare (strict_types=1);
require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Saknar postdata", "Metoden POST ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}
$db = kopplaTestDB();

if (isset($_POST['id'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id==false || $id < 1) {
        $error = new stdClass();
        $error->error = ["Felaktig indata", "Ogiltigt 'id'"];
        skickaJSON($error, 400);
    }
} else {
    $error = new stdClass();
    $error->error = ["Felaktig indata", "'id' saknas"];
    skickaJSON($error, 400);
}

$sql = "DELETE FROM tasks where id=:id";
$stmt = $db->prepare($sql);
$stmt->execute(['id' => $id]);
$antal = $stmt->rowCount();
$svar = new stdClass();
if ($antal === 0) {
    $svar->message =array_merge( ["Delete misslyckades"], $stmt->errorInfo());
    $svar->result = false;
} else {
    $svar->result = true;
    $svar->message = ["$antal post(er) raderades"];
}
skickaJSON($svar);