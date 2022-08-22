<?php

declare (strict_types=1);
define('ROOT_DIR', __DIR__ . "/");
/* $serverPath=substr($_SERVER['SCRIPT_NAME'],0,-9);
  define('SERVER_PATH', substr($serverPath,0,-1));

 */
require_once ROOT_DIR . 'routing/routing.php';
require_once ROOT_DIR . 'activity/activities.php';
require_once ROOT_DIR . 'task/tasks.php';
require_once ROOT_DIR . '/functions/functions.php';

$querystring = filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW);
if (isset($_POST)) {
    $postData = filter_var($_POST, FILTER_UNSAFE_RAW);
} else {
    $postData = [];
}
$route = getRoute($querystring);

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
                $out = updateActivity($db, (int) $route->params[0]);
                break;
        }
        break;
    case "/tasklist/":
        if (count($route->params) === 1) {
            $out = getTasks($db, (int) $route->params[0]);
            resetDatabase();
            break;
        } else {
            echo "Hämta uppgifter mellan {$route->params[0]} och  {$route->params[1]}";
            exit;
        }
        break;
    case "/task/":
        switch ($route->method) {
            case REQUEST_GET:
                echo "Hämta uppgiftnr:" . $route->params[0];
                exit;
                break;
            case REQUEST_POST:
                echo "Spara NY aktivitet";
                exit;
                break;
            case REQUEST_PUT:
                echo "Uppdatera aktivitetnr:" . $route->params[0];
                exit;
                break;
            case REQUEST_DELETE:
                echo "Radera aktivitetnr:" . $route->params[0];
                exit;
                break;
        }
        break;
    case "/compilation/":
        echo "Hämta sammanställning mellan {$route->params[0]} och  {$route->params[1]}";
        exit;
        break;
    default:
        echo "Ogiltigt anrop";
        exit;
}
header("{$out->statusText};Content-type:application/json;charset=utf-8");
echo $out->json;
