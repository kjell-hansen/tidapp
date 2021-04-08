<?php

declare (strict_types=1);

function kopplaDB() {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_ALL); // Set MySQLi to throw exceptions 

    try {
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPassword = '';
        $db = mysqli_connect($dbHost, $dbUser, $dbPassword, "tidsredovisning");
        $db->set_charset("utf8");
        return $db;
    } catch (Exception $e) {
        $fel = $e->getMessage();
        $error = new stdClass();
        $error->error[] = "Något gick fel vid databaskoppling";
        $error->error[] = $fel;
        skickaJSON($error, 500);
    }
}

function skickaJSON(stdClass $obj, int $status = 200): void {
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