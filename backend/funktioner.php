<?php

declare (strict_types=1);

function kopplaDB() {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_ALL); // Set MySQLi to throw exceptions 

    try {
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPassword = '';
        $db= new PDO("mysql:host=$dbHost;dbname=tidapp;charset=UTF8", $dbUser, $dbPassword);
        return $db;
    } catch (Exception $e) {
        $fel = $e->getMessage();
        $error = new stdClass();
        $error->error[] = "Något gick fel vid databaskoppling";
        $error->error[] = $fel;
        skickaJSON($error, 500);
    }
}

function skickaJSON(stdClass $obj, int $status = 200): string {
    $statusText = getStatusMeddelande($status);
    header("$statusText;Content-type:application/json;charset=utf-8");
    $json = json_encode($obj, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
    echo $json;
    exit;
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

function kopplaTestDB() {
    $db= new PDO('sqlite:../databas/test.db');
    $db->query('PRAGMA foreign_keys = ON;');

    return $db;
}

/**
 * Funktion för att konvertera en tidssträng (i formatet [h]h:mm) till ett heltal som anger antalet minuter
 * @param string $tid (format [h]h:mm, t.ex. 1:10)
 * @return int antal minuter t.ex. 1:10 -> 70
 */
function tidStrangTillMinuter(string $tid):int {
    $aTid= explode(":",$tid);
    if (count($aTid)!=2 || filter_var($aTid, FILTER_VALIDATE_INT)) {
        throw new InvalidArgumentException('Tid ska anges på formen [h]h:mm');
    }
    
    $timmar=(int) $aTid[0];
    $minuter=(int) $aTid[1];
    
    return (60*$timmar+$minuter);
        
}

/**
 * Funktion för att konvertera ett antal minuter till en tidssträng med formatet [h]h:mm
 * @param int $minuter antal minuter att konvertera
 * @return string tidssträng i formatet [h]h:mm
 */
function minuterTillTid(int $minuter):string {
    $timmar=intdiv($minuter , 60);
    $rest=$minuter % 60;
    
    $tid= sprintf('%d:%02d', $timmar, $rest);
    return $tid;
}