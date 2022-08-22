<?php

declare (strict_types=1);
require_once __DIR__ . '/../../src/tidapp/task/tasks.php';

testGetTasksByPage();


function testGetTasksByPage(): void {
    $db = makeTestDb();
    $test = json_decode(getTasks($db, 1)->json);
    $antalPoster=5;
    $antalSidor=3;
    if ($test->pages === $antalSidor && count($test->tasks)===$antalPoster) {
        echo "GetTasks sida 1 OK \n";
    } else {
        echo "GetTasks sida 1, förväntade: \n\t sidor:$antalSidor \n\t Antal poster:$antalPoster \nfick \n\t Antal sidor: $test->pages \n\t Antal poster: " . count($test->tasks) ." \n";
    }

    $test = json_decode(getTasks($db, 3)->json);
    $antalPoster=2;
    $antalSidor=3;
    if ($test->pages === $antalSidor && count($test->tasks)===$antalPoster) {
        echo "GetTasks sida 3 OK \n";
    } else {
        echo "GetTasks sida 3, förväntade: \n\t sidor:$antalSidor \n\t Antal poster:$antalPoster \nfick \n\t Antal sidor: $test->pages \n\t Antal poster: " . count($test->tasks) ." \n";
    }

    $test = json_decode(getTasks($db, 5)->json);
    if (is_array($test->error)) {
        echo "GetTasks sida 5 OK \n";
    } else {
        echo "GetTasks sida 5, förväntade: array \nfick \n";
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
