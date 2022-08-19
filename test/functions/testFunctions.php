<?php

declare (strict_types=1);
require_once __DIR__ .'/../../src/tidapp/functions/functions.php';

session_start();
testSetDatabase();
testGetDatabase();
testRefreshDatabase();
session_destroy();

function testSetDatabase(): void {
    session_unset() or die("Cannot unset session");
    $actData = file_get_contents(__DIR__ ."/activities.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    $actData = json_encode(json_decode($actData));
    setDatabase($db);
    $test = json_decode($_SESSION["db"]);
    $activities = json_encode($test->activities);

    if ($activities === $actData) {
        echo "SetDatabase OK \n";
    } else {
        echo "SetDatabase misslyckades förväntade {$actData} fick " . $activities . "\n";
    }
}

function testGetDatabase(): void {
    session_unset() or die("Cannot unset session");
    $actData = file_get_contents(__DIR__ . "/activities.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    setDatabase($db);
    $test = getDatabase();
    if (json_encode($test) === json_encode($db)) {
        echo "GetDatabase OK \n";
    } else {
        echo "GetDatabase misslyckades, förväntat " . json_encode($db) . " fick " . json_encode($test) . " \n";
    }
}

function testRefreshDatabase(): void {
    session_unset() or die("Cannot unset session");
    $old = getDatabase();

    $actData = file_get_contents(__DIR__ . "/activities.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    setDatabase($db);
    if (json_encode($db) !== json_encode($old)) {
        resetDatabase();
        $new = getDatabase();
        if (json_encode($new) === json_encode($old)) {
            echo "RefreshDatabase OK\n";
        } else {
            echo "Refresh database misslyckat, förväntat \n\t" . json_encode($old) . "\n fick \n\t" . json_encode($new) . "\n";
        }
    } else {
        echo "RefreshDatabase misslyckades, fel vid skrivning till databasen\n";
    }
}
