<?php

declare (strict_types=1);
require_once 'funktioner.php';

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    $error = new stdClass();
    $error->error = ["Saknar postdata", "Metoden POST ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

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

$svar = new stdClass();
if ($id > 100) {
    $svar->result = false;
    $svar->message = ["id=$id saknas", "0 post(er) raderades"];
} else {
    $svar->result = true;
    $svar->message = ["1 post(er) raderades"];
}
skickaJSON($svar);

