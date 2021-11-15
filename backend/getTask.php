<?php

declare (strict_types=1);

require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "Metoden GET ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

if (!isset($_GET['id'])) {
    $out = new stdClass();
    $out->error = ["Felaktig indata", "'id' saknas"];
    skickaJSON($out, 400);
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if ($id < 1) {
    $out = new stdClass();
    $out->error = ["Felaktig indata", "Ogiltigt id"];
    skickaJSON($out, 400);
}
$db= kopplaDB();
$sql="SELECT t.*, a.activity from tasks t inner join activities a ON a.id=t.activityid where t.id=:id";
$stmt=$db->prepare($sql);
if(!$stmt->execute(['id'=>$id])) {
    $out = new stdClass();
    $out->error= array_merge("Felaktigt databasanrop", $db->errorInfo());
    skickaJSON($out, 400);    
}

if ($rec=$stmt->fetchObject()) {
    $rec->time= minuterTillTid((int)$rec->time);
    skickaJSON($rec);
} else {
    $out = new stdClass();
    $out->error = ["Post saknas", "id=$id finns inte"];
    skickaJSON($out, 400);
}
