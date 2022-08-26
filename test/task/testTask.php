<?php

declare (strict_types=1);
require_once __DIR__ . '/../../src/tidapp/task/tasks.php';

testGetTasksByPage();
testGetTasksByDate();
testGetTask();
testDeleteTask();
testAddTask();
testUpdateTask();

function testGetTasksByPage(): void {
    $db = makeTestDb();
    $test = json_decode(getTasksByPage($db, 1)->json);
    $antalPoster = 5;
    $antalSidor = 3;
    if ($test->pages === $antalSidor && count($test->tasks) === $antalPoster) {
        echo "GetTasksByPage sida 1 OK \n";
    } else {
        echo "GetTasksByPage sida 1, förväntade: \n\t sidor:$antalSidor \n\t Antal poster:$antalPoster \nfick \n\t Antal sidor: $test->pages \n\t Antal poster: " . count($test->tasks) . " \n";
    }

    $test = json_decode(getTasksByPage($db, 3)->json);
    $antalPoster = 2;
    $antalSidor = 3;
    if ($test->pages === $antalSidor && count($test->tasks) === $antalPoster) {
        echo "GetTasksByPage sida 3 OK \n";
    } else {
        echo "GetTasksByPage sida 3, förväntade: \n\t sidor:$antalSidor \n\t Antal poster:$antalPoster \nfick \n\t Antal sidor: $test->pages \n\t Antal poster: " . count($test->tasks) . " \n";
    }

    $test = json_decode(getTasksByPage($db, 5)->json);
    if (is_array($test->error)) {
        echo "GetTasksByPage sida 5 OK \n";
    } else {
        echo "GetTasksByPage sida 5, förväntade: array \nfick \n";
        print_r($test);
    }
}

function testGetTasksByDate(): void {
    $db = makeTestDb();
    $test = json_decode(getTasksByDate($db, new DateTime("-51 days"), new DateTime("-39 days"))->json);
    $antalPoster = 4;

    if (count($test->tasks) === $antalPoster) {
        echo "GetTasksByDate OK \n";
    } else {
        echo "GetTasksByDate förväntade: $antalPoster \nfick " . count($test->tasks) . "\n";
        print_r($test);
    }

    $test = json_decode(getTasksByDate($db, new DateTime("-1 days"), new DateTime("-1 days"))->json);
    $antalPoster = 0;

    if (count($test->tasks) === $antalPoster) {
        echo "GetTasksByDate ingen retur OK \n";
    } else {
        echo "GetTasksByDate fråndatum > tilldatum , förväntade: array \nfick \n";
        print_r($test);
    }

    $test = json_decode(getTasksByDate($db, new DateTime(), new DateTime("-2 weeks"))->json);
    if (is_array($test->error)) {
        echo "GetTasksByDate fråndatum > tilldatum OK \n";
    } else {
        echo "GetTasksByDate fråndatum > tilldatum , förväntade: array \nfick \n";
        print_r($test);
    }
}

function testGetTask(): void {
    $db = makeTestDb();
    $test = json_decode(getTask($db, 2)->json);
    $expect = new stdClass();
    $expect->id = 2;
    $expect->activity = "Slappat";
    if (($test->id === $expect->id) && ($test->activity === $expect->activity)) {
        echo "GetTask OK \n";
    } else {
        echo "GetTask #2 misslyckades förväntade \n\t" . json_encode($expect) . "\nfick \n\t" . json_encode($test) . "\n";
    }

    $test = json_decode(getTask($db, 0)->json);
    if (isset($test->error) && is_array($test->error)) {
        echo "GetTask (post saknas) OK \n";
    } else {
        echo "GetTask (post saknas) misslyckades förväntade array fick \n\t" . json_encode($test) . "\n";
    }
}

function testDeleteTask(): void {
    $db = makeTestDB();
    $test = json_decode(deleteTask($db, 30)->json);
    $expect = new stdClass();
    $expect->result = true;
    $expect->message = ["Radera post 30 lyckades", "1 poster raderad"];
    if (isset($test->result) && $test->result === $expect->result) {
        echo "DeleteTask OK\n";
    } else {
        echo "DeleteTask misslyckades, förväntade\n\t" . json_encode($expect) . "\nfick\n\t" . json_encode($test) . "\n";
    }
    $test = json_decode(getTask($db, 30)->json);
    if (isset($test->error) && is_array($test->error)) {
        echo "Hämta raderad task efter delete OK\n";
    } else {
        echo "Hämta raderad task efter delete misslyckades,förväntade array \nfick \n\t" . json_encode($test) . "\n";
    }


    $test = json_decode(deleteTask($db, 1)->json);
    $expect = new stdClass();
    $expect->result = false;
    $expect->message = ["Radera post 1 misslyckades", "0 poster raderade"];
    if (isset($test->result) && $test->result === $expect->result) {
        echo "DeleteTask (post saknas) OK\n";
    } else {
        echo "DeleteTask (post saknas) misslyckades, förväntade\n\t" . json_encode($expect) . "\nfick\n\t" . json_encode($test) . "\n";
    }
}

