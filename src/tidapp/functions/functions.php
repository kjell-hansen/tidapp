<?php

declare (strict_types=1);

function refreshDatabase():stdClass {
    $retur=new stdClass();
    $actData= file_get_contents(__DIR__ . '/../data/activities.json');
    $taskData=file_get_contents  (__DIR__ . '/../data/tasks.json');
    
    $retur->activities= json_decode($actData);
    $retur->tasks= json_decode($taskData);
    
    setDatabase($retur);
    
    return $retur;
    
}

function setDatabase(stdClass $db):void {
    $_SESSION["db"]= json_encode($db);
}

function getDatabase(): stdClass {
    if (isset($_SESSION["db"])) {
        return json_decode($_SESSION["db"]);
    } else {
        return refreshDatabase();        
    }
}
