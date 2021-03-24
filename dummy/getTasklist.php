<?php

declare (strict_types=1);
require_once 'funktioner.php';
$posterPerSida = 15;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "Metoden GET ska användas vid anrop till sidan"];
    skickaJSON($error, 405);
}

// Kontrollera indata
$error = [];
if (isset($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if ($page < 1) {
        $error[] = "Felaktigt sidnummer ('page')";
    }
}

if (!isset($_GET['page']) && !isset($_GET['to']) && !isset($_GET['from'])) {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "'page' ELLER 'to' och 'from' ska anges vid anrop"];
    skickaJSON($error, 400);
}

if (!isset($page)) {
    if (isset($_GET['to']) && $_GET['to'] !== "") {
        $in = filter_input(INPUT_GET, "to", FILTER_SANITIZE_STRING);
        if (($to = date_create_from_format("Y-m-d", $in)) === false) {
            $error[] = "Felaktigt datum för 'to'";
        } elseif ($to->format('Y-m-d') != $in) {
            $error[] = "Felaktigt angivet datum för 'to'";
        }
    } else {
        $error[] = "'to' saknas";
        $to = false;
    }
    if (isset($_GET['from']) && $_GET['from'] !== "") {
        $in = filter_input(INPUT_GET, "from", FILTER_SANITIZE_STRING);
        if (($from = date_create_from_format("Y-m-d", $in)) === false) {
            $error[] = "Felaktigt datum för 'from'";
        }elseif ($from->format('Y-m-d') != $in) {
            $error[] = "Felaktigt angivet datum för 'from'";
        }
    } else {
        $error[] = "'from' saknas";
        $from = false;
    }
    if ($to && $from && $to < $from) {
        $error[] = "'to' ska vara större än 'from'";
    }
}

// Indata fel?
if (count($error) > 0) {
    array_unshift($error, "Fel på indata");
    $fel = new stdClass();
    $fel->error = $error;
    skickaJSON($fel, 400);
}

$out = new stdClass();
if (isset($page)) {
    if ($page > 100) {
        $out = new stdClass();
        $out->pages = 100;
        $out->message = ["Otillräckligt antal poster för att visa sidan"];
        skickaJSON($out);
    } else {
        $page < 100 ? $out->pages = ++$page : $out->pages = 100;
        $recFrom = ($page - 1) * $posterPerSida + 1;
        $out->tasks = [];
        for ($i = 0; $i < $posterPerSida; $i++) {
            $rec = new stdClass();
            $rec->id = $recFrom + $i;
            $rec->activityId = $i % count($activities);
            $rec->activity = $activities[$i % count($activities)];
            $rec->date = date("Y-m-d", strtotime("-$i days"));
            $rec->time = date("G:i", mktime(0, rand(3, 8) * 15));
            $rec->description = "Fritext ";
            $out->tasks[] = $rec;
        }
    }
} else {
    if ($to->format("Y-m-d") === $from->format("Y-m-d")) {
        $out = new stdClass();
        $out->message = ["Inga rader matchar angivet datumintervall"];
        skickaJSON($out);
    }
    $out->tasks = [];
    $date = $from;
    $id = rand(1, 15);
    while ($date < $to) {
        $i = rand(1, 15);
        $rec = new stdClass();
        $rec->id = $id++;
        $rec->activityId = $i % count($activities);
        $rec->activity = $activities[$i % count($activities)];
        $rec->date = $date->format("Y-m-d");
        $rec->time = date("G:i", mktime(0, rand(3, 8) * 15));
        $rec->description = "Fritext ";
        $out->tasks[] = $rec;
        $date = $date->add(new DateInterval("P{$i}D"));
    }
}
skickaJSON($out);
