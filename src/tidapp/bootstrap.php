<?php

declare (strict_types=1);
session_start();

define('ROOT_DIR', __DIR__ . "/");
/* $serverPath=substr($_SERVER['SCRIPT_NAME'],0,-9);
  define('SERVER_PATH', substr($serverPath,0,-1));

 */
require_once ROOT_DIR . 'routing/routing.php';
require_once ROOT_DIR . 'activity/activities.php';
require_once ROOT_DIR . 'task/tasks.php';
require_once ROOT_DIR . '/functions/functions.php';

$querystring = filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW);
if ($_SERVER["REQUEST_METHOD"]==="POST") {
    $postData = $_POST;
} else {
    $postData = [];
}
$route = getRoute($querystring, $_SERVER["REQUEST_METHOD"]);
//var_dump($route);exit;
$db = getDatabase();
switch ($route->route) {
    case '/activity/':
        switch ($route->method) {
            case REQUEST_GET:
                if (count($route->params) === 1) {
                    $out = getActivity($db, (int) $route->params[0]);
                } else {
                    $out = getActivities($db);
                    resetDatabase();
                }
                break;
            case REQUEST_POST:
                $out = addActivity($db, $postData);
                break;
            case REQUEST_PUT:
                $out = updateActivity($db, $postData, (int) $route->params[0]);
                break;
            case REQUEST_DELETE:
                $out = deleteActivity($db, (int) $route->params[0]);
                break;
        }
        break;
    case "/tasklist/":
        if (count($route->params) === 1) {
            $out = getTasksByPage($db, (int) $route->params[0]);
            resetDatabase();
            break;
        } else {
            $out = getTasksByDate($db, new DateTime($route->params[0]), new DateTime( $route->params[1]));
            resetDatabase();
            break;
        }
        break;
    case "/task/":
        switch ($route->method) {
            case REQUEST_GET:
                $out= getTask($db, (int)$route->params[0]);
                break;
            case REQUEST_POST:
                $out= addTask($db, $_POST);
                break;
            case REQUEST_PUT:
                $out= updateTask($db, $_POST,(int) $route->params[0]);
                break;
            case REQUEST_DELETE:
                $out= deleteTask($db,(int) $route->params[0]);
                break;
        }
        break;
    case "/compilation/":
        $out = getCompilation($db, new DateTime($route->params[0]), new DateTime( $route->params[1]));
        break;
    default:
        include ROOT_DIR . 'info/info.html';
//        print_r($route);
        exit;
}
header("{$out->statusText};Content-type:application/json;charset=utf-8");
echo $out->json;
