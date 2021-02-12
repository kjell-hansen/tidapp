<?php

declare (strict_types=1);

require_once 'funktioner.php';

$out = new stdClass();
$out->uppgifter = [];
for ($i = 0; $i < count($duties); $i++) {
    $duty = new stdClass();
    $duty->id = $i;
    $duty->uppgift = $duties[$i];
    $out->uppgifter[] = $duty;
}

skickaJSON($out);



