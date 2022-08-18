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

function skickaJSON(stdClass $obj, int $status = 200): string {
    $statusText = getStatusMeddelande($status);
    $json = json_encode($obj, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
    return $json;
}

function getStatusMeddelande(int $status): string {
    switch ($status) {
        case 200:
            return "HTTP/1.1 200 OK";
        case 400:
            return "HTTP/1.1 400 Bad request";
        case 401:
            return "HTTP/1.1 401 Unauthorized";
        case 403:
            return "HTTP/1.1 403 Forbidden";
        case 405:
            return "HTTP/1.1 405 Method not allowed";
        case 500:
            return "HTTP/1.1 500 Internal Server Error";
        default:
            throw new Exception("Okänt felnummer ($status)");
    }
}