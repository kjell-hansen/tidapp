<?php

declare (strict_types=1);
require_once 'funktioner.php';

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
$posterPerSida = 15;
if (isset($_GET['records']) && filter_input(INPUT_GET, 'records', FILTER_VALIDATE_INT)) {
    $posterPerSida = $_GET['records'];
}

if (!isset($_GET['page']) && !isset($_GET['to']) && !isset($_GET['from'])) {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "'page' ELLER 'to' och 'from' ska anges vid anrop"];
    skickaJSON($error, 400);
}

if (isset($_GET['activity']) && filter_input(INPUT_GET, 'activity', FILTER_VALIDATE_INT) === false) {
    $error = new stdClass();
    $error->error = ["Felaktigt anrop", "'activity' ska vara ett heltal"];
    skickaJSON($error, 400);
} else {
    $activityId = filter_input(INPUT_GET, 'activity', FILTER_VALIDATE_INT);
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
        } elseif ($from->format('Y-m-d') != $in) {
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

$db = kopplaDB();
$out = new stdClass();

if (isset($page)) {
    $sql = "SELECT COUNT(*) FROM tasks";
    $stmt = $db->query($sql);
    $row = $stmt->fetch(PDO::FETCH_NUM);
    $antalPoster = (int) $row[0];
    $antalSidor = ceil($antalPoster / $posterPerSida);
    if ($page > $antalSidor) {
        $out = new stdClass();
        $out->pages = $antalSidor;
        $out->message = ["Otillräckligt antal poster för att visa sidan"];
        skickaJSON($out);
    } else {

        $sql = "SELECT t.*, a.activity FROM tasks t INNER JOIN activities a ON a.id=t.activityid order by date DESC LIMIT " . $posterPerSida * ($page - 1) . ",$posterPerSida";
        $stmt = $db->prepare($sql);
        if (!$stmt->execute()) {
            var_dump($stmt->execute());
            echo "95";
            $out->error = array_merge(["Felaktigt databasanrop"], $db->errorInfo());
            skickaJSON($out, 400);
        }

        $out->pages = $antalSidor;
        $out->tasks = [];
        while ($rec = $stmt->fetchObject()) {
            $rec->date = date("Y-m-d", strtotime($rec->date));
            $rec->time = minuterTillTid((int) $rec->time);
            $out->tasks[] = $rec;
        }
    }
} else {
    $params = ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')];
    $sql = "SELECT t.*, a.activity FROM tasks t INNER JOIN activities a ON a.id=t.activityid where (t.date BETWEEN :from AND :to)";
    if (isset($activityId)) {
        $sql .= " AND a.id=:id";
        $params['id'] = $activityId;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $out->result = false;
    $out->tasks = [];
    while ($rec = $stmt->fetchObject()) {
        $rec->date = date("Y-m-d", strtotime($rec->date));
        $rec->time = minuterTillTid((int) $rec->time);
        $out->tasks[] = $rec;
    }

    if (count($out->tasks) === 0) {
        $out->result = false;
        $out->message = ["Inga rader matchar angivet datumintervall"];
        skickaJSON($out);
    }
}

skickaJSON($out);