function testAddTask(): void {
    $db = makeTestDB();
    $postData = ["activityId" => 1, "time" => "01:30", "date" => date("Y-m-d", strtotime("last week")), "description" => "Fritext "];
    $test = json_decode(addTask($db, $postData)->json);
    $expect = new stdClass();
    $expect->id = 56;
    if (isset($test->id) && $test->id === $expect->id) {
        echo "AddTask OK\n";
    } else {
        echo "AddTask misslyckades, förväntade:\n\t" . json_encode($expect) . "\n fick:\n\t" . json_encode($test) . "\n";
    }

    $test = json_decode(getTask($db, $expect->id)->json);
    if (isset($test->id) && $test->id === $expect->id) {
        echo "Hämta task efter add OK\n";
    } else {
        echo "Hämta task efter add misslyckades, fick \n\t" . json_decode($test) . "\n";
    }

    $postData = ["activityId" => 1, "time" => "01:30", "date" => "fel", "description" => "Fritext "];
    $test = json_decode(addTask($db, $postData)->json);
    if (is_array($test->error)) {
        echo "AddTask felaktigt datum OK\n";
    } else {
        echo "AddTask felaktigt datum misslyckades, förväntade: array\n fick:\n\t" . json_encode($test) . "\n";
    }
}

function testUpdateTask(): void {
    $db = makeTestDB();
    $record = json_decode('{"id": 30,"activityId": 8,"time": "00:45","date":"' . date("Y-m-d", strtotime("yesterday")) . '" ,
        "description": "Fritext "}');
    $postData = ["activityId" => 8, "time" => "01:30", "date" => date("Y-m-d", strtotime("yesterday")), "description" => "Fritext "];
    $test = json_decode(updateTask($db, $postData, $record->id)->json);
    $expect = new stdClass();
    $expect->result = true;
    $expect->message = ["Uppdatera post {$record->id} lyckades", "1 poster uppdaterades"];
    if (isset($test->result) && $test->result === $expect->result) {
        echo "UpdateTask OK\n";
    } else {
        echo "UpdateTask misslyckades, förväntade\n\t" . json_encode($expect) . "\nfick\n\t" . json_encode($test) . "\n";
    }
    $test = json_decode(getTask($db, $record->id)->json);

    if (isset($test->activityId) && $test->activityId === $record->activityId && isset($test->date) && $test->date = $record->date) {
        echo "Hämta uppdaterad task OK\n";
    } else {
        echo "Hämta uppdaterad task misslyckades,förväntade " . json_encode($record) . "\nfick \n\t" . json_encode($test) . "\n";
    }

    $postData = ["activityId" => 8, "time" => "01:30", "date" => date("Y-m-d", strtotime("yesterday")), "description" => "Fritext "];
    $record->id = 1;
    $test = json_decode(updateTask($db, $postData, $record->id)->json);
    $expect->result = false;
    $expect->message = ["Uppdatera post {$record->id} misslyckades", "0 poster uppdaterades"];
    if (isset($test->result) && $test->result === $expect->result) {
        echo "UpdateTask OK\n";
    } else {
        echo "UpdateTask misslyckades, förväntade\n\t" . json_encode($expect) . "\nfick\n\t" . json_encode($test) . "\n";
    }

    $postData = ["activityId" => 8, "time" => "01:30", "date" => "Fel", "description" => "Fritext "];
    $test = json_decode(updateTask($db, $postData, $record->id)->json);
    if (isset($test->error) && is_array($test->error)) {
        echo "UpdateTask (felaktiga indata) OK\n";
    } else {
        echo "UpdateTask (felaktig indata) misslyckades, förväntade array \nfick\n\t" . json_encode($test) . "\n";
    }
}

function makeTestDB(): stdClass {
    $actData = file_get_contents(__DIR__ . "/../functions/activities.json");
    $taskData = file_get_contents(__DIR__ . "/../functions/tasks.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    $db->tasks = json_decode($taskData);
    return $db;
}
