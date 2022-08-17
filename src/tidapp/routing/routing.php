<?php

declare (strict_types=1);
const REQUEST_GET = 0;
const REQUEST_POST = 1;
const REQUEST_PUT = 2;
const REQUEST_DELETE = 3;

function getRoute(string $querystring, string $method = "GET"): stdClass {
    $retur = new stdClass();
    if (substr($querystring, -1) === "/") {
        $querystring = substr($querystring, 0, -1);
    }
    $uri = explode("/", $querystring);
    switch (count($uri)) {
        case 0:
        case 1:
        case 2:
            $route = "";
            break;
        case 3:
            $route = $uri[2];
            break;
        default :
            $route = $uri[2];
            $params = array_slice($uri, 3);
    }

    $retur->route = $route==="" ? "/" : "/{$route}/";
    $retur->params = $params ?? [];

    if ($method === "POST") {
        $retur->method = REQUEST_POST;
        if (isset($_POST["action"]) && $_POST["action"] === "delete") {
            $retur->method = REQUEST_DELETE;
        } elseif (isset($_POST["action"]) && $_POST["action"] === "save" && count($params) > 0) {
            $retur->method = REQUEST_PUT;
        }
    } else {
        $retur->method = REQUEST_GET;
    }

    return $retur;
}
