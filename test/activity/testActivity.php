<?php

declare (strict_types=1);

require_once __DIR__ . '/../../src/tidapp/activity/activities.php';

testGetActivities();
testGetActivity();
testAddActivity();
testUpdateActivity();
testDeleteActivity();

function testGetActivities(): void {
    $db = makeTestDb();
    $test = json_encode(json_decode(getActivities($db)->json));
    $expect = json_encode($db);
    if ($test === $expect) {
        echo "GetActivities OK \n";
    } else {
        echo "GetActivities misslyckades, förväntade: \n\t $expect \nfick \n\t $test\n";
    }
}

function testGetActivity(): void {
    $db = makeTestDb();
    $test = json_decode(getActivity($db, 1)->json);
    $expect = new stdClass();
    $expect->id = 1;
    $expect->activity = "Slappat";
    if (($test->id === $expect->id) && ($test->activity === $expect->activity)) {
        echo "GetActivity OK \n";
    } else {
        echo "GetActivity misslyckades förväntade \n\t" . json_encode($expect) . "\nfick \n\t" . json_encode($test) . "\n";
    }

    $test = json_decode(getActivity($db, 2)->json);
    if (isset($test->error) && is_array($test->error)) {
        echo "GetActivity OK \n";
    } else {
        echo "GetActivity misslyckades förväntade array fick \n\t" . json_encode($test) . "\n";
    }
}

function testAddActivity(): void {
    $db = makeTestDB();
    $postData = ["activity" => "test"];
    $test = json_decode(addActivity($db, $postData)->json);
    $expected = new stdClass();
    $expected->id = 5;
    $expected->message = ["Spara lyckades"];
    if (isset($test->id) && isset($test->message) && $test->id === $expected->id && is_array($test->message)) {
        echo "AddActivity OK\n";
    } else {
        echo "AddActivity misslyckades, förväntade \n\t" . json_encode($expected) . "\nfick \n\t" . json_decode($test) . "\n";
    }

    $test = json_decode(addActivity($db, $postData)->json);
    $expected->id = 6;
    if (isset($test->id) && isset($test->message) && $test->id === $expected->id && is_array($test->message)) {
        echo "AddActivity OK\n";
    } else {
        echo "AddActivity misslyckades, förväntade \n\t" . json_encode($expected) . "\nfick \n\t" . json_decode($test) . "\n";
    }

    $postData = ["test" => "test"];
    $test = json_decode(addActivity($db, $postData)->json);
    $expected = new stdClass();
    if (isset($test->error) && is_array($test->error)) {
        echo "AddActivity OK\n";
    } else {
        echo "AddActivity misslyckades, förväntade array fick \n\t" . json_decode($test) . "\n";
    }
}

function testUpdateActivity(): void {
    $db = makeTestDB();
    $postData = ["activity" => "test"];
    $test = json_decode(updateActivity($db, $postData, 1)->json);
    $expected = new stdClass();
    $expected->result = true;
    $expected->message = ["Uppdatera post 1 lyckades", "1 poster uppdaterade"];
    if (isset($test->result) && isset($test->message) && $test->result === $expected->result && is_array($test->message)) {
        echo "UpdateActivity OK\n";
    } else {
        echo "UpdateActivity misslyckades, förväntade \n\t" . json_encode($expected) . "\nfick \n\t" . json_decode($test) . "\n";
    }

    $test = json_decode(updateActivity($db, $postData, 6)->json);
    $expected->result = false;
    $expected->message = ["Uppdatera post 6 misslyckades", "0 poster uppdaterade"];
    if (isset($test->result) && isset($test->message) && $test->result === $expected->result && is_array($test->message)) {
        echo "UpdateActivity OK\n";
    } else {
        echo "UpdateActivity misslyckades, förväntade \n\t" . json_encode($expected) . "\nfick \n\t" . json_decode($test) . "\n";
    }

    $postData = ["test" => "test"];
    $test = json_decode(updateActivity($db, $postData, 1)->json);
    $expected = new stdClass();
    if (isset($test->error) && is_array($test->error)) {
        echo "UpdateActivity OK\n";
    } else {
        echo "UpdateActivity misslyckades, förväntade array fick \n\t" . json_decode($test) . "\n";
    }
}

function testDeleteActivity(): void {
    $db = makeTestDB();
    $test = json_decode(deleteActivity($db, 1)->json);
    $expected = new stdClass();
    $expected->result = true;
    $expected->message = ["Radera post 1 lyckades", "1 poster raderad"];
    if (isset($test->result) && isset($test->message) && $test->result === $expected->result && is_array($test->message)) {
        echo "DeleteActivity OK\n";
    } else {
        echo "DeleteActivity misslyckades, förväntade \n\t" . json_encode($expected) . "\nfick \n\t" . json_decode($test) . "\n";
    }
    
    $test = json_decode(deleteActivity($db, 1)->json);
    $expected = new stdClass();
    $expected->result = false;
    $expected->message = ["Radera post 1 misslyckades", "0 poster raderade"];
    if (isset($test->result) && isset($test->message) && $test->result === $expected->result && is_array($test->message)) {
        echo "DeleteActivity OK\n";
    } else {
        echo "DeleteActivity misslyckades, förväntade \n\t" . json_encode($expected) . "\nfick \n\t" . json_decode($test) . "\n";
    }
    
    $test= json_decode(getActivity($db, 1)->json);
    if (isset($test->error) && is_array($test->error)) {
        echo "GetActivity efter delete OK \n";
    } else {
        echo "GetActivity efter delete misslyckades förväntade error-array fick \n\t" . json_encode($test) . "\n";
    }
}

function makeTestDB(): stdClass {
    $actData = file_get_contents(__DIR__ . "/../functions/activities.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    return $db;
}
