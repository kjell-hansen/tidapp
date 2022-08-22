<?php

declare (strict_types=1);
require_once __DIR__ . '/../../src/tidapp/task/tasks.php';

testGetTasksByPage();
testGetTasksByDate();

function testGetTasksByPage(): void {
    $db = makeTestDb();
    $test = json_decode(getTasksByPage($db, 1)->json);
    $antalPoster=5;
    $antalSidor=3;
    if ($test->pages === $antalSidor && count($test->tasks)===$antalPoster) {
        echo "GetTasksByPage sida 1 OK \n";
    } else {
        echo "GetTasksByPage sida 1, förväntade: \n\t sidor:$antalSidor \n\t Antal poster:$antalPoster \nfick \n\t Antal sidor: $test->pages \n\t Antal poster: " . count($test->tasks) ." \n";
    }

    $test = json_decode(getTasksByPage($db, 3)->json);
    $antalPoster=2;
    $antalSidor=3;
    if ($test->pages === $antalSidor && count($test->tasks)===$antalPoster) {
        echo "GetTasksByPage sida 3 OK \n";
    } else {
        echo "GetTasksByPage sida 3, förväntade: \n\t sidor:$antalSidor \n\t Antal poster:$antalPoster \nfick \n\t Antal sidor: $test->pages \n\t Antal poster: " . count($test->tasks) ." \n";
    }

    $test = json_decode(getTasksByPage($db, 5)->json);
    if (is_array($test->error)) {
        echo "GetTasksByPage sida 5 OK \n";
    } else {
        echo "GetTasksByPage sida 5, förväntade: array \nfick \n";
        print_r($test);
    }
}

function testGetTasksByDate():void {
    $db = makeTestDb();
    $test = json_decode(getTasksByDate($db, new DateTime("-51 days"), new DateTime("-39 days"))->json);
    $antalPoster=4;

    if (count($test->tasks)===$antalPoster) {
        echo "GetTasksByDate OK \n";
    } else {
        echo "GetTasksByDate förväntade: $antalPoster \nfick " . count($test->tasks) . "\n";
        print_r($test);
    }

    $test = json_decode(getTasksByDate($db, new DateTime("-1 days"), new DateTime("-1 days"))->json);
    $antalPoster=0;

    if (count($test->tasks)===$antalPoster) {
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

function makeTestDB(): stdClass {
    $actData = file_get_contents(__DIR__ . "/../functions/activities.json");
    $taskData = file_get_contents(__DIR__ . "/../functions/tasks.json");
    $db = new stdClass();
    $db->activities = json_decode($actData);
    $db->tasks = json_decode($taskData);
    return $db;
}
